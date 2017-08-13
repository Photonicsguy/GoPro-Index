<?PHP
@include('connect.php');
$skip=0;
$limit=3;

if(!isset($_SERVER["SCRIPT_URL"]) OR $_SERVER["SCRIPT_URL"]=="/GoPro/json.php") {
    $mode="list";
    $sql="SELECT `id`,`filename`, `path`, `dt`,1000*UNIX_TIMESTAMP(`dt`) as jsdt, UNIX_TIMESTAMP(`dt`) as unixdt,`location`, `name`, `description`, `meta`, `star`,TIME_TO_SEC(`duration`) as duration, `htagQty`, `htags`, `fps`, `aspect`, `set`, `seq`, `exposure`, `width`, `height`,`children` FROM `file` WHERE `video` = TRUE AND `child` IS false ORDER BY `dt` DESC LIMIT {$skip},{$limit}";

}else{
    $mode="file";
    $sql="SELECT `id`,`filename`, `path`, `dt`,UNIX_TIMESTAMP(`dt`) as unixdt, `md5`, `location`, `name`, `description`, `meta`, `star`,`duration`, `htagQty`, `htags`, `fps`, `aspect`, `set`, `seq`, `exposure`, `width`, `height` FROM `file` WHERE `video`=TRUE ORDER BY `ID` DESC";

}

$mysqli = mysqli_connect(constant("hostname"),constant("username"),constant("password"),constant("database"))
	or die('Could not connect: ' . mysqli_error($mysqli));

/*
# Search for Duplicate MD5SUMS
$sql="SELECT `md5`, COUNT(*) c FROM `file` GROUP BY `md5` HAVING c > 1";
$result = mysqli_query($mysqli,$sql) or die("SQL Error 1: " . mysqli_error($mysqli));
$dupMD5=array();
while($r = mysqli_fetch_array($result, MYSQL_ASSOC)) {
    $dupMD5[$r['md5']]=$r['c'];
}


 */


// Load Paths (And verify they exist)

$result=$mysqli->query("SELECT * FROM `path`");
while ($r = $result->fetch_assoc()) {
    $paths[$r['id']]=$r;
    unset($paths[$r['id']]['id']);  // Don't need id defined twice
    $paths[$r['id']]["exists"]=file_exists($r['path']);
}




$result = mysqli_query($mysqli,$sql) or die("SQL Error 1: " . mysqli_error($mysqli));

if($mode=="file"){
    $cache_dir = array_diff(scandir(constant('fileCache')), array('..', '.'));
}

while($r = mysqli_fetch_assoc($result)):
$row=array("error"=>false,"errorText"=>"","class"=>"success","files"=>array("original"=>false));
$row['id']=intval($r["id"]);  
$r["id"]=intval($r["id"]);
#$r["unixdt"]=intval($r["unixdt"]);
$r["htagQty"]=intval($r["htagQty"]);
//TODO
$r["htags"]=explode(',',$r["htags"]);
foreach($r["htags"] as $k=>$t)
    $r["htags"][$k]=intval($t);

$r["fps"]=floatval($r["fps"]);
$r["path"]=intval($r["path"]);
$row["unixdt"]=intval($r["unixdt"]);
$row["jsdt"]=intval($r["unixdt"]*1000);
$row["duration"]=intval($r["duration"]);
$row["sql"]=$r;

if($mode=="file"):
// Check paths to see if they exist or not
if($paths[$r["path"]]["exists"]){
    if(file_exists($paths[$r["path"]]["path"]."/{$r['filename']}")) {
        $row["files"]["original"]=prettyfilesize($paths[$r["path"]]["path"]."/{$r['filename']}");    // File Exists
    } else {
        $row["error"]=true;     // Error, file missing
        $row["errorText"].="{$r['filename']} missing";
    }
if(file_exists(constant("fileCache")."/".$r['id']."_high.mp4"))
        $row["files"]["high"]=prettyfilesize(constant("fileCache")."/".$r['id']."_high.mp4");
    else
        $row["files"]["high"]=0;
    if(file_exists(constant("fileCache")."/".$r['id']."_med.mp4"))
        $row["files"]["med"]=prettyfilesize(constant("fileCache")."/".$r['id']."_med.mp4");
    else
        $row["files"]["med"]=0;
    if(file_exists(constant("fileCache")."/".$r['id']."_low.mp4"))
        $row["files"]["low"]=prettyfilesize(constant("fileCache")."/".$r['id']."_low.mp4");
    else
        $row["files"]["low"]=0;
    if(file_exists(constant("fileCache")."/".$r['id']."_strip.jpg"))
        $row["files"]["strip"]=prettyfilesize(constant("fileCache")."/".$r['id']."_strip.jpg");
    else
        $row["files"]["strip"]=0;
    if(file_exists(constant("fileCache")."/".$r['id']."_thumb.jpg"))
        $row["files"]["thumb"]="exists";#prettyfilesize(constant("fileCache")."/".$r['id']."_thumb.jpg");
    else
        $row["files"]["thumb"]=null;


} else {
    $row["error"]=true;  // Error, Path missing
    $row["errorText"].="Path missing";
}
else:
    $path=$paths[$r["path"]]["path"];
#    $t=explode('GOPRO/',$path);
#    $path=$t[1];
    $row["files"]["original"]="{$path}/{$r["filename"]}";
    $row["files"]["high"]="cache/{$r["id"]}_high.mp4";
    $row["files"]["med"]="cache/{$r["id"]}_med.mp4";
    $row["files"]["low"]="cache/{$r["id"]}_low.mp4";
    $row["files"]["strip"]="cache/{$r["id"]}_strip.jpg";
    $row["files"]["thumb"]="cache/{$r["id"]}_thumb.jpg";


endif;

// TODO DEBUG an error
/*if($r["filename"]=="GOPR0949.MP4"){
    $row["error"]=true;
    $row["errorText"].="Test";
}
 */

$json[]=$row;
endwhile;


#var_dump($json);
$j["vid"]=$json;
#$j["paths"]=$paths;
#$j["header"]=array("field","text1");
print json_encode($j);
#print_r($j);





function prettyfilesize($file)  {
    $unit="M";
    $size=filesize($file)/1024/1024;
    if($size>1000){
        $size=$size/1024;
        $unit="G";
    }
    return sprintf("%.1f%s",$size,$unit);
}

?>
