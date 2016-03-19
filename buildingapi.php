<?php

include('modal.php'); 
$value=array(); 

if (isset($_POST["action"]) ) 
{ 

switch ($_POST["action"])
    {
      case "building_list":
				$x = new DB();
                $value = $x->all_building();
                break;
      case "members_list":
				$x = new DB();
                $value = $x->get_building_members($_POST["building_id"],$_POST["lastDate"],$_POST["limit"]);
                break;
    } 
} 

exit(json_encode($value));
?>