<?php

include('modal.php');
$value=array();
if (isset($_POST["action"]) )
{
  switch ($_POST["action"])
    {
      case "get_building_notices":
        	$x = new DB();
                $value = $x->get_building_notices($_POST["building_id"],$_POST["limit"],$_POST["lastDate"]);
				
                break;
      case "insert_notice":
                $x = new DB();
                $value = $x->insert_notice($_POST["heading"], $_POST["url"], $_POST["apt_name"], $_POST["user_name"]);
                if($result == 1)
  		    $value=array("Success","Success");
	        else
  		    $value=array("Error","Error");
                break;
    }
exit(json_encode($value));
}
?>