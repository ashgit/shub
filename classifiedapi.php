<?php

include('modal.php'); 
$value=array(); 
if (isset($_POST["action"]) )
{
  switch ($_POST["action"])
    {
      case "get_buy_sell_ads":
        	$x = new DB();
                $value = $x->get_buy_sell_ads($_POST["building_name"], $_POST["category_name"]);
                break;
      case "get_buy_sell_ads_details":
        	$x = new DB();
                $value = $x->get_buy_sell_ads_details($_POST["bs_id"]);
                break;
      case "get_buy_sell_ads_comments":
        	$x = new DB();
                $value = $x->get_buy_sell_ads_comments($_POST["bs_id"]);
                break;
      case "insert_buy_sell_ques":
                  $x = new DB();
                 $value = $x->insert_buy_sell_ques($_POST["bs_id"], $_POST["user_name"], $_POST["comment"]);
                 break;
      case "insert_buy_sell_ans":
                  $x = new DB();
                $result = $x->insert_buy_sell_ans($_POST["bs_id"], $_POST["user_name"], $_POST["comment"], $_POST["parent"]);
                if($result == 1)
                    $value=array("Success","Success");
                else
                    $value=array("Error","Error");

                break;
    }
    exit(json_encode($value));
}
?>