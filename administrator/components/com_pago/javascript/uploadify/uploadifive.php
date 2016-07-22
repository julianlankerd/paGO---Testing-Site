<?php defined( '_JEXEC' ) or die();
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
/*
UploadiFive
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
*/

// Set the uplaod directory
$uploadDir  = $_GET['folder'];
$JPATH_ROOT = $_GET['JPATH_ROOT'];
$imageType  = $_GET['imageType'];

// Set the allowed file extensions
$fileTypes = array('jpg', 'jpeg', 'gif', 'png'); // Allowed file extensions

$verifyToken = md5('unique_salt' . $_POST['timestamp']);

$path = $JPATH_ROOT .DIRECTORY_SEPARATOR. 'media' .DIRECTORY_SEPARATOR. 'pago' .DIRECTORY_SEPARATOR. $imageType;

//$uploadDir
if (!empty($_FILES)) {
	$tempFile   = $_FILES['Filedata']['tmp_name'];
	$filename = $_FILES['Filedata']['name'];
	//$targetFile = $path . DIRECTORY_SEPARATOR . $uploadDir . DIRECTORY_SEPARATOR . $_FILES['Filedata']['name'];

	//generate image name
	$ext = end(explode('.', $filename));
    $ext = substr(strrchr($filename, '.'), 1);
    $ext = substr($filename, strrpos($filename, '.') + 1);
    $ext = preg_replace('/^.*\.([^.]+)$/D', '$1', $filename);

	$arr = explode(' ',microtime());
	$newfilename = $arr[0] + $arr[1]+rand(1,1000);
	$newfilename =	str_replace('.','',$newfilename);
	$newfilename = $newfilename.".".$ext;
	///////
	
	$targetFile = $path . DIRECTORY_SEPARATOR . $uploadDir . DIRECTORY_SEPARATOR . $newfilename;

	if (!file_exists($path . DIRECTORY_SEPARATOR . $uploadDir)) {
    	mkdir($path . DIRECTORY_SEPARATOR . $uploadDir, 0755, true);
	}

	// Validate the filetype
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	if (in_array(strtolower($fileParts['extension']), $fileTypes)) {

		// Save the file
		move_uploaded_file($tempFile, $targetFile);
		echo 1;

	} else {

		// The file type wasn't allowed
		echo 'Invalid file type.';

	}
}
?>