<?PHP

/*

UPDATE `file` t1 INNER JOIN `old` t2 ON t1.`md5`=t2.`md5` SET
t1.`location`=t2.`location`, t1.`name`=t2.`name`,
t1.`aspect`=t2.`aspect`, t1.`description`=t2.`description`,
t1.`dt`=t2.`dt`,
t1.`meta`=t2.`meta`,
t1.`duration`=t2.`duration`,
t1.`star`=t2.`star`, 
t1.`htags`=t2.`htags`,
t1.`htagQty`=t2.`htagQty`,
t1.`old_id`=t2.`ID`,
t1.`fps`=t2.`fps`
where t1.`md5`=t2.`md5`


 */

include('../connect.php');

require_once 'Zend/Media/Iso14496.php'; 


$mysqli = mysqli_connect(constant("hostname"),constant("username"),constant("password"),constant("database"))
	or die('Could not connect: ' . mysqli_error($mysqli));

#command line arguments
#$_SERVER["argv"]

$sql="SELECT * FROM `path` ORDER BY `path`.`id` ASC";
$result = mysqli_query($mysqli,$sql) or die("SQL Error 1: " . mysqli_error($mysqli));
while($r = mysqli_fetch_array($result, MYSQL_ASSOC)) {
    $paths[$r['id']]=$r['path'];
}

$seq=$set=null;
$dt=$pathid=$aspect=$set=$seq=$width=$height=0;
$file=$md5=$exposure=$width=$height="";

#$stmt_photo = $mysqli->prepare("INSERT INTO ``.`file` (`filename`, `path`, `dt`, `md5`,`aspect`, `set`, `seq`, `exposure`, `width`, `height`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
#$stmt_photo->bind_param($file,$pathid,$dt,$md5,$aspect,$set,$seq,$exposure,$width,$height);
#print $mysqli->error;
#die;
#$stmt = $mysqli->prepare("INSERT INTO ``.`file` (`filename`, `path`, `dt`, `md5`, `location`, `name`, `description`, `meta`, `star`, `duration`, `htagQty`, `htags`, `fps`, `aspect`,`width`, `height`, `old_id`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);");

