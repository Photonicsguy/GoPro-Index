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

@include('../connect.php');

require_once 'Zend/Media/Iso14496.php'; 


$mysqli = mysqli_connect(constant("hostname"),constant("username"),constant("password"),constant("database"))
	or die('Could not connect: ' . mysqli_error($mysqli));




$sql="SELECT * FROM `path` ORDER BY `path`.`id` ASC";
$result = mysqli_query($mysqli,$sql) or die("SQL Error 1: " . mysqli_error($mysqli));
while($r = mysqli_fetch_array($result, MYSQL_ASSOC)) {
    $paths[$r['id']]=$r['path'];
}

$seq=$set=null;
$dt=$pathid=$aspect=$set=$seq=$width=$height=0;
$file=$md5=$exposure=$width=$height="";

#$stmt_photo = $mysqli->prepare("INSERT INTO `".constant("database")."`.`file` (`filename`, `path`, `dt`, `md5`,`aspect`, `set`, `seq`, `exposure`, `width`, `height`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
#$stmt_photo->bind_param($file,$pathid,$dt,$md5,$aspect,$set,$seq,$exposure,$width,$height);
#print $mysqli->error;
#die;
#$stmt = $mysqli->prepare("INSERT INTO `".constant("database")."`.`file` (`filename`, `path`, `dt`, `md5`, `location`, `name`, `description`, `meta`, `star`, `duration`, `htagQty`, `htags`, `fps`, `aspect`,`width`, `height`, `old_id`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);");

$sql="SELECT * FROM `file` WHERE `dt` IS NULL or `width` IS NULL OR `duration` IS NULL ORDER BY `id` DESC";
#$sql="SELECT * FROM `file` WHERE `old_id` = 99999 OR `dt` IS NULL or `width` IS NULL ORDER BY `id` DESC";
$result = mysqli_query($mysqli,$sql) or die("SQL Error 1: " . mysqli_error($mysqli));
while($r = mysqli_fetch_array($result, MYSQL_ASSOC)) {
    $path=$paths[$r['path']];
    $file=$r['filename'];
     if($r['video']) {


         $htagQty=0;
        unset($htags);
        unset($hmmt);
        try {
            $isom = new Zend_Media_Iso14496("{$path}/{$file}");
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

		$ffprobe=shell_exec("ffprobe -show_streams \"{$path}/{$file}\" 2>&1");
        
        preg_match_all('/(?:display_aspect_ratio=)(?<aspect>.+)(?:$[\s\S]*duration=)(?<duration>.+)$(?:$[\s\S]*TAG:creation_time=(?<datetime>.+))/m',$ffprobe,$m);
        preg_match_all('/(?<fps>[0-9.]+) fps(?:[\s\S]*width=)(?<width>\d+)(?:[\s\S]+height=)(?<height>\d+)/m',$ffprobe,$f);
        
        #print_r($m);
        #print_r($f);
        #echo $ffprobe;
        if(!isset($m['datetime'][0])){
            echo "-- {$file}: datetime field missing, probably not a GoPro video, skipping!!\n";
            continue;
        }
        $fps=$f['fps'][0];
        $height=$f['height'][0];
        $width=$f['width'][0];
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
        #$sql = "INSERT INTO `scanner`.`` (`dt`,`duration`,`htagQty`,`htags`,`aspect`,`md5`,`fps`) VALUES ('$datetime', '$dur', '$htagQty', '$htags','$aspect','$md5','$fps');";
        #$sql="UPDATE `scanner`.`` SET `md5` = '$md5' WHERE ``.`path` = '$dir' AND ``.`filename`='$file' AND  ``.`duration`='$dur' ;";
        $sql="UPDATE `".constant("database")."`.`file` SET ";
        $sql.="`fps` = '$fps',";
        $sql.="`dt`='{$datetime}',";
        $sql.="`duration`='{$duration}',";
        $sql.="`htagQty`='{$htagQty}',";
        if($htags==null){ $sql.="`htags`=NULL,"; }else{ $sql.="`htags`='{$htags}',"; }
        $sql.="`width` = '$width',";
        $sql.="`height` = '$height',";
        $sql.="`aspect`='{$aspect}',";
        $sql.="`old_id`='99999'";
#        $sql.="`htagQty`='{$htagQty}',";

        $sql.=" WHERE `file`.`id` = '{$r['id']}';";

		print "$sql\n";
 
 
 
     } else {
            if(preg_match('/GOPR(?<single>\d{4})|G(?<group>\d{3})(?<file>\d{4})/i',$file,$m)){
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
                print_r($exif);
                $sql="INSERT INTO `".constant("database")."`.`file` (`filename`, `path`, `dt`, `md5`,`aspect`, `set`, `seq`, `exposure`, `width`, `height`,`video`) VALUES ('{$file}', '{$pathid}', '{$exif['DateTime']}', '{$md5}','{$aspect}','{$set}','{$seq}','{$exif['ExposureTime']}','{$width}','{$height}',false)";
                print $sql;
                    #if($mysqli->query($sql)===TRUE) {
                        #print "-- Added {$path}/{$file} as ID ".$mysqli->insert_id."\n";
                    #}
                }
    }
}


# Process keywords
#
$keys=array();
$sql='SELECT `meta`,count(`meta`) as count FROM `file` WHERE `meta` IS NOT NULL GROUP BY `meta`';
$result = mysqli_query($mysqli,$sql) or die("SQL Error 1: " . mysqli_error($mysqli));
while($r = mysqli_fetch_array($result, MYSQL_ASSOC)) {
    $k_array=(split(",",$r['meta']));
    foreach($k_array as $k){
        if(array_key_exists($k,$keys)) {
            $keys[$k]+=$r['count'];
        }else {
            $keys[$k]=$r['count'];
        }
    }
}
#print_r($keys);
print "-- Updating Meta keywords\n";
foreach($keys as $k=>$c) {
    $sql="INSERT INTO `".constant("database")."`.`words` (`keyword`, `count`) VALUES ('{$k}', '{$c}') ON DUPLICATE KEY UPDATE `count`='{$c}';";
#    print "$sql\n";
        if($mysqli->query($sql)===TRUE) {
#        print "-- Added {$k}:{$c} as ID ".$mysqli->insert_id."\n";
#        print "-- {$k}: {$c}\n";
        }
}

    die;






function gen_md5($file) {
    print "-- Generating MD5 ({$file})\n";
    if(file_exists($file)) {
        $md5=shell_exec("md5sum \"{$file}\"");
        list($md5,)=(split(" ",$md5,2));
    } else {
        $md5=null;
    }
    return $md5;
}

?>
