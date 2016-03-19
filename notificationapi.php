<?php

include('modal.php'); 
$value=array(); 
if (isset($_POST["action"]) ) 
{ 
  switch ($_POST["action"])
    {
      case "get_notifications_count":
        	$x = new DB();
                $value = $x->get_notifications_count($_POST["name"]); 
                break;
       case "get_notifications":
        	$x = new DB();
         $value = $x->get_notifications($_POST["name"]); 
                break;
        case "redirect_notifications":
        	$x = new DB();
                $value = $x->redirect_notifications($_POST["name"]); 
                break;
        case "read_notifications":
        	$x = new DB();
           $result = $x->read_notifications($_POST["notification_id"]); 
                if($result == 1)
                     $value=array("Success","Success"); 
                else
                     $value=array("Error","Error");
                break;
   }
   exit(json_encode($value));
} 
?>