<?php

include('modal.php');

$action = $_POST["action"];
$head   = $_POST["head"];

$value  = array();

if($action =="send_notice")
	$target_dir = "images/noticeBoard/";
if($action =="insert_post_category_open_forum" || $action =="insert_general_open_forum")
	$target_dir = "images/forumactivities/";


$imageFileType = pathinfo(basename($_FILES["uploaded_file"]["name"]),PATHINFO_EXTENSION);		
$uniqFileName = uniqid (rand(), true);

$uniqFileName = $uniqFileName.".".$imageFileType;

$target_file = $target_dir .$uniqFileName;
$uploadOk = 1;

// Check if image file is a actual image or fake image

    $check = getimagesize($_FILES["uploaded_file"]["tmp_name"]);
    if($check !== false) {

        $uploadOk = 1;

    } else {
  
        $value = array("File is not an image.");
        $uploadOk = 0;
    }

// Check if file already exists
if (file_exists($target_file)) {
  
     $value = array("Sorry,file already exists.");
    $uploadOk = 0;
}
// Check file size
if ($_FILES["uploaded_file"]["size"] > 5000000) {
 
     $value = array("Sorry, your file is too large.");
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
   
    $value = array("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {

exit(json_encode($value));
// if everything is ok, try to upload file
} 
else {
    if (move_uploaded_file($_FILES["uploaded_file"]["tmp_name"], $target_file)) 
	{
		$value = array("File is Uploaded");
        $x = new DB();
		if($action =="send_notice")
			{
				$building_id= $_POST["building_id"];
				$user_id= $_POST["user_id"];
				$x->insert_notice($head, $uniqFileName, $building_id, $user_id);
			}
		if($action =="insert_post_category_open_forum")
			{
				$x->insert_open_discussion_explore($_POST["building_id"],$_POST["categorytype"], $_POST["user_id"], $_POST["text"],$_POST["lat"], $_POST["lang"],$uniqFileName);
			}
			
        if($action =="insert_general_open_forum")
			{
				$x->insert_open_discussion_general($_POST["building_id"],$_POST["user_id"], $_POST["text"],$_POST["lat"], $_POST["lang"],$uniqFileName);
			}
			
        exit(json_encode($value));
    } 
	else 
	{

        $value = array("File is Not Uploaded");
        
    }

     exit(json_encode($value));

}

?>	