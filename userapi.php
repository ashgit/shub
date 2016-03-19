<?php

include('modal.php');
$value=array();
if (isset($_POST["action"]))
{
	$x = new DB();
 switch ($_POST["action"])
    {
      case "adduser":
        $value = $x->adduser($_POST["name"] ,$_POST["picurl"],$_POST["facebook_id"],$_POST["email"],$_POST["lat"],$_POST["lang"],$_POST["address"],$_POST["pin"]);
		break;
	  
	  case "update_openforum_view_time":
        $value = $x->updateopenforumviewtime($_POST["user_id"]);
		break;
	
	  case "follow_category":
        $value = $x->followcategory($_POST["user_id"],$_POST["category_id"],$_POST["building_id"]);
		break;

	  case "unfollow_category":
        $value = $x->unfollowcategory($_POST["user_id"],$_POST["category_id"]);
		break;
		
	case "get_user_categories":
        $value = $x->getusercategories($_POST["user_id"]);
		break;
		
	case "get_user_categories_notifs":
        $value = $x->getusercategoriesNotifs($_POST["user_id"]);
		break;
		
	case "update_user_category_group_view_time":	
        $value = $x->updateusercategorygroupviewtime($_POST["user_id"],$_POST["category_id"]);
		break;
			
	case "update_gcm":	
        $value = $x->updategcm($_POST["user_id"],$_POST["gcm_id"]);
		break;	
			
	}
}
exit(json_encode($value));
?>
