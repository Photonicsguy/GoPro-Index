<?PHP
@include('../../connect.php');
$mysqli = mysqli_connect(constant("hostname"),constant("username"),constant("password"),constant("database"))
	or die('Could not connect: ' . mysqli_error($mysqli));

if(!isset($_REQUEST["mode"])){
#	header("HTTP/1.1 500 Internal Server Error");
#	die;
} elseif($_REQUEST["mode"]=="range") {
	$sdate=$_REQUEST["startDate"]/1000;
	$edate=($_REQUEST["endDate"]/1000)+86400; // + one day to allow single range selections to match final day
	$sql="SELECT * FROM `file` WHERE UNIX_TIMESTAMP(`dt`) >= '{$sdate}' AND UNIX_TIMESTAMP(`dt`) < '{$edate}' AND ".constant("queryHelp"); // Yes it's intentional to have >= and <
	$result = mysqli_query($mysqli,$sql) or die("SQL Error 1: " . mysqli_error($mysqli));
while($r = mysqli_fetch_assoc($result)):
	$j[]=$r;
endwhile;
if(isset($j)){
	$json["status"]=0;
	$json["range"]=$j;
}else{
	$json["status"]=1;
	$json["error"]="No Results found within range.";
}
$json["MinRange"]=$_REQUEST["startDate"];
$json["MaxRange"]=$_REQUEST["endDate"];
print json_encode($json);
	die;
}



$sql="SELECT UNIX_TIMESTAMP(MIN(dt))*1000 as MinDate,UNIX_TIMESTAMP(MAX(dt))*1000 as MaxDate FROM `file` WHERE ".constant("queryHelp");
$result = mysqli_query($mysqli,$sql) or die("SQL Error 1: " . mysqli_error($mysqli));
$json = mysqli_fetch_assoc($result);

$sql = "SELECT ID as id,UNIX_TIMESTAMP(dt)*1000 as startDate,UNIX_TIMESTAMP(ADDTIME(dt,duration))*1000 as endDate, IFNULL(`name`,DATE_FORMAT(`dt`,'%r')) as name, IFNULL(`description`,'') as description, `meta`, `star`, TIME_TO_SEC(`duration`)*1000 as duration FROM `file` WHERE ".constant("queryHelp")." ORDER BY `dt` ASC";
$result = mysqli_query($mysqli,$sql) or die("SQL Error 1: " . mysqli_error($mysqli));

while($r = mysqli_fetch_assoc($result)):
	$j[]=$r;
endwhile;


$json["video"]=$j;
$json["MinDate"]=intval($json["MinDate"]);
$json["MaxDate"]=intval($json["MaxDate"]);
#$json["MaxDate"]=;


#var_dump($json);
#$j["paths"]=$paths;
#$j["header"]=array("field","text1");
print json_encode($json);
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
