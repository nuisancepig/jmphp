<?php
require("../../conn.php");

$time = date("Y-m-d H:i:s");
$flag = $_POST["flag"];
// $flag = '2';

switch ($flag) {
		case '0' : 

			$sql = "SELECT id,pid,name,figure_number,modid,routeid FROM review WHERE reviews != '0' and isfinish = '5'";
			$res = $conn->query($sql);
			if($res -> num_rows > 0) {
				$i = 0;
				while($row = $res->fetch_assoc()) {
					$data[$i]['rid'] = $row['id'];
					$data[$i]['pid'] = $row['pid'];
					$data[$i]['name'] = $row['name'];
                    $data[$i]['figure_number'] = $row['figure_number'];
					$data[$i]['routeid'] = $row['routeid'];
					$data[$i]['modid'] = $row['modid'];
					$i++;
				}
				$data['row'] = $i;
			} else{
				//若workshop_k无数据跳出循环
//				die();
				$data['row'] = 0;

			}
			$json = json_encode($data);
			echo $json;
			break;
		//显示评审信息	
		case '1' : 
			$rid = $_POST["rid"];
			$sql = "SELECT id,name,figure_number,reviews,route,photourl FROM review WHERE id = '".$rid."'";
			$res = $conn->query($sql);
			if($res -> num_rows > 0) {
				
				$i = 0;
				while($row = $res->fetch_assoc()) {
					$data[$i]['id'] = $row['id'];
					$data[$i]['name'] = $row['name'];
                    $data[$i]['figure_number'] = $row['figure_number'];
					$data[$i]['reviews'] = $row['reviews'];
					$data[$i]['route'] = $row['route'];
					$data[$i]['photourl'] = $row['photourl'];
					$i++;
				}
			} else{
				//若workshop_k无数据跳出循环
				die();
			}
			
			
			$data['row'] = $i;
			$json = json_encode($data);
			echo $json;
			break;
		case '2' :   
		$modid = $_POST["modid"];
		// $modid = '1000616931';
		$pid = $_POST["pid"];
		$rid = $_POST["rid"];
		$routeid = $_POST["routeid"];
//		$routeid = '19067';
		$route = $_POST["route"];
//		$station = $_POST["station"];
		//$messageid = $_POST["messageid"];
		$name = $_POST["name"];
//		$figure_number = $_POST["figure_number"];
		$reviews = $_POST["reviews"];
		$finishcount = $_POST["finishcount"];
		$writtenBy = $_POST["writtenBy"];
		$inspect = $_POST["inspect"];
		$department=$_POST['department'];
		// $finishcount = '2';
		// $inspect = '8';
//		$remark = $_POST["remark"];
		$sql_search = "SELECT isexterior FROM part WHERE modid = '".$modid."' and fid = '".$pid."'";
		$res_search = $conn->query($sql_search);
		if($res_search -> num_rows > 0) {
			while($row1 = $res_search->fetch_assoc()) {
				$isexterior = $row1['isexterior'];
			}
		}
		//判断route处于哪个车间
		$sql_class="SELECT workshop FROM workshop_class where route='" . $route . "'";
		$res_class = $conn->query($sql_class);
		if ($res_class->num_rows > 0) {
		    while ($row = $res_class->fetch_assoc()) {
		        $workshop = $row['workshop'];
		    }
		}
		else{
			$workshop ="其他";
		}
		//让步接收
		if ($inspect === "8") {
			if ($reviews === $finishcount) {
				//全部合格
				$reviews = $reviews - $finishcount;	
				$sql = "UPDATE workshop_k SET reviews=reviews-'".$finishcount."' ,utime='".$time."' WHERE modid='" . $modid . "' and routeid='" . $routeid . "' ORDER by id LIMIT 1";
				$conn -> query($sql);
				//更新review
				$sql2 = "UPDATE review SET reviews='".$reviews."', isfinish='3' WHERE modid='".$modid."' and routeid='".$routeid."' and id='" . $rid . "' ORDER by id LIMIT 1";
				$conn -> query($sql2);
//	//			// 更新message
 				$message = $name . "的" . $route .  "已评审！";
           		 $sql_mes = "INSERT INTO message (content,time,department,state,workstate,route,cuser,workshop,count) VALUES ('" . $message . "','" . date("Y-m-d H:i:s") . "','".$department."','0','让步接收','" . $route . "','" . $writtenBy . "','" . $workshop . "','" . $finishcount . "')";
				$conn->query($sql_mes);
//				$sql1 = "UPDATE message SET state='1' where id='" . $messageid . "' ORDER by id LIMIT 1 ";
//				$conn -> query($sql1);
				// 循环检测是否所有零件完成
				$sql3 = "SELECT todocount ,reviews ,inspectcount from workshop_k where modid='".$modid."' and routeid='".$routeid."'  ";
				$res = $conn -> query($sql3);
				if ($res -> num_rows > 0) {       
					while ($row = $res -> fetch_assoc()) {
						if ($row['todocount'] == '0'  && $row['reviews'] == '0' && $row['inspectcount'] == '0') {
							$sql4 = "UPDATE workshop_k SET isfinish='3'  WHERE modid='" . $modid . "' and routeid='" . $routeid . "' ORDER by id LIMIT 1";
							$conn -> query($sql4);
						}
					}
				}
			} else {
				//部分合格
				$reviews = $reviews - $finishcount;
				$sql5 = "UPDATE review SET reviews='".$reviews."' ,utime='" . $time . "' WHERE modid='" . $modid . "' and routeid='" . $routeid . "' and id='" . $rid . "' ORDER by id LIMIT 1";
				$conn -> query($sql5);
				
				$sql6 = "UPDATE workshop_k SET reviews=reviews - '".$finishcount."' ,utime='".$time."' WHERE modid='" . $modid . "' and routeid='" . $routeid . "' ORDER by id LIMIT 1";
				$conn -> query($sql6);
			}
			//记录让步接收的数量
			//检测是否存在
			$sql_exist="SELECT id FROM concession WHERE modid='".$modid."'";
			$res_exist = $conn -> query($sql_exist);
			if ($res_exist -> num_rows > 0) {
				$sql_update="UPDATE concession SET backcount =backcount+'".$finishcount."' WHERE modid='".$modid."' ";
				$conn -> query($sql_update);
			}
			else{
				$sql_sea="SELECT * FROM part WHERE  modid='".$modid."'";
				$res_sea=$conn -> query($sql_sea);
				if ($res_sea -> num_rows > 0) {
					while ($row = $res_sea -> fetch_assoc()) {
					$count=	$row['count'];
					$name=	$row['name'];
					$figure_number=	$row['figure_number'];
					$pNumber=$row['pNumber'];
//					echo $count;
//					echo $pNumber;
					$sql_add="INSERT INTO concession (backcount,count,name,modid,figure_number,pNumber) VALUES ('".$finishcount."','".$count."','".$name."','".$modid."','".$figure_number."','".$pNumber."')";	
					$conn -> query($sql_add);
					}
				}
			}
			
		}
		//返工返修记录次数，默认变为未完成
		else if ($inspect === "7") {
			if ($reviews === $finishcount) {
				$reviews = $reviews - $finishcount;
				if($isexterior=="1"){
					$sql7 = "UPDATE workshop_k SET isfinish='2' ,reviews='".$reviews."' ,inspectcount=inspectcount +  '" . $finishcount . "',notNum=notNum+1 WHERE modid='".$modid."' and routeid='".$routeid."' ";
					$conn -> query($sql7);
					$sql8 = "UPDATE review SET isfinish='3' ,reviews='".$reviews."'  WHERE modid='".$modid."' and routeid='".$routeid."'and id='" . $rid . "' ORDER by id LIMIT 1";
					$conn -> query($sql8);
				}
				else{
					$sql7 = "UPDATE workshop_k SET isfinish='2' ,reviews='".$reviews."' ,todocount=todocount + '".$finishcount."',notNum=notNum+1 WHERE modid='".$modid."' and routeid='".$routeid."' ORDER by id LIMIT 1";
					$conn -> query($sql7);
					$sql8 = "UPDATE review SET isfinish='3' ,reviews='".$reviews."'  WHERE modid='".$modid."' and routeid='".$routeid."'and id='" . $rid . "' ORDER by id LIMIT 1";
					$conn -> query($sql8);
				}
				
			} else {
				$reviews = $reviews - $finishcount;
				$sql9 = "UPDATE workshop_k SET isfinish='2' ,reviews=reviews - '".$finishcount."' ,todocount=todocount + '".$finishcount."' ,notNum=notNum+1  WHERE modid='" . $modid . "' and routeid='" . $routeid . "' ORDER by id LIMIT 1";
				$conn -> query($sql9);
				$sql10 = "UPDATE review SET reviews='".$reviews."' WHERE modid='".$modid."' and routeid='".$routeid."' and id='" . $rid . "'  ORDER by id LIMIT 1";
				$conn -> query($sql10);
			}
			$message = $name . "的" . $route .  "已评审！";
       		$sql_mes = "INSERT INTO message (content,time,department,state,workstate,route,cuser,workshop,count) VALUES ('" . $message . "','" . date("Y-m-d H:i:s") . "','".$department."','0','返工','" . $route . "','" . $writtenBy . "','" . $workshop . "','" . $finishcount . "')";
			$conn->query($sql_mes);
		} 
		//报废，默认不改变完成数量，记录检查数量作为报废数量
		else if ($inspect === "6") {
			$reviews = $reviews - $finishcount;
			$sql11 = "UPDATE workshop_k SET reviews='".$reviews."' ,dumping=dumping + '".$finishcount."' WHERE modid='" . $modid . "' and routeid='" . $routeid . "' ORDER by id LIMIT 1";
			$conn -> query($sql11);
			$sql12 = "UPDATE review SET reviews='".$reviews."'  WHERE modid='".$modid."' and routeid='".$routeid."'and id='" . $rid . "' ORDER by id LIMIT 1";
			$conn -> query($sql12);
			  // 检测当前零件不合格处理是否完成
			$sql_finish = "SELECT todocount ,reviews ,inspectcount,unqualified from workshop_k where modid='".$modid."' and routeid='".$routeid."'  ";
			$res_finish = $conn -> query($sql_finish);
			if ($res_finish -> num_rows > 0) {   
				while ($row = $res_finish -> fetch_assoc()) {
					if ($row['todocount'] == '0'  && $row['reviews'] == '0' && $row['inspectcount'] == '0'&& $row['unqualified'] == '0') {
						 // 更新route进度为完成状态
						 if($isexterior=="1"){
						 	$sql13 = "UPDATE route SET isfinish='1' where modid='" . $modid . "' and pid='" . $pid . "'  ";
			           		$conn->query($sql13);
			           		$sql14 = "UPDATE part SET isfinish='1' where modid='" . $modid . "' and fid='" . $pid . "' ORDER by id LIMIT 1 ";
			           		$conn->query($sql14);
						 }else{
						 	$sql13 = "UPDATE route SET isfinish='1' where modid='" . $modid . "' and id='" . $routeid . "' ORDER by id LIMIT 1 ";
			           		$conn->query($sql13);
						 }
			            
					}
				}
			}
//			$sql19 = "UPDATE scrap SET scrapNum=scrapNum + '" . $reviews . "'  WHERE modid='" . $modid . "' and routeid='" . $routeid . "' ORDER by id LIMIT 1";
//			$conn -> query($sql19);
			$message = $name . "的" . $route .  "已评审！";
       		$sql_mes = "INSERT INTO message (content,time,department,state,workstate,route,cuser,workshop,count) VALUES ('" . $message . "','" . date("Y-m-d H:i:s") . "','".$department."','0','报修','" . $route . "','" . $writtenBy . "','" . $workshop . "','" . $finishcount . "')";
			$conn->query($sql_mes);
		}
		// 循环检测是否所有工序完成
		$sql13 = "SELECT isfinish from workshop_k where modid='" . $modid . "' and routeid='" . $routeid . "' ";
		$res1 = $conn -> query($sql13);
		if ($res1 -> num_rows > 0) {
			while ($row1 = $res1 -> fetch_assoc()) {
				if ($row1['isfinish'] != '3') {
					// 检测如果还有未完成则终止脚本
					die();
				}
			}
			 if($isexterior=="1"){//外协
             	// 更新part进度为完成状态
             	 // 更新route进度为完成状态
             	$sql10 = "UPDATE route SET isfinish='1' where modid='" . $modid . "' and pid='" . $pid . "' ";
	            $conn->query($sql10);
                $sql12 = "UPDATE part SET isfinish='1' where modid='" . $modid . "' and fid='" . $pid . "' ORDER by id LIMIT 1 ";
                $res2 = $conn->query($sql12);
             }
             else{
             	// 更新route进度为完成状态
				$sql14 = "UPDATE route SET isfinish='1' where modid='" . $modid . "' and id='" . $routeid . "' ORDER by id LIMIT 1 ";
				$conn -> query($sql14);
	
				// 循环检测是否所有车间完成
				$sql15 = "SELECT isfinish from route where modid='" . $modid . "' and pid='" . $pid . "' ";
				$res2 = $conn -> query($sql15);
				if ($res2 -> num_rows > 0) {
					while ($row2 = $res2 -> fetch_assoc()) {
						if ($row2 ['isfinish'] != '1') {
							// 检测如果还有未完成则终止脚本
							die();
						}
					}
					// 更新part进度为完成状态
					$sql16 = "UPDATE part SET isfinish='1' where modid='" . $modid . "' and fid='" . $pid . "' ORDER by id LIMIT 1 ";
					$res2 = $conn -> query($sql16);
				}
             }
			
		}
		break;
}
?>