<?php

include('modal.php'); 
$value=array(); 
if (isset($_POST["action"]) ) 
{ 
  switch ($_POST["action"])
    {
      case "get_open_discussion":
        	$x = new DB();
                $value = $x->get_open_discussion($_POST["building_id"],$_POST["limit"],$_POST["lastDate"],$_POST["lat"],$_POST["lang"],$_POST["minradius"],$_POST["maxradius"]);
                break;
      case "get_open_discussion_user_notifs":
        	$x = new DB();
                $value = $x->get_open_discussion_user_notifs($_POST["user_id"],$_POST["building_id"],$_POST["lat"], $_POST["lang"]);
                break;
	  case "get_open_discussion_comments":
        	$x = new DB();
                $value = $x->get_open_discussion_comments($_POST["id"],$_POST["lastDate"],$_POST["limit"]);
                break;
      case "insert_open_discussion":
                $x = new DB();
                $value = $x->insert_open_discussion($_POST["building_id"], $_POST["user_id"], $_POST["text"],$_POST["lat"], $_POST["lang"]);
                break;
      case "insert_open_discussion_comment":
                $x = new DB();
                $result = $x->insert_open_discussion_comment($_POST["building_id"], $_POST["user_id"], $_POST["text"], $_POST["id"],$_POST["lat"], $_POST["lang"]);
                if($result == 1)
  		    $value=array("Success","Success");
	        else
  		    $value=array("Error","Error");
                break;
    }
    exit(json_encode($value));
} 
?>