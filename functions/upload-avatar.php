<?php
session_start();

$filenameOut = __DIR__.'/../avatars/'.$_SESSION['user']['_id'];
if(isset($_FILES["avatar"]))
{
	$ret = array();
	
//	This is for custom errors;	
/*	$custom_error= array();
	$custom_error['jquery-upload-file-error']="File already exists";
	echo json_encode($custom_error);
	die();
*/
	$error =$_FILES["avatar"]["error"];
	//You need to handle  both cases
	//If Any browser does not support serializing of multiple files using FormData() 
	if(!is_array($_FILES["avatar"]["name"])) //single file
	{
            move_uploaded_file($_FILES["avatar"]["tmp_name"],$filenameOut);
            $ret[]= "avatar remplacé";
	}
        echo json_encode($ret);
 }
 ?>