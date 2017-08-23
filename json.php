<?PHP
if(!isset($_REQUEST["mode"])){
	#$_REQUEST["mode"]="fileList";
}
	/*
	$target_file="/var/www/GoPro/Cal/bootstrap-year-calendar-master/pinfo.html";
	ob_start();
    phpinfo();
    $info = ob_get_contents();
    ob_end_clean();
 
    $fp = fopen($target_file, "w+");
    fwrite($fp, $info);
	fclose($fp);
	header("HTTP/1.1 501 Internal Server Error");
	die;
}*/

@include('../connect.php');
$mysqli = mysqli_connect(constant("hostname"),constant("username"),constant("password"),constant("database"))
	or die('Could not connect: ' . mysqli_error($mysqli));


//
// Mode: getCal
//
if($_REQUEST["mode"]=="getCal") {
	$sql="SELECT UNIX_TIMESTAMP(MIN(dt))*1000 as MinDate,UNIX_TIMESTAMP(MAX(dt))*1000 as MaxDate FROM `file` WHERE ".constant("queryHelp");
	$result = mysqli_query($mysqli,$sql) or die("SQL Error 1: " . mysqli_error($mysqli));
	$json = mysqli_fetch_assoc($result);

	$sql = "SELECT ID as id,UNIX_TIMESTAMP(dt)*1000 as startDate,UNIX_TIMESTAMP(ADDTIME(dt,duration))*1000 as endDate, `name`, `description`, `meta`, `star`, TIME_TO_SEC(`duration`)*1000 as duration FROM `file` WHERE ".constant("queryHelp")." ORDER BY `dt` ASC";
//	$sql = "SELECT ID as id,UNIX_TIMESTAMP(dt)*1000 as startDate,UNIX_TIMESTAMP(ADDTIME(dt,duration))*1000 as endDate, IFNULL(`name`,DATE_FORMAT(`dt`,'%r')) as name, IFNULL(`description`,'') as description, `meta`, `star`, TIME_TO_SEC(`duration`)*1000 as duration FROM `file` WHERE ".constant("queryHelp")." ORDER BY `dt` ASC";
	$result = mysqli_query($mysqli,$sql) or die("SQL Error 1: " . mysqli_error($mysqli));

while($r = mysqli_fetch_assoc($result)):
$r["color"]="#4286f4";
if(($r["name"]=="") or ($r["description"]=="")) {
	$r["color"]="#f4ca41";
	}
	$j[]=$r;

endwhile;


$json["video"]=$j;
$json["MinDate"]=intval($json["MinDate"]);
$json["MaxDate"]=intval($json["MaxDate"]);


print json_encode($json);




//
// Mode: range
// 
} elseif($_REQUEST["mode"]=="range") {
	$sdate=$_REQUEST["startDate"]/1000;
	$edate=($_REQUEST["endDate"]/1000)+86400; // + one day to allow single range selections to match final day
	$sql="SELECT * FROM `file` WHERE UNIX_TIMESTAMP(`dt`) >= '{$sdate}' AND UNIX_TIMESTAMP(`dt`) < '{$edate}' AND ".constant("queryHelp"); // Yes it's intentional to have >= and <
	$result = mysqli_query($mysqli,$sql) or die("SQL Error 1: " . mysqli_error($mysqli));
	while($r = mysqli_fetch_assoc($result)) {
		$j[]=$r;
	}
	if(isset($j)){
		$json["status"]=0;
		$json["range"]=$j;
	} else {
		$json["status"]=1;
		$json["error"]="No Results found within range.";
	}
	$json["MinRange"]=$_REQUEST["startDate"];
	$json["MaxRange"]=$_REQUEST["endDate"];

	print json_encode($json);
} elseif($_REQUEST["mode"]=="fileList"){

	// Cache cache directory
    $cache_dir = array_diff(scandir(constant('fileCache')), array('..', '.'));

	// Load Paths (And verify they exist)
	$result=$mysqli->query("SELECT * FROM `path`");
	while ($r = $result->fetch_assoc()) {
	    $paths[$r['id']]=$r;
	    unset($paths[$r['id']]['id']);  // Don't need id defined twice
	    $paths[$r['id']]["exists"]=file_exists($r['path']);
	}



	$sql="SELECT `id`,`filename`, `path`, `dt`,UNIX_TIMESTAMP(`dt`) as unixdt, `md5`, `location`, `name`, `description`, `meta`, `star`,`duration`, `htagQty`, `htags`, `fps`, `aspect`, `set`, `seq`, `exposure`, `width`, `height` FROM `file` WHERE `video`=TRUE ORDER BY `ID` DESC";
	$result = mysqli_query($mysqli,$sql) or die("SQL Error 1: " . mysqli_error($mysqli));
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

if($paths[$r["path"]]["exists"]){
    if(file_exists($paths[$r["path"]]["path"]."/{$r['filename']}")) {
        $row["files"]["original"]=prettyfilesize($paths[$r["path"]]["path"]."/{$r['filename']}");    // File Exists
    } else {
        $row["error"]=true;     // Error, file missing
        $row["errorText"].="{$r['filename']} missing";
	}
}else {
    $row["error"]=true;  // Error, Path missing
    $row["errorText"].="Path missing";
}

	if(file_exists(constant("fileCache")."/".$r['id']."_high.mp4"))
        $row["files"]["high"]=prettyfilesize(constant("fileCache")."/".$r['id']."_high.mp4");
    else
        $row["files"]["high"]=null;
    if(file_exists(constant("fileCache")."/".$r['id']."_med.mp4"))
        $row["files"]["med"]=prettyfilesize(constant("fileCache")."/".$r['id']."_med.mp4");
    else
        $row["files"]["med"]=null;
    if(file_exists(constant("fileCache")."/".$r['id']."_low.mp4"))
		$row["files"]["low"]=prettyfilesize(constant("fileCache")."/".$r['id']."_low.mp4");
    else
        $row["files"]["low"]=null;
    if(file_exists(constant("fileCache")."/".$r['id']."_strip.jpg"))
        $row["files"]["strip"]=prettyfilesize(constant("fileCache")."/".$r['id']."_strip.jpg");
    else
        $row["files"]["strip"]=null;
    if(file_exists(constant("fileCache")."/".$r['id']."_thumb.jpg"))
        $row["files"]["thumb"]=prettyfilesize(constant("fileCache")."/".$r['id']."_thumb.jpg");
    else
        $row["files"]["thumb"]=null;

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


	
}


function prettyfilesize($file)  {
    $unit="K";
	$size=filesize($file)/1024;
	if($size>1000){
		$unit="M";
		$size=$size/1024;
	}
    if($size>1000){
        $size=$size/1024;
        $unit="G";
    }
    return sprintf("%.1f%s",$size,$unit);
}


?>
