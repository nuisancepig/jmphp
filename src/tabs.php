<?php
	require("../conn.php");
	// header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
	$ret_data=array();
	$time=date("Y-m-d h:i:sa");
	$flag = isset($_POST["flag"])?$_POST["flag"]:'';
	// $flag = 'Overdue';

	if($flag == "Overdue"){
		$sql = "SELECT id,otime,name,route,station FROM workshop_k where odata = '0' ";
		
		$res=$conn->query($sql);
		if($res->num_rows>0){
			$i = 0;
			while($row=$res->fetch_assoc()){
				if(strtotime($row["otime"])<strtotime($time)){
					$message = $row["name"]."的".$row["route"]."的".$row["station"]."已逾期！";
					$sql1 = "INSERT INTO message (content,time,department,state,workstate,route,station) VALUES ('".$message."','".$time."','销售部','0','逾期','".$row["route"]."','".$row["station"]."')";
					$res1=$conn->query($sql1);
					$sql2 = "UPDATE workshop_k SET odata='1' WHERE id='".$row["id"]."' ";
					$res2=$conn->query($sql2);
		   		}else{
					echo “zero2早于zero1′;
		   };
				$i++;
			}
			$ret_data["success"] = 'success';
		}
		
	}else if($flag == "Unread"){
		$department = $_POST["department"]; 
		$sql = "SELECT content,time,id,station,workstate,route,workshop,cuser,count FROM message WHERE state='0'  ORDER BY `id` desc ";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			$i = 0;
			while($row=$res->fetch_assoc()){
				
				$ret_data["data"][$i]["address"] = $row["content"];
				$ret_data["data"][$i]["date"] = $row["time"];
				$ret_data["data"][$i]["id"] = $row["id"];
				$ret_data["data"][$i]["tag"] = $row["station"];
				$ret_data["data"][$i]["state"] = $row["workstate"];
				$ret_data["data"][$i]["route"] = $row["route"];
				$ret_data["data"][$i]["workshop"] = $row["workshop"];
				$ret_data["data"][$i]["cuser"] = $row["cuser"];
				$ret_data["data"][$i]["count"] = $row["count"];
				$i++;
			}
			$ret_data["success"] = 'success';
		}
		//获取下拉车间筛选
		$sql2 = "SELECT DISTINCT workshop FROM message WHERE state='0' ORDER BY `id` desc";
		$res2 = $conn->query($sql2);
		if($res2->num_rows > 0) {
	      $i = 0;
	      while($row2 = $res2->fetch_assoc()) {
	        $ret_data["WorkshopBox"][$i]["f5"] = $row2['workshop'];
	        $i++;
	      }
	    }
	    //获取下拉状态筛选
		$sql3 = "SELECT DISTINCT workstate FROM message WHERE state='0' ORDER BY `id` desc";
		$res3 = $conn->query($sql3);
		if($res3->num_rows > 0) {
	      $i = 0;
	      while($row3 = $res3->fetch_assoc()) {
	        $ret_data["WorkstateBox"][$i]["f6"] = $row3['workstate'];
	        $i++;
	      }
	    }
	    //获取下拉状态筛选
		$sql4 = "SELECT DISTINCT cuser FROM message WHERE state='0' ORDER BY `id` desc";
		$res4 = $conn->query($sql4);
		if($res4->num_rows > 0) {
	      $i = 0;
	      while($row4 = $res4->fetch_assoc()) {
	        $ret_data["WorkcuserBox"][$i]["f7"] = $row4['cuser'];
	        $i++;
	      }
	    }
	}else if($flag == "Search"){
		$department = $_POST["department"]; 
		$modid = $_POST["modid"]; 

		$sql = "SELECT content,time,id,station,workstate,route,workshop,cuser,count FROM message WHERE state='0' AND CONCAT(content,time,workstate,station,route,cuser,count) LIKE  '%".$modid."%' or workshop like '%".$modid."%' ORDER BY `id` desc";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			$i = 0;
			while($row=$res->fetch_assoc()){
				
				$ret_data["data"][$i]["address"] = $row["content"];
				$ret_data["data"][$i]["date"] = $row["time"];
				$ret_data["data"][$i]["id"] = $row["id"];
				$ret_data["data"][$i]["tag"] = $row["station"];
				$ret_data["data"][$i]["state"] = $row["workstate"];
				$ret_data["data"][$i]["route"] = $row["route"];
				$ret_data["data"][$i]["workshop"] = $row["workshop"];
				$ret_data["data"][$i]["cuser"] = $row["cuser"];
				$ret_data["data"][$i]["count"] = $row["count"];
				$i++;
			}
			$ret_data["success"] = 'success';
		}
	}else if($flag == "SearchRead"){
		$department = $_POST["department"]; 
		$modid = $_POST["modid"]; 

		$sql = "SELECT content,time,id,station,workstate,route,workshop,cuser,count FROM message WHERE state='1' AND CONCAT(content,time,workstate,station,route,cuser,count) LIKE  '%".$modid."%' or workshop like '%".$modid."%' ORDER BY `id` desc";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			$i = 0;
			while($row=$res->fetch_assoc()){
				
				$ret_data["data"][$i]["address"] = $row["content"];
				$ret_data["data"][$i]["date"] = $row["time"];
				$ret_data["data"][$i]["id"] = $row["id"];
				$ret_data["data"][$i]["tag"] = $row["station"];
				$ret_data["data"][$i]["state"] = $row["workstate"];
				$ret_data["data"][$i]["route"] = $row["route"];
				$ret_data["data"][$i]["workshop"] = $row["workshop"];
				$ret_data["data"][$i]["cuser"] = $row["cuser"];
				$ret_data["data"][$i]["count"] = $row["count"];
				$i++;
			}
			$ret_data["success"] = 'success';
		}
	}else if($flag == "Read"){
		$department = $_POST["department"]; 
		$sql = "SELECT content,time,id,station,workstate,route,workshop,cuser,count FROM message WHERE state='1' ORDER BY `id` desc";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			$i = 0;
			while($row=$res->fetch_assoc()){
				
				$ret_data["data"][$i]["address"] = $row["content"];
				$ret_data["data"][$i]["date"] = $row["time"];
				$ret_data["data"][$i]["id"] = $row["id"];
				$ret_data["data"][$i]["tag"] = $row["station"];
				$ret_data["data"][$i]["state"] = $row["workstate"];
				$ret_data["data"][$i]["route"] = $row["route"];
				$ret_data["data"][$i]["workshop"] = $row["workshop"];
				$ret_data["data"][$i]["cuser"] = $row["cuser"];
				$ret_data["data"][$i]["count"] = $row["count"];
				$i++;
			}
			//获取下拉车间筛选
			$sql2 = "SELECT DISTINCT workshop FROM message WHERE state='1' ORDER BY `id` desc";
			$res2 = $conn->query($sql2);
			if($res2->num_rows > 0) {
		      $i = 0;
		      while($row2 = $res2->fetch_assoc()) {
		        $ret_data["WorkshopBox1"][$i]["f5"] = $row2['workshop'];
		        $i++;
		      }
		    }
		    //获取下拉状态筛选
			$sql3 = "SELECT DISTINCT workstate FROM message WHERE state='1' ORDER BY `id` desc";
			$res3 = $conn->query($sql3);
			if($res3->num_rows > 0) {
		      $i = 0;
		      while($row3 = $res3->fetch_assoc()) {
		        $ret_data["WorkstateBox1"][$i]["f6"] = $row3['workstate'];
		        $i++;
		      }
		    }
		    //获取下拉状态筛选
			$sql4 = "SELECT DISTINCT cuser FROM message WHERE state='1' ORDER BY `id` desc";
			$res4 = $conn->query($sql4);
			if($res4->num_rows > 0) {
		      $i = 0;
		      while($row4 = $res4->fetch_assoc()) {
		        $ret_data["WorkcuserBox1"][$i]["f7"] = $row4['cuser'];
		        $i++;
		      }
		    }
			$ret_data["success"] = 'success';
		}
	}else if($flag == "Recycle"){
		$sql = "SELECT content,time,id,station,workstate,route,workshop,cuser,count FROM message where state='1' or state='0' ORDER BY `id` desc ";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			$i = 0;
			while($row=$res->fetch_assoc()){
				
				$ret_data["data"][$i]["address"] = $row["content"];
				$ret_data["data"][$i]["date"] = $row["time"];
				$ret_data["data"][$i]["id"] = $row["id"];
				$ret_data["data"][$i]["tag"] = $row["station"];
				$ret_data["data"][$i]["state"] = $row["workstate"];
				$ret_data["data"][$i]["route"] = $row["route"];
				$ret_data["data"][$i]["workshop"] = $row["workshop"];
				$ret_data["data"][$i]["cuser"] = $row["cuser"];
				$ret_data["data"][$i]["count"] = $row["count"];
				$i++;
			}
			$ret_data["success"] = 'success';
		}	
	}else if($flag == "ReadIn"){
		$id = $_POST["id"]; 
		$sql = "UPDATE message SET state='1' WHERE id='".$id."' ";
		$res=$conn->query($sql);
		
	}else if($flag == "allRead"){
		$id = $_POST["id"]; 
		$sql = "UPDATE message SET state='1' WHERE id='".$id."' ";
		$res=$conn->query($sql);
		
	}else if($flag == "RecycleIn"){
		$id = $_POST["id"]; 
		$sql = "UPDATE message SET state='2' WHERE id='".$id."' ";
		$res=$conn->query($sql);
		
	
	}else if($flag == "allDel"){
		$id = $_POST["id"]; 
		$sql = "UPDATE message SET state='2' WHERE id='".$id."' ";
		$res=$conn->query($sql);
		
	
	}else if($flag == "Reduction"){
		$id = $_POST["id"]; 
		$sql = "UPDATE message SET state='1' WHERE id='".$id."' ";
		$res=$conn->query($sql);
		
	}else if($flag == "selectUnreadTime"){
		$DateData = isset($_POST["DateData"])?$_POST["DateData"]:'';
		$DateData = explode(",",$DateData);
		$startime = $DateData[0]." 00:00:00";
		$endtime = $DateData[1]." 23:59:59";
		$sql = "SELECT content,time,id,station,workstate,route,workshop,cuser,count FROM message WHERE state='0' AND time BETWEEN '".$startime."' AND '".$endtime."' ORDER BY `id` desc ";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			$i = 0;
			while($row=$res->fetch_assoc()){
				
				$ret_data["data"][$i]["address"] = $row["content"];
				$ret_data["data"][$i]["date"] = $row["time"];
				$ret_data["data"][$i]["id"] = $row["id"];
				$ret_data["data"][$i]["tag"] = $row["station"];
				$ret_data["data"][$i]["state"] = $row["workstate"];
				$ret_data["data"][$i]["route"] = $row["route"];
				$ret_data["data"][$i]["workshop"] = $row["workshop"];
				$ret_data["data"][$i]["cuser"] = $row["cuser"];
				$ret_data["data"][$i]["count"] = $row["count"];
				$i++;
			}
			$ret_data["success"] = 'success';
		}
		//获取下拉车间筛选
		$sql2 = "SELECT DISTINCT workshop FROM message WHERE state='0' AND time BETWEEN '".$startime."' AND '".$endtime."' ORDER BY `id` desc";
		$res2 = $conn->query($sql2);
		if($res2->num_rows > 0) {
	      $i = 0;
	      while($row2 = $res2->fetch_assoc()) {
	        $ret_data["WorkshopBox"][$i]["f5"] = $row2['workshop'];
	        $i++;
	      }
	    }
	    //获取下拉状态筛选
		$sql3 = "SELECT DISTINCT workstate FROM message WHERE state='0' AND time BETWEEN '".$startime."' AND '".$endtime."' ORDER BY `id` desc";
		$res3 = $conn->query($sql3);
		if($res3->num_rows > 0) {
	      $i = 0;
	      while($row3 = $res3->fetch_assoc()) {
	        $ret_data["WorkstateBox"][$i]["f6"] = $row3['workstate'];
	        $i++;
	      }
	    }
	    //获取下拉状态筛选
		$sql4 = "SELECT DISTINCT cuser FROM message WHERE state='0' AND time BETWEEN '".$startime."' AND '".$endtime."' ORDER BY `id` desc";
		$res4 = $conn->query($sql4);
		if($res4->num_rows > 0) {
	      $i = 0;
	      while($row4 = $res4->fetch_assoc()) {
	        $ret_data["WorkcuserBox"][$i]["f7"] = $row4['cuser'];
	        $i++;
	      }
	    }
	}else if($flag == "selectReadTime"){
		$DateData = isset($_POST["DateData"])?$_POST["DateData"]:'';
		$DateData = explode(",",$DateData);
		$startime = $DateData[0]." 00:00:00";
		$endtime = $DateData[1]." 23:59:59";
		$sql = "SELECT content,time,id,station,workstate,route,workshop,cuser,count FROM message WHERE state='1' AND time BETWEEN '".$startime."' AND '".$endtime."'  ORDER BY `id` desc";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			$i = 0;
			while($row=$res->fetch_assoc()){
				
				$ret_data["data"][$i]["address"] = $row["content"];
				$ret_data["data"][$i]["date"] = $row["time"];
				$ret_data["data"][$i]["id"] = $row["id"];
				$ret_data["data"][$i]["tag"] = $row["station"];
				$ret_data["data"][$i]["state"] = $row["workstate"];
				$ret_data["data"][$i]["route"] = $row["route"];
				$ret_data["data"][$i]["workshop"] = $row["workshop"];
				$ret_data["data"][$i]["cuser"] = $row["cuser"];
				$ret_data["data"][$i]["count"] = $row["count"];
				$i++;
			}
			//获取下拉车间筛选
			$sql2 = "SELECT DISTINCT workshop FROM message WHERE state='1' AND time BETWEEN '".$startime."' AND '".$endtime."'  ORDER BY `id` desc";
			$res2 = $conn->query($sql2);
			if($res2->num_rows > 0) {
		      $i = 0;
		      while($row2 = $res2->fetch_assoc()) {
		        $ret_data["WorkshopBox1"][$i]["f5"] = $row2['workshop'];
		        $i++;
		      }
		    }
		    //获取下拉状态筛选
			$sql3 = "SELECT DISTINCT workstate FROM message WHERE state='1' AND time BETWEEN '".$startime."' AND '".$endtime."'  ORDER BY `id` desc";
			$res3 = $conn->query($sql3);
			if($res3->num_rows > 0) {
		      $i = 0;
		      while($row3 = $res3->fetch_assoc()) {
		        $ret_data["WorkstateBox1"][$i]["f6"] = $row3['workstate'];
		        $i++;
		      }
		    }
		    //获取下拉状态筛选
			$sql4 = "SELECT DISTINCT cuser FROM message WHERE state='1' AND time BETWEEN '".$startime."' AND '".$endtime."'  ORDER BY `id` desc";
			$res4 = $conn->query($sql4);
			if($res4->num_rows > 0) {
		      $i = 0;
		      while($row4 = $res4->fetch_assoc()) {
		        $ret_data["WorkcuserBox1"][$i]["f7"] = $row4['cuser'];
		        $i++;
		      }
		    }
			$ret_data["success"] = 'success';
		}
	}
		
	
	$conn->close();
	$json = json_encode($ret_data);
	echo $json;
?>