foreach($paths as $pathid => $path){
    foreach(scandir($path) as $file) {
        $a=explode('.',$file);
        if(isset($a[1]) and (strtolower($a[1])=="jpg")) {	# File is a GoPro Photo
            if(preg_match('/GOPR(?<single>\d{4})|G(?<group>\d{3})(?<file>\d{4})/i',$file,$m)){
                #$md5="md5";
                $exif=exif_read_data("{$path}/{$file}",$arrays=true);
                $width=$exif['COMPUTED']['Width'];
                $height=$exif['COMPUTED']['Height'];
                $aspect=round($width/$height,2);
                if(isset($m['group'])) {
                    $set=$m['group'];
                    $seq=$m['file'];
                }else{
                    $set=null;
                    $seq=$m['single'];
                }
                $sql="SELECT `id` FROM `file` WHERE `filename` = '{$file}' AND `path` = {$pathid}";
                $query = $mysqli->query($sql);
                if($query->num_rows==1) {
                    print "-- Matched file {$file}\n";
                }else {
                    $md5=gen_md5("{$path}/{$file}");
                    $sql="INSERT INTO `".constant("database")."`.`file` (`filename`, `path`, `dt`, `md5`,`aspect`, `set`, `seq`, `exposure`, `width`, `height`,`video`) VALUES ('{$file}', '{$pathid}', '{$exif['DateTime']}', '{$md5}','{$aspect}','{$set}','{$seq}','{$exif['ExposureTime']}','{$width}','{$height}',false)";
                    if($mysqli->query($sql)===TRUE) {
                        print "-- Added {$path}/{$file} as ID ".$mysqli->insert_id."\n";
                    }
                }
            }else{
                print "-- Unmatched: $file\n";
            }
        }elseif(isset($a[1]) and (strtolower($a[1])=="mp4")) {	# File is a GoPro Video
            $sql="SELECT `id` FROM `file` WHERE `filename` = '{$file}' AND `path` = {$pathid}";
                $query = $mysqli->query($sql);
                if($query->num_rows==1) {
                    print "-- Matched file {$file}\n";
                }elseif($query->num_rows>1) {
                    print "-- Too many matches for {$file}\n";
                }else {
/*                    $sql="SELECT `ID`,`md5` FROM `scanner`.`` WHERE `md5`='{$md5}' AND `path`='{$path}'";
                    unset($md5);
                    $query = $mysqli->query($sql) or die("SQL Error 1: " . $mysqli->error);
                    if($query->num_rows==1){
                        $existing=$query->fetch_array();
                        $md5=$existing['md5'];
                    }else{
 */                        $md5=gen_md5("{$path}/{$file}");
#`                    }
                    $sql="INSERT INTO `".constant("database")."`.`file` (`filename`, `path`,`md5`,`video`) VALUES ('${file}', '{$pathid}', '{$md5}',true)";
                    if($mysqli->query($sql)===TRUE) {
                        print "-- Added {$path}/{$file} as ID ".$mysqli->insert_id."\n";
                    }else {
                        print "-- Error: ".$mysqli->insert_id."\n";
                        die;
                    }
                }
        }
    }
}

                /*
            $sql="SELECT `ID` FROM `scanner`.`` WHERE `md5`='{$md5}' AND `path`='{$path}'";
            $query = $mysqli->query($sql) or die("SQL Error 1: " . $mysqli->error);
            if($query->num_rows==1){
                $existing=$query->fetch_array();
                #                print_r($res);
                $md5=$existing['md5'];
                $dt=$existing['dt'];

            }else{
                print "New\n";
                $md5=gen_md5("{$path}/{$file}");                
                $sql="SELECT * FROM `scanner`.`` WHERE `md5` = '{$md5}'";
                $query = $mysqli->query($sql) or die("SQL Error 1: " . $mysqli->error);
                if($query->num_rows==1){
                    print "Oh Wait\n";
                    $existing=$query->fetch_array();
                    $sql="INSERT INTO ``.`file` (`filename`, `path`,`md5`,`old_id`,`dt`,`duration`) VALUES ('${file}', '{$pathid}', '{$existing['md5']}','{$existing['ID']}','{$existing['dt']}','{$existing['duration']}')";
                    if($mysqli->query($sql)===TRUE) {
                        print "-- Added {$path}/{$file} as ID ".$mysqli->insert_id."\n";
                    }
                }
            }
        }
}
die;
/*{if(0){
            $exif=exif_read_data("{$dir}/{$file}",$arrays=true);
            $aspect=round($exif['COMPUTED']['Width']/$exif['COMPUTED']['Height'],2);
            if(isset($m['group'])) {
                $group=$m['group'];
                $seq=$m['file'];
            }else{
                $group=null;
                $seq=$m['single'];
            }
            $sql="INSERT INTO `scanner`.`gophoto` (`set`,`seq`,`filename`, `dt`,`path`, `md5`, `exposure`, `aspect`, `width`, `height`) VALUES ('{$group}','{$seq}','{$file}', '{$exif['DateTime']}', '{$dir}', '{$md5}', '{$exif['ExposureTime']}','{$aspect}','{$exif['COMPUTED']['Width']}','{$exif['COMPUTED']['Width']}');";
            print "$sql\n";
        }else{
            print "-- Unmatched: $file\n";
        }
    }
    continue;
    if(isset($a[1]) and (strtolower($a[1])=="mp4")) {	# File is a GoPro Video

		$htagQty=0;
        unset($htags);
        unset($hmmt);
        try {
            $isom = new Zend_Media_Iso14496("{$dir}/{$file}");
            $hmmt = $isom->moov->udta->HMMT;
        } catch (Exception $e) {
            echo "-- {$file}: Caught exception: ",  $e->getMessage(), "\n";
        }
    

		if (isset($hmmt)) {
			$reader = $hmmt->getReader();
			$reader->setOffset($hmmt->getOffset());

			$reader->readHHex(4);//skip some bytes
			$reader->readHHex(4);//skip some bytes

			$n = $reader->readInt32BE(); //number of points
			$htagQty=$n;
			for ($i = 1; $i <= $n; $i++) {
				$htags[]=$reader->readInt32BE();
#				print_r($t); // marker in ms
#			        echo "\n";
			}
			if($n>0) {
				$htags=implode($htags,',');
			}else{
				$htags=null;
			}
		}

        $md5=gen_md5("{$dir}/{$file}"); // Generate MD5SUM
		$ffprobe=shell_exec("ffprobe -show_streams \"{$dir}/{$file}\" 2>&1");
        
        preg_match_all('/(?:display_aspect_ratio=)(?<aspect>.+)(?:$[\s\S]*duration=)(?<duration>.+)$(?:$[\s\S]*TAG:creation_time=(?<datetime>.+))/m',$ffprobe,$m);
        preg_match_all('/(?<fps>[0-9.]+) fps/m',$ffprobe,$f);
        #print_r($m);
        #print_r($f);
        #echo $ffprobe;
        #die;
        if(!isset($m['datetime'][0])){
            echo "-- {$file}: datetime field missing, probably not a GoPro video, skipping!!\n";
            continue;
        }
        $fps=$f['fps'][0];
        $aspect=$m['aspect'][0];
		$duration=$m['duration'][0];
		$datetime=$m['datetime'][0];
		$seconds=$duration;
		$hours = floor($seconds / 3600);
		$mins = floor($seconds / 60 % 60);
		$secs = floor($seconds % 60);
		$dur = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
        #print"$datetime\t$duration\t$aspect\n";
        if(!isset($htags)){$htags='';}
        $sql = "INSERT INTO `scanner`.`` (`filename`, `dt`, `path`, `duration`,`htagQty`,`htags`,`aspect`,`md5`,`fps`) VALUES ('$file', '$datetime', '$dir', '$dur', '$htagQty', '$htags','$aspect','$md5','$fps');";
        #$sql="UPDATE `scanner`.`` SET `md5` = '$md5' WHERE ``.`path` = '$dir' AND ``.`filename`='$file' AND  ``.`duration`='$dur' ;";
        #$sql.="UPDATE `scanner`.`` SET `fps` = '$fps' WHERE ``.`path` = '$dir' AND ``.`filename`='$file' AND  ``.`duration`='$dur' ;";

		print "$sql\n";


	}
}

 */
function gen_md5($file) {
    print "-- Generating MD5 ({$file})\n";
    if(file_exists($file)) {
        $md5=shell_exec("md5sum {$file}");
        list($md5,)=(split(" ",$md5,2));
    } else {
        $md5=null;
    }
    return $md5;
}

?>
