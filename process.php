<?php
error_reporting(0);
include('connect.php');
$connect = mysqli_connect(constant("hostname"),constant("username"),constant("password"),constant("database"))
	or die('Could not connect: ' . mysqli_error($mysqli));

$errors         = array();  	// array to hold validation errors
$data 			= array(); 		// array to pass back data

#$errors['tags']='Test error for tags';
// return a response ===========================================================


$json=json_decode(file_get_contents('php://input'), true);
if(!isset($json['id'])){
	header("HTTP/1.1 500 Internal Server Error");
	die;
}
/*
ob_start(); //Start buffering
print_r($params); //print the result
$output = ob_get_contents(); //get the result from buffer
ob_end_clean(); //close buffer
 
$h = fopen('test/log.html', 'w'); //open a file
fwrite($h, $output); //write the output text
fclose($h); //close file

die;

 */





// if there are any errors in our errors array, return a success boolean of false
	if ( ! empty($errors)) {

		// if there are items in our errors array, return those errors
		$data['success'] = false;
        $data['errors']  = $errors;

    } else {
        $id=1*$json['id'];
        $name=$json['name'];
        $desc=$json['desc'];
        $meta=$json['meta'];
        if(empty($name)){$name=null;}
        if(empty($desc)){$desc=null;}
        if(empty($meta)){$meta=null;}
        #if(empty($name)){$name=null;}

/*        }else{
            $id=0;
            $name="debug";
            $desc="debug";
            $meta=null;
            $tags=null;
		}
 */
        // if there are no errors process our form, then return a message
        
        #$query="UPDATE `scanner`.`` SET `location` = 'location', `name` = 'myname', `description` = 'mytest_desc', `meta` = 'meta', `star` = '2' WHERE ``.`ID` = '{$id}';";
        #$sql_orig="UPDATE `scanner`.`` SET `name` = '{$name}', `description` = '{$desc}', `meta` = '{$tags}' WHERE ``.`ID` = '{$id}';";
        $sql="UPDATE `file` SET `name` = ?, `description` = ?, `meta` = ? WHERE `file`.`id` = ?;";
        $stmt = mysqli_prepare($connect,$sql);
        $stmt->bind_param('ssss', $name,$desc,$meta,$id);
		if (mysqli_execute($stmt)) {
			# Success!
			$data['success'] = true;
            $data['message'] = '';
            $data['id']=$id;
        }else {
            $errors['sql'] = "SQL Error! " . mysqli_error($connect);
            $data['success'] = false;
            $data['errors']  = $errors;
            $data['message'] = 'Error!';
            $data['id']=$id;
#	echo json_encode($data);
#    die;        

        }
        $sql=$connect->real_escape_string($sql);
/*        #$querylog="INSERT INTO `scanner`.`gopro.log` (`goproid`,`query`) VALUES ('{$id}','$sql_orig');";
        if (mysqli_query($connect, $querylog)) {
            # echo "New record created successfully";
        } else {
            $data['errors'] = "SQL Error: " . mysqli_error($connect);
            #    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
            
#            $query=print_r($_POST,TRUE);
            file_put_contents("/tmp/log",$querylog,FILE_APPEND);
 */
	}

	// return all our data to an AJAX call
	echo json_encode($data);
