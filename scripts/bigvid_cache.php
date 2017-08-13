<?PHP
// https://gopro.com/support/articles/hero3-and-hero3-file-naming-convention

// SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(`duration`)))as total,max(`dt`) as recent FROM `gopro` WHERE 1
// ffmpeg -i $file -vf "scale=640:-2"  -vcodec libx264 -crf 30 -y $id_low.mp4

// ffmpeg -f concat -safe 0 -i list -c copy output.mp4
// https://trac.ffmpeg.org/wiki/Concatenate
//
@include('../connect.php');
$mysqli = mysqli_connect(constant("hostname"),constant("username"),constant("password"),constant("database"))
	or die('Could not connect: ' . mysqli_error($mysqli));

$sql="SELECT `id` FROM `file` WHERE `parent` IS NOT NULL AND `children` IS NOT NULL ORDER BY `file`.`dt` DESC";
$result = mysqli_query($mysqli,$sql) or die("SQL Error 1: " . mysqli_error($mysqli));

while($set = mysqli_fetch_array($result, MYSQL_ASSOC)) {
    $id=$set['id'];
    print "Parent: {$id}\n";
    $sql="SELECT `path`.`path`,`filename`,`dt`,time_to_sec(`duration`) as duration,`file`.`id` as id,`parent`, `children`,`htagQty`, `htags` FROM `file` INNER JOIN `path` ON `file`.`path`=`path`.`id` WHERE `parent` = {$id} ORDER BY `file`.`dt` ASC";
    #print "$sql\n";
    $vidResult = $mysqli->query($sql);
    $fullDuration=0;
while($r = mysqli_fetch_array($vidResult, MYSQL_ASSOC)) {
    $fullDuration+=$r['duration'];
    #    print_r($r);
    print "file '{$r['path']}/{$r['filename']}'\n";
    if(isset($r['htags'])) {
        print "Htags: {$r['htags']}\n";
    }
}
print "Full Duration: {$fullDuration}\n\n";
}

#print print_r($Videos,true)."</pre>"
?>
