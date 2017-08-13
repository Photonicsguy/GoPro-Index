<?PHP
// https://gopro.com/support/articles/hero3-and-hero3-file-naming-convention

// SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(`duration`)))as total,max(`dt`) as recent FROM `gopro` WHERE 1
// ffmpeg -i $file -vf "scale=640:-2"  -vcodec libx264 -crf 30 -y $id_low.mp4

@include('../connect.php');
$mysqli = mysqli_connect(constant("hostname"),constant("username"),constant("password"),constant("database"))
	or die('Could not connect: ' . mysqli_error($mysqli));


$sql="SELECT `ID`,`path`,`filename` FROM `file` WHERE `filename` LIKE 'GP0%' ORDER BY `dt` ASC";
$result = mysqli_query($mysqli,$sql) or die("SQL Error 1: " . mysqli_error($mysqli));

$solvedVideos=array();

while($r = mysqli_fetch_array($result, MYSQL_ASSOC)) {
    $errorText="";
    $class="success";

    
    preg_match('/(GP\d\d)(\d{4})/i',$r['filename'],$m);
    #$errorText="<pre>".print_r($m,true)."</pre>";

    // If a file from the list was used already, skip it to avoid duplication
    if(array_key_exists($r['ID'],$solvedVideos)) {
        continue;
    }

    //TODO Solve for possible duplicates
    //For now, checking for a delta of less than 30 seconds should verify no incorrect chapter files are included
    $sql="SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(`duration`)))as dur,SEC_TO_TIME(min(TIME_TO_SEC(`duration`))+TIMESTAMPDIFF(SECOND,min(`dt`),max(`dt`))) as diff,ABS(SUM(TIME_TO_SEC(`duration`))-(min(TIME_TO_SEC(`duration`))+TIMESTAMPDIFF(SECOND,min(`dt`),max(`dt`)))) as delta,MIN(`dt`) as dt, SUM(`htagQty`) as htagQty FROM `file` WHERE `filename` LIKE '%{$m[2]}.%'";
    $vidResult = $mysqli->query($sql);
    $meta= $vidResult->fetch_assoc();
    if($meta['delta']>10) {   // Delta appears to be about 2-3 seconds, so a threshold of 10 seconds should be OK
        $errorText.="Delta of {$meta['delta']}s is too large";
    }

    #$errorText="<pre>".print_r($meta,true)."</pre>";
    
    $sql="SELECT `ID`,`path`, `filename`, `dt` ,`duration`,`parent`,`children` FROM `file` WHERE `filename` LIKE '%{$m[2]}.%' and `video`= TRUE ORDER BY `dt` ASC";
    $vidResult = mysqli_query($mysqli,$sql) or die("SQL Error 1: " . mysqli_error($mysqli));
    $vid=array('dt'=>$meta['dt'],'duration'=>$meta['dur'],'class'=>'','error'=>$errorText,'htagQty'=>$meta['htagQty']);
    while($v = mysqli_fetch_array($vidResult, MYSQL_ASSOC)) {
        $solvedVideos[$v['ID']]=true;
        preg_match('/(GOPR|(GP)(\d\d))(\d{4})/i',$v['filename'],$m);
        if(strtoupper($m[1])==="GOPR") {
            $vid['files'][0]['filename']=$v['filename'];
            $vid['files'][0]['ID']=$v['ID'];
        }elseif(strtoupper($m[2])==="GP"){
            $vid['files'][intval($m[3])]['filename']=$v['filename'];
            $vid['files'][intval($m[3])]['ID']=$v['ID'];

        }
    }
    // Sort Array
    ksort($vid['files']);
    $Videos[$vid['files'][0]['filename']]=$vid;

}
#print "-- <PRE>\n";
foreach($Videos as $k =>$v){
    #$k=Main video
    $parent=$v['files'][0]['ID'];
    unset($children);
    print "-- Parent: {$parent}\n";
    foreach($v['files'] as $key=>$vid) {
        if($key==0) {
#            print "UPDATE `gopro`.`file` SET `parent` = '{$parent}' WHERE `file`.`id` = {$vid['ID']};\n";
        } else {
            print "UPDATE `gopro`.`file` SET `parent` = '{$parent}', `child` = TRUE WHERE `id` = {$vid['ID']};\n";
              
            $children[]=$vid['ID'];
        }
    }
    #unset($children[0]);
    $children=join(", ",$children);
        print "UPDATE `gopro`.`file` SET `parent` = '{$parent}', `children` = '{$children}' WHERE `id` = {$parent};\n";
print "\n";
}
#print print_r($Videos,true)."</pre>"
?>
