
<?PHP
// ffmpeg -i $file -vf "scale=640:-2"  -vcodec libx264 -crf 30 -y $id_low.mp4

@include('../connect.php');
$mysqli = mysqli_connect(constant("hostname"),constant("username"),constant("password"),constant("database"))
	or die('Could not connect: ' . mysqli_error($mysqli));


# Search for Duplicate MD5SUMS
$sql="SELECT `md5`, COUNT(*) c FROM `file` GROUP BY `md5` HAVING c > 1 ORDER BY `file`.`dt` ASC";
$result = mysqli_query($mysqli,$sql) or die("SQL Error 1: " . mysqli_error($mysqli));
$dupMD5=array();
while($r = mysqli_fetch_array($result, MYSQL_ASSOC)) {
    $dupMD5[$r['md5']]=$r['c'];
}


// Load Paths (And verify they exist)

$result=$mysqli->query("SELECT * FROM `path`");
while ($r = $result->fetch_assoc()) {
    $paths[$r['id']]=$r;
    unset($paths[$r['id']]['id']);  // Don't need id defined twice
    $paths[$r['id']]["exists"]=file_exists($r['path']);
}

$sql="SELECT `ID`,`video`,`path`,`filename`,`md5` FROM `file` ORDER BY `id` DESC";
$result = mysqli_query($mysqli,$sql) or die("SQL Error 1: " . mysqli_error($mysqli));

$cache_dir = array_diff(scandir(constant('fileCache')), array('..', '.'));

$error="";	// Error Text
while($r = mysqli_fetch_array($result, MYSQL_ASSOC)) {
    if(!$r['video']){
        print "ID: {$r['ID']} is not a video ({$r['filename']})\n";
        continue;
    }
        print "ID: {$r['ID']} is a video ({$r['filename']})\n";

	$id=$r['ID'];
	$path=$paths[$r['path']]['path'];
	$file=$r['filename'];

	$exists=false;


        $exists=file_exists("{$r['path']}/{$r['filename']}");
    if(!$exists){   // If file dosen't exist, add a glyph and set danger class
		$error.="Warning: {$id} not found: {$path}/{$file}\n";
    }

    // Check for duplicates based on MD5
    if(array_key_exists($r['md5'],$dupMD5)){
        $error.="Warning: {$id} has a non-unique MD5SUM, skipped\n";
        print "\n";
        continue;
    }

    print "{$id}: ";
        #print "\n";
    #continue;

    // Check for high resolution video
    if(file_exists(fileCache."/{$id}_high.mp4")){
        print "{$id}_high.mp4 exists, ";
    }else {
        #print "{$id}_high.mp4 missing, creating... \n";
        $ffmpeg=shell_exec("ffmpeg -i \"{$path}/{$file}\" -vf \"scale=1080:-2\"  -vcodec libx264 -crf 30 -y \"".fileCache."/{$id}_high.mp4\"");
    }
       // Check for med resolution video
    if(file_exists(fileCache."/{$id}_med.mp4")){
        print "{$id}_med.mp4 exists, ";
    }else {
        #print "{$id}_med.mp4 missing, creating... \n";
        $ffmpeg=shell_exec("ffmpeg -i \"".fileCache."/{$id}_high.mp4\" -vf \"scale=720:-2\"  -vcodec libx264 -crf 30 -y \"".fileCache."/{$id}_med.mp4\"");
    }
    // Check for low resolution video
    if(file_exists(fileCache."/{$id}_low.mp4")){
        print "{$id}_low.mp4 exists, ";
    }else {
        #print "{$id}_low.mp4 missing, creating... \n";
        $ffmpeg=shell_exec("ffmpeg -i \"".fileCache."/{$id}_med.mp4\" -vf \"scale=640:-2\"  -vcodec libx264 -crf 30 -y \"".fileCache."/{$id}_low.mp4\"");
    }
    if(file_exists(fileCache."/{$id}_strip.jpg")){
        print "{$id}_strip.jpg exists, ";
    }else {
        print "{$id}_strip.jpg missing, creating... ";
        $strip=shell_exec("video_preview.sh \"".fileCache."/{$id}_med.mp4\" \"".fileCache."/{$id}_strip.jpg\"");
        print $strip;
    }
   if(file_exists(fileCache."/{$id}_thumb.jpg")){
        print "{$id}_thumb.jpg exists, ";
    }else {
        print "{$id}_thumb.jpg missing, creating... ";
        print shell_exec("thumb.sh \"".fileCache."/{$id}_strip.jpg\"");
    }

 
    print "\n";
    }
?>
