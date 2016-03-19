<?php

include('modal.php');

$title = $_POST["title"];
$description= $_POST["description"];
$contact   = $_POST["contact"];
$price = $_POST["price"];
$sp= $_POST["sp1"];
$category   = $_POST["category"];
$aptname= $_POST["aptname"];

$value  = array();

$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["uploaded_file"]["name"]);
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image

    $check = getimagesize($_FILES["uploaded_file"]["tmp_name"]);
    if($check !== false) {

//        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;

    } else {
  //      echo "File is not an image.";
        $value = array("File is not an image.");
        $uploadOk = 0;
    }

// Check if file already exists
if (file_exists($target_file)) {
  //  echo "Sorry, file already exists.";
     $value = array("Sorry, file already exists.");
    $uploadOk = 0;
}
// Check file size
if ($_FILES["uploaded_file"]["size"] > 5000000) {
  //  echo "Sorry, your file is too large.";
     $value = array("Sorry, your file is too large.");
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    //echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $value = array("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {

exit(json_encode($value));
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["uploaded_file"]["tmp_name"], $target_file)) {

      //  echo "The file ". basename( $_FILES["uploaded_file"]["name"]). " has been uploaded.";
        $value = array("File is Uploaded");
                $x = new DB();
                $ans = $x->get_classifieds($aptname, "Nupur", $title, $description, $category, $price, $contact, $sp);
                if($ans > 0)
                {
                    $x->update_classifieds($aptname, "Nupur", $title, $description, $category, $price, $target_file, $contact, $sp);
                }
                else
                {
                    $x->insert_buy_sell_classifieds($aptname, "Nupur", $title, $description, $category, $price, $target_file, $contact, $sp);
                }

        exit(json_encode($value));
    } else {
        //echo "Sorry, there was an error uploading your file.";
        $value = array("File is Not Uploaded");
        exit(json_encode($value));
    }
}
?>