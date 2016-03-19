<?php
error_reporting(0);
session_start();
date_default_timezone_set("Asia/Kolkata");
    class DB{
        public $link;
        function __construct() {
			//echo"Constructor";
            //$this->link =  mysql_connect('mysql.hostinger.in', 'u580335094_test', 'dPjzsX1AgH',true) or die('Could not connect'.mysql_error());

		    //mysql_select_db("u580335094_reg",$this->link) or die( "Cannot select db.");
			
			$this->link =  mysql_connect('localhost', 'root', '',true) or die('Could not connect'.mysql_error());
		    mysql_select_db("sozial",$this->link) or die( "Cannot select db.");
		
        }
		
		function __destruct () {
			//echo"__destruct";
        	mysql_close($this->link);
    	}
		
		 // fetch all building name after login
	    public function all_building()
		{
			$rows = array();
			$key=0;
			$result=mysql_query("SELECT * FROM building",$this->link);
			while($query_row=mysql_fetch_assoc($result))
			{
				$names = array();
				$names[0] = $query_row['building_name'];
				$names[1] = $query_row['building_id'];
				$rows[$key] = $names;
				$key++;
			}
			return $rows;
		}
		
		 public function get_building_members($building_id,$lastDate,$limit)
		{		
		    $names = array();
			$key=0;
			$result=mysql_query("select * from user where building_id='$building_id'&& added_date <'$lastDate' ORDER BY added_date DESC  LIMIT $limit ",$this->link);
		        while($query_row=mysql_fetch_assoc($result))
			{
                                $row = array();
				$row[0] = $query_row['id'];
				$row[1] = $query_row['email'];
				$row[2] = $query_row['name'];
				$row[3] = $query_row['profile_pic'];
				                $names[$key] = $row;
                                $names[$key] = array('id' => $row[0] ,'email' => $row[1],'name' => $row[2],'profile_pic' => $row[3]);
                                $key++;
			}
			return $names;
		}



		// add user registration
		public function adduser($name,$pic, $facebook_id, $email,$lat,$lang,$address,$pin)
		{
		$today = date('Y-m-d H:i:s');
					 $res = array();
                     $id = "";
					 
					 $result = mysql_query("INSERT INTO building (pincode, address, lat,lng,status) VALUES ('$pin','$address','$lat','$lang','active')",$this->link);
					$id = mysql_insert_id();
			
                     $result = mysql_query("INSERT INTO user (name, building_id, profile_pic,facebook_id,email,added_date) VALUES ('$name','$id','$pic','$facebook_id','$email',   '$today')",$this->link);
					 
					 $result1 = mysql_query("SELECT * FROM user WHERE facebook_id='$facebook_id'",$this->link);
					
					 $names = array();				
					 $user_db_id="zzz";
					 
					while($query_row=mysql_fetch_assoc($result1))
					{
						$user_db_id = $query_row['id'];
						 break;
					}
					 $names[0] = $user_db_id;
				
				
                     if($result)
                     {
						$names[1] = "success"; 
						$names[2] = $id; 
                     }
                     else
                     {
						$names[1] = "error"; 
                     }
					 
					 $res[0] = $names;
					 return $names;
        }
		
		public function followcategory($user_id,$category_id,$building_id)
		{
		$today = date('Y-m-d H:i:s'); 
			$result = mysql_query("INSERT INTO user_groups (user_id,group_id,building_id,added_date,approval_status) VALUES ('$user_id',	
			'$category_id','$building_id','$today','approve')",$this->link);
		}
		
		public function unfollowcategory($user_id,$category_id)
		{
			$result = mysql_query("DELETE FROM user_groups WHERE user_id='$user_id' and group_id='$category_id' ",$this->link);
		}

		public function updategcm($user_id,$gcm_id)
		{
			$res = array();
			  $result = mysql_query("update user set  gcm_id='$gcm_id' where id = '$user_id' ;",$this->link);
			  $res[0] = "success";
			  return $res;
		}
		
		public function  getusercategories($user_id)
		{
			$rows = array();
			$key=0;
			$result=mysql_query("SELECT * FROM user_groups WHERE user_id='$user_id'",$this->link);
			while($query_row=mysql_fetch_assoc($result))
			{
				$catid =$query_row['group_id'];
				$result1=mysql_query("SELECT * FROM group_category WHERE group_category_id='$catid'",$this->link);
				while($query_row1=mysql_fetch_assoc($result1))
				{
					$rows[$key] = $query_row1;
					$key++;
					break;
				}

			}
			return $rows;
		}
		
		public function  getusercategoriesNotifs($user_id)
		{
			$rows = array();
			$key=0;
			$result=mysql_query("SELECT * FROM user_groups WHERE user_id='$user_id'",$this->link);
			while($query_row=mysql_fetch_assoc($result))
			{
				$catid =$query_row['group_id'];
				$lastVisitedDate =$query_row['last_visited'];
				
				$result1=mysql_query("SELECT count(*) as total FROM building_forum_activity WHERE group_id='$catid' AND added_date > '$lastVisitedDate'",$this->link);
				$data1=mysql_fetch_assoc($result1);
				if($data1)
				{
				$result2=mysql_query("select count(DISTINCT user_id) as counttotal from building_forum_activity WHERE group_id='$catid' group by user_id",$this->link);
				$data2=mysql_fetch_assoc($result2);
				
					$LadiesTalking = 0;
					if($data2)
						$LadiesTalking = $data2["counttotal"];
					
					$rows1 = array();		
					$rows1[0] =$LadiesTalking;
					$rows1[1] =$catid;
					$rows1[2] = $data1["total"];
					
					$rows[$key] = $rows1;
					$key = $key + 1;
				}
			}
			return $rows;
		}
		
		
		public function updateopenforumviewtime($user_id)
		{
			$today = date('Y-m-d H:i:s'); 
			
			$result1 = mysql_query("SELECT * from user_general_group_forum where user_id='$user_id'",$this->link);
			$num_rows = mysql_num_rows($result1);
			if($num_rows > 0)
			{
			  $result = mysql_query("update user_general_group_forum set view_date = '$today' where user_id='$user_id';",$this->link);
			}
			else
			{
			$result = mysql_query("INSERT INTO user_general_group_forum (user_id,view_date) VALUES ('$user_id','$today')",$this->link);
			}
                     
                        
		}

		public function updateusercategorygroupviewtime ($user_id, $category_id)
		{
			$today = date('Y-m-d H:i:s'); 

			$result = mysql_query("update user_groups set last_visited = '$today' where user_id='$user_id' AND group_id='$category_id';",$this->link);
		}
		
        public function get_open_discussion_user_notifs($user_id,$building_id,$lat,$lang)
		{	$viewDate = date('Y-m-d H:i:s');   
			
			$result = mysql_query("SELECT * from user_general_group_forum where user_id='$user_id'",$this->link);
			while($query_row=mysql_fetch_assoc($result))
					{
						$viewDate = $query_row['view_date'];
						 break;
					}
					
			$finalResult=array();
			$key=1;
			
			$neighbourRadius = 15;
			$qu = "SELECT *,  ( 6371 * acos( cos( radians($lat
					) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($lang) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance FROM building_general_group_forum HAVING distance < $neighbourRadius and !(user_id ='$user_id')";

					
			$result = mysql_query($qu,$this->link);
			$countnotif = 0;
			$finalResult[0] = $countnotif;
			
			while($query_row=mysql_fetch_assoc($result))
			{
				if($query_row['added_date']>$viewDate)
				{
					$finalResult[$key] = $query_row;
					$key++;
				}
				$countnotif = $countnotif + 1;
			}
			$finalResult[0] = $countnotif;
			return $finalResult;         
		}
		
		public function get_building_notices($building_id,$limit,$lastDate)
		{
            $names = array();
			$key=0;
			$result=mysql_query("select * from notice_board where building_id='$building_id' &&  status ='active' && added_date <'$lastDate' ORDER BY added_date DESC  LIMIT $limit ",$this->link);
		        while($query_row=mysql_fetch_assoc($result))
			{
                                $row = array();
				$row[0] = $query_row['title'];
				$row[1] = $query_row['image'];
				$row[2] = $query_row['added_date'];
                                $names[$key] = $row;
                                $names[$key] = array('heading' => $row[0] ,'url' => $row[1],'date' => $row[2]);
                                $key++;
			}

			return $names;
		}

                public function insert_notice($heading, $url, $building_id, $user_id)
		{			
                        
                        $id = $building_id;
                        $userid = $user_id;

                        $data = $url;
                        $today = date("Y-m-d H:i:s");    
                        $result = mysql_query("INSERT INTO notice_board (building_id, user_id, title, image, status, added_date) VALUES ('$id','$userid','$heading','$data','active','$today')",$this->link);		
                        if($result)
                        {

                           return 1;
                        }
                        else
                        {
                           return 0;
                        }
          	
		}


        	public function get_buy_sell_ads($building_name, $category_name)
		{
                        $result = mysql_query("select building_id from building where building_name like '$building_name'",$this->link);
                        $query_row=mysql_fetch_assoc($result);
                        $building_id = $query_row['building_id'];
        
                        $result = mysql_query("select bs_category_id from bs_category where category_name like '$category_name'",$this->link);
                        $query_row=mysql_fetch_assoc($result);
                        $category_id = $query_row['bs_category_id'];

                        $names = array();
			$key=0;

			//echo "SELECT * FROM bs_sell WHERE building_id='$building_id' && bs_category='$category_id' && status = 'active' order by added_date DESC";
			$result=mysql_query("SELECT * FROM buy_sell WHERE building_id='$building_id' && bs_category='$category_id' && status = 'active' order by added_date DESC",$this->link);

         	        while($query_row=mysql_fetch_assoc($result))
			{
                                $row = array();
                                $img = $query_row['images'];		
                                list($row[0], $row[3], $row[4])  = split('[;]', $img);
				$row[1] = $query_row['title'];
				$row[2] = $query_row['description'];
				$row[5] = $query_row['bs_id'];
                                $row[6] = $query_row['image_count'];
                                $names[$key] = $row;
                                $names[$key] = array('id' => $row[5] ,'image' => $row[0] ,'title' => $row[1] ,'description' => $row[2] ,'count' => $row[6]);
                                $key++;
			}

			return $names;
		}


	// get buy sell ads detail

		public function get_buy_sell_ads_details($id)
		{
                        $names = array();
			$key=0;
			$result=mysql_query("SELECT * FROM buy_sell WHERE bs_id='$id' && status = 'active'",$this->link);
		        while($query_row=mysql_fetch_assoc($result))
			{
                                $row = array();
				$row[0] = $query_row['user_id'];
				$row[1] = $query_row['title'];
				$row[2] = $query_row['description'];
                                $img = $query_row['images'];		
				$row[3] = $query_row['price'];
				$row[4] = $query_row['contact'];
				$row[5] = $query_row['product_condition'];
                                list($row[6], $row[7], $row[8])  = split('[;]', $img);
				$row[9] = $query_row['image_count'];
                                $names[$key] = $row;
                                $names[$key] = array('user_id' => $row[0] ,'title' => $row[1] ,'description' => $row[2], 'price' => $row[3] ,'contact' => $row[4] ,'product_condition' => $row[5], 'image1' => $row[6] ,'image2' => $row[7] ,'image3' => $row[8] ,'count' => $row[9]);
                                $key++;
			}
			return $names;
                 }


                public function get_buy_sell_ads_comments($id)
		{
                        $result = mysql_query("select user_id from buy_sell where bs_id = '$id'",$this->link);
                        $query_row=mysql_fetch_assoc($result);
                        $headid = $query_row['user_id'];

                        $names = array();
			$key=0;
//			$result=mysql_query("SELECT a.comment as ques, b.comment as ans, u.name as name, u.profile_pic  as pic, us.profile_pic as head_pic, us.name as head_name  FROM buy_sell_comments AS a, buy_sell_comments AS b, user as u, user as us WHERE a.comment_id = b.comment_parent_id and a.bs_id='$id' and u.id=a.user_id and us.id='$headid' union SELECT comment as ques, comment as ans, u.name, u.profile_pic , us.profile_pic as head_pic, us.name as head_name FROM buy_sell_comments, user as u, user as us WHERE buy_sell_comments.comment_id <> buy_sell_comments.comment_parent_id and buy_sell_comments.bs_id='$id' and buy_sell_comments.user_id=u.id and us.id='$headid' and buy_sell_comments.comment_parent_id=0;",$this->link);
//			$result=mysql_query("SELECT a.comment_id as id , a.comment as ques , b.comment as ans, u.name as name, u.profile_pic  as pic, us.profile_pic as head_pic, us.name as head_name FROM buy_sell_comments as a, buy_sell_comments as b, user as u, user as us  WHERE a.bs_id='$id' and a.comment_id=b.comment_parent_id  and u.id=a.user_id and us.id='$headid' union SELECT a.comment_id as id , comment as ques , comment as ans, u.name as name, u.profile_pic  as pic, us.profile_pic as head_pic, us.name as head_name from buy_sell_comments, user as u, user as us where bs_id='$id' and comment_parent_id=0 and comment_id and buy_sell_comments.user_id=u.id and us.id='$headid' not in  (select comment_parent_id from buy_sell_comments where comment_parent_id<>0);",$this->link);
			$result=mysql_query("SELECT a.comment_id as id , a.comment as ques , b.comment as ans, u.name as name, u.profile_pic  as pic, us.profile_pic as head_pic, us.name as head_name FROM buy_sell_comments as a, buy_sell_comments as b, user as u, user as us WHERE a.bs_id='$id' and a.comment_id=b.comment_parent_id  and u.id=a.user_id and us.id='$headid' union SELECT comment_id as id , comment as ques , comment as ans, u.name as name, u.profile_pic as pic, us.profile_pic as head_pic, us.name as head_name from buy_sell_comments , user as u, user as us where bs_id='$id' and comment_parent_id=0 and user_id=u.id and us.id='$headid' and comment_id not in  (select comment_parent_id from buy_sell_comments where comment_parent_id<>0);",$this->link);
		        while($query_row=mysql_fetch_assoc($result))
			{
                                $row = array();
				$row[0] = $query_row['ques'];
				$row[1] = $query_row['ans'];
				$row[2] = $query_row['name'];
				$row[3] = $query_row['pic'];
				$row[4] = $query_row['head_pic'];
				$row[5] = $query_row['head_name'];
				$row[6] = $query_row['id'];

      				$names[$key] = $row;
                                $names[$key] = array('ques' => $row[0] ,'ans' => $row[1], 'name' => $row[2] ,'pic' => $row[3], 'head_pic' => $row[4] ,'head_name' => $row[5] ,'id' => $row[6]);
                                $key++;
			}
			return $names;
                 }



                public function insert_buy_sell_ques($id, $user_name, $comment)
		{			
                        $result = mysql_query("select id from user where name like '$user_name'",$this->link);
                        $query_row=mysql_fetch_assoc($result);
                        $userid = $query_row['id'];

                        $today = date("Y-m-d H:i:s");  
                        $result = mysql_query("INSERT INTO buy_sell_comments (bs_id, user_id, comment, status, added_date) VALUES ('$id','$userid','$comment','active','$today')",$this->link);		
                        
                        $result = mysql_query("select user_id from buy_sell where bs_id = '$id'",$this->link);
                        $query_row=mysql_fetch_assoc($result);
                        $receiver = $query_row['user_id'];

                        $result = mysql_query("INSERT INTO notifications (notification_type, initiator_id, reciver_id, item_id, status, added_date) VALUES ('7', '$userid', '$receiver', '$id','unread','$today')",$this->link);
                        
                        $names = array();
			$key=0;
			$result=mysql_query("SELECT comment_id from buy_sell_comments where bs_id='$id' and user_id='$userid' and comment like '$comment';",$this->link);
		        while($query_row=mysql_fetch_assoc($result))
			{
                                $row = array();
				$row[0] = $query_row['comment_id'];
                                $names[$key] = $row;
                                $names[$key] = array('parent_id' => $row[0]);
                                $key++;
			}
			return $names;
          	}






                public function insert_buy_sell_ans($id, $user_name, $comment, $parent)
		{			
                        $result = mysql_query("select id from user where name like '$user_name'",$this->link);
                        $query_row=mysql_fetch_assoc($result);
                        $userid = $query_row['id'];

                        $today = date("Y-m-d H:i:s");  
                        $result = mysql_query("INSERT INTO buy_sell_comments (bs_id, comment_parent_id, user_id, comment, status, added_date) VALUES ('$id','$parent','$userid','$comment','active','$today')",$this->link);		
                        
                       /* $result = mysql_query("select user_id from buy_sell where bs_id = '$id'",$this->link);
                        $query_row=mysql_fetch_assoc($result);
                        $receiver = $query_row['user_id'];*/

                        $result = mysql_query("select user_id from buy_sell_comments where comment_id = '$parent';",$this->link);
                        $query_row=mysql_fetch_assoc($result);
                        $receiver = $query_row['user_id'];

                        $result = mysql_query("INSERT INTO notifications (notification_type, initiator_id, reciver_id, item_id, status, added_date) VALUES ('8', '$userid', '$receiver', '$id','unread','$today');",$this->link);
                        if($result)
                        {
                           return 1;
                        }
                        else
                        {
                           return 0;
                        }
          	}

	        public function get_notifications($user_name)
		{
                        $names = array();
			$key=0;
	                $result = mysql_query("select id from user where name like '$user_name'",$this->link);
                        $query_row=mysql_fetch_assoc($result);
                        $userid = $query_row['id'];

			$result=mysql_query("SELECT * FROM notifications WHERE reciver_id = '$userid' && status='unread' order by added_date DESC",$this->link);
                        while($query_row=mysql_fetch_assoc($result))
			{
                                $row = array();
				$row[1] = $query_row['notification_type'];
				$row[7] = $query_row['notification_type'];
				$row[2] = $query_row['initiator_id'];
                                $row[4] = $query_row['item_id'];
                                $row[8] = $query_row['item_id'];
                                $row[6] = $query_row['added_date'];
                                $row[9] = $query_row['notification_id'];
                   		
                                $result1=mysql_query("SELECT name FROM user WHERE id = '$row[2]'",$this->link);
                                $query_row=mysql_fetch_assoc($result1);
                                $row[2] = $query_row['name'];
  
                                if($row[1]==7  ||  $row[1]==8)
                                {
                                     $result1=mysql_query("SELECT title FROM buy_sell WHERE bs_id = '$row[4]';",$this->link);
                                     $query_row=mysql_fetch_assoc($result1);
                                     $row[4] = $query_row['title'];
                                }
                                if($row[1]==11)
                                {
                                     $result1=mysql_query("SELECT title FROM notice_board WHERE board_id = '$row[4]';",$this->link);
                                     $query_row=mysql_fetch_assoc($result1);
                                     $row[4] = $query_row['title'];
                                }


				$names[$key] = $row;                               
                                $names[$key] = array('notification_id' => $row[9] ,'initiator_name' => $row[2] ,'item_name' => $row[4] ,'added_date' => $row[6] ,'notification_type' => $row[7] ,'item_id' => $row[8]);
                                $key++;
			}
			return $names;
		}



	        public function redirect_notifications($notification_type ,$item_id)
		{
                        if($notification_type=7  ||  $notification_type=8)
                        {
                             $names = array();
			     $key=0;
                             $result=mysql_query("SELECT * FROM buy_sell WHERE bs_id = '$item_id'",$this->link);
                             while($query_row=mysql_fetch_assoc($result))
			     {
                                $row = array();
				$row[0] = $query_row['notification_id'];
				$row[1] = $query_row['notification_type'];
				$row[7] = $query_row['notification_type'];
				$row[2] = $query_row['initiator_id'];
				$row[3] = $query_row['reciver_id'];
                                $row[4] = $query_row['item_id'];
				$row[8] = $query_row['item_id'];
				$row[5] = $query_row['status'];
                                $row[6] = $query_row['added_date'];
                   		
                                $names[$key] = $row;
                                $names[$key] = array('notification_id' => $row[0] ,'notification_type_name' => $row[1], 'initiator_name' => $row[2] ,'reciver_id' => $row[3] ,'item_name' => $row[4] ,'status' => $row[5] ,'added_date' => $row[6] ,'notification_type' => $row[7] ,'item_id' => $row[8]);
                                $key++;
                             }
			}
			return $names;
		}
                public function get_notifications_count($user_name)
		{
                        $names = array();
			$key=0;
	                $result = mysql_query("select id from user where name like '$user_name'",$this->link);
                        $query_row=mysql_fetch_assoc($result);
                        $userid = $query_row['id'];

			$result=mysql_query("SELECT * FROM notifications WHERE reciver_id = '$userid' && status='unread'",$this->link);
			$result = mysql_num_rows($result);
                        $row = array();
			$row[0] = $result;
                        $names[$key] = $row;
                        $names[$key] = array('count' => $row[0]);

			return $names;

		}


		public function read_notifications($notification_id)
		{
                     $result = mysql_query("update notifications set status = 'read' where notification_id='$notification_id';",$this->link);
                     if($result)
                     {
                        return 1;
                     }
                     else
                     {
                        return 0;
                     }
          	}

        public function get_open_discussion($building_id,$limit,$lastDate,$lat,$lang,$lowerlimit,$upperlimit)
		{
					$qu = "SELECT *,  ( 6371 * acos( cos( radians($lat
					) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($lang) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance FROM building_general_group_forum HAVING distance < $upperlimit and distance >= $lowerlimit and activity_parent_id<1  ORDER BY distance";


                    $names = array();
					$key=0;
					$result = mysql_query($qu,$this->link);

					while($query_row=mysql_fetch_assoc($result))
					{
					$usrid=	$query_row['user_id'];
                    $row = array();
							
					$row[0] = $query_row['added_date'];
					$row[1] = $query_row['activity_id'];
					$row[2] = $usrid; //user id					
					$row[3] = $query_row['text'];
					$userRes = mysql_query("SELECT * FROM  user WHERE id='$usrid';",$this->link);
					while($query_row2=mysql_fetch_assoc($userRes))
					{
					   $row[4] = $query_row2['profile_pic'];   //user pic
					   $row[5] = $query_row2['name'];
						break;
					}
	             	    $result1 = mysql_query("select count(*) as count from building_general_group_forum where activity_parent_id='$row[1]';",$this->link);
                            $query_row1=mysql_fetch_assoc($result1);                            
                            $row[6] = $query_row1['count'];
							 $row[7] = $query_row['image'];
							
                 	    $names[$key] = $row;
                            $names[$key] = array('date' => $row[0] ,'id' => $row[1], 'user' => $row[2] ,'pic' => $row[4] ,'text' => $row[3] ,'count' => $row[6] ,'name' => $row[5],'image' => $row[7]);
                            $key++;
					}
					return $names;
		}

		public function get_all_categories_explore($lastDate,$limit)
		{
			$qu = "SELECT * FROM group_category WHERE added_date <'$lastDate' ORDER BY added_date DESC LIMIT $limit;";
			$result = mysql_query($qu,$this->link);
			$key = 0;	
			while($query_row=mysql_fetch_assoc($result))
			{
					$finalResult[$key] = $query_row;
					$key++;
			}
			return $finalResult;   
		}
		
		        public function get_open_discussion_explore($building_id,$categorytype,$limit,$lastDate,$lat,$lang,$lowerlimit,$upperlimit)
		{
          

					$qu = "SELECT *,  ( 6371 * acos( cos( radians($lat
					) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($lang) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance FROM building_forum_activity HAVING distance < $upperlimit and distance >= $lowerlimit and activity_parent_id<1 and group_id='$categorytype'  ORDER BY distance";


                    $names = array();
					$key=0;
					$result = mysql_query($qu,$this->link);

					while($query_row=mysql_fetch_assoc($result))
					{
					$usrid=	$query_row['user_id'];
                    $row = array();
							
					$row[0] = $query_row['added_date'];
					$row[1] = $query_row['activity_id'];
					$row[2] = $usrid; //user id					
					$row[3] = $query_row['text'];
					$userRes = mysql_query("SELECT * FROM  user WHERE id='$usrid';",$this->link);
					while($query_row2=mysql_fetch_assoc($userRes))
					{
					   $row[4] = $query_row2['profile_pic'];   //user pic
					   $row[5] = $query_row2['name'];
						break;
					}
	             	    $result1 = mysql_query("select count(*) as count from building_forum_activity where activity_parent_id='$row[1]';",$this->link);
                            $query_row1=mysql_fetch_assoc($result1);                            
                            $row[6] = $query_row1['count'];
							
							
                 	    $names[$key] = $row;
                            $names[$key] = array('date' => $row[0] ,'id' => $row[1], 'user' => $row[2] ,'pic' => $row[4] ,'text' => $row[3] ,'count' => $row[6] ,'name' => $row[5]);
                            $key++;
					}
					return $names;
		}


                public function get_open_discussion_comments($activity_id,$lastDate,$limit)
		{
                        $names = array();
			$key=0;
	                $result = mysql_query("SELECT text, user_id,added_date FROM  building_general_group_forum WHERE activity_parent_id='$activity_id' AND added_date< '$lastDate' order by added_date DESC LIMIT $limit;",$this->link);
                        while($query_row=mysql_fetch_assoc($result))
			{
                            $row = array();
			    $row[0] = $query_row['text'];
			    $row[1] = $query_row['user_id'];
                 	    $result1 = mysql_query("select * from user where id='$row[1]';",$this->link);
                            $query_row1=mysql_fetch_assoc($result1);                            
                            $row[2] = $query_row1['profile_pic'];
							$row[3] = $query_row1['name'];
							$row[4] = $query_row['added_date'];
                 	    $names[$key] = $row;
                            $names[$key] = array('text' => $row[0] ,'image' => $row[2],'name' => $row[3], 'date' => $row[4],'user_id' => $row[1] );
                            $key++;
     			}
			return $names;
		}
		
		                public function get_open_discussion_details_explore($activity_id,$lastDate,$limit)
		{
                        $names = array();
			$key=0;
	                $result = mysql_query("SELECT text, user_id,added_date FROM  building_forum_activity WHERE activity_parent_id='$activity_id' AND added_date< '$lastDate' order by added_date DESC LIMIT $limit;",$this->link);
                        while($query_row=mysql_fetch_assoc($result))
			{
                            $row = array();
			    $row[0] = $query_row['text'];
			    $row[1] = $query_row['user_id'];
                 	    $result1 = mysql_query("select * from user where id='$row[1]';",$this->link);
                            $query_row1=mysql_fetch_assoc($result1);                            
                            $row[2] = $query_row1['profile_pic'];
							$row[3] = $query_row1['name'];
							$row[4] = $query_row['added_date'];
                 	    $names[$key] = $row;
                            $names[$key] = array('text' => $row[0] ,'image' => $row[2],'name' => $row[3], 'date' => $row[4],'user_id' => $row[1] );
                            $key++;
     			}
			return $names;
		}

                public function insert_open_discussion($building_id, $user_id, $text,$lat,$lang)
		{
                        $id = $building_id;
                        $userid = $user_id;
						$names = array();
						$names[0] ="error";
                        $today = date("Y-m-d H:i:s");    
                        $result = mysql_query("INSERT INTO building_general_group_forum (building_id, user_id, activity_parent_id, text, status, added_date,lat,lng) VALUES ('$id','$userid',0,'$text','active','$today','$lat','$lang')",$this->link);		
						$names[1] = mysql_insert_id();
						
						if($result)
                        {
							$names[0] ="success";
                        }
                        else
                        {
							$names[0] ="error";
                        }
					
						return $names;
		}
		
		 public function insert_open_discussion_explore($building_id,$categorytype, $user_id, $text,$lat,$lang,$image)
		{
                        $id = $building_id;
                        $userid = $user_id;
						$names = array();
						$names[0] ="error";
                        $today = date("Y-m-d H:i:s");    
                        $result = mysql_query("INSERT INTO building_forum_activity (building_id, user_id, activity_parent_id,group_id, text, status, added_date,lat,lng,image) VALUES ('$id','$userid',0,'$categorytype','$text','active','$today','$lat','$lang','$image')",$this->link);		
						$names[1] = mysql_insert_id();
						
						if($result)
                        {
							$names[0] ="success";
                        }
                        else
                        {
							$names[0] ="error";
                        }
					
						return $names;
		}

		public function insert_open_discussion_general($building_id,$user_id, $text,$lat,$lang,$image)
		{
                        $id = $building_id;
                        $userid = $user_id;
						$names = array();
						$names[0] ="error";
                        $today = date("Y-m-d H:i:s");    
                        $result = mysql_query("INSERT INTO building_general_group_forum (building_id, user_id, activity_parent_id,text, status, added_date,lat,lng,image) VALUES ('$id','$userid',0,'$text','active','$today','$lat','$lang','$image')",$this->link);		
						$names[1] = mysql_insert_id();
						
						if($result)
                        {
							$names[0] ="success";
                        }
                        else
                        {
							$names[0] ="error";
                        }
					
						return $names;
		}
		
		
		
         public function insert_open_discussion_comment($building_id, $user_id, $text, $activity_id,$lat,$lang)
		{
                        $id = $building_id;
                        $userid = $user_id;

                        $today = date("Y-m-d H:i:s");    
                        $result = mysql_query("INSERT INTO building_general_group_forum (building_id, user_id, activity_parent_id, text, status, added_date,lat,lng) VALUES ('$id','$userid','$activity_id','$text','active','$today','$lat','$lang')",$this->link);		
                        if($result)
                        {
						
						$resultSel = mysql_query("SELECT * FROM building_general_group_forum WHERE activity_id='$activity_id'",$this->link);	
						$query_row_sel=mysql_fetch_assoc($resultSel);                            
                        $receiver_id = $query_row_sel['user_id'];
							
						
						         $resultN = mysql_query("INSERT INTO notifications (notification_type, initiator_id, reciver_id, item_id, status, added_date) VALUES ('12', '$userid', '$receiver_id', '$activity_id','unread','$today')",$this->link);
                           return 1;
                        }
                        else
                        {
                           return 0;
                        }
		} 
		
		public function insert_open_discussion_details_explore($building_id,$categorytype, $user_id, $text, $activity_id)
		{
                        $id = $building_id;
                        $userid = $user_id;

                        $today = date("Y-m-d H:i:s");    
                        $result = mysql_query("INSERT INTO building_forum_activity (building_id, user_id, activity_parent_id,group_id, text, status, added_date) VALUES ('$id','$userid','$activity_id','$categorytype','$text','active','$today')",$this->link);		
                        if($result)
                        {
						
						$resultSel = mysql_query("SELECT * FROM building_forum_activity WHERE activity_id='$activity_id'",$this->link);	
						$query_row_sel=mysql_fetch_assoc($resultSel);                            
                        $receiver_id = $query_row_sel['user_id'];
							
						
						         $resultN = mysql_query("INSERT INTO notifications (notification_type, initiator_id, reciver_id, item_id, status, added_date) VALUES ('12', '$userid', '$receiver_id', '$activity_id','unread','$today')",$this->link);
                           return 1;
                        }
                        else
                        {
                           return 0;
                        }
		} //
		
		


    } //class DB close	

?>	