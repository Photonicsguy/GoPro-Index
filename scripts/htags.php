#!/usr/bin/php
<?PHP
#command line arguments
$check=$_SERVER["argv"];
array_shift($check);    # first element is filename
$c=array_shift($check);

if(isset($c)) {
    if(!file_exists($c)){
        print "Not found: $c\n";
        continue;
    }
    if(is_file($c)) {
        $file=$c;
    }
}else {
    print "Specify a file\nhtags.php <video.mp4>\nThis script will show the htagQty and htags (in milliseconds)\n";
    die;
}
require_once 'Zend/Media/Iso14496.php'; 


    $htagQty=0;
    try {
        $isom = new Zend_Media_Iso14496($file);
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
			}
			if($n>0) {
				$htags=implode($htags,',');
			}else{
				$htags=null;
            }
            print "{$htagQty}\t$htags\n";
		}
