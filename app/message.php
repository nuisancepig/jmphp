<?php
require ("../conn.php");
	$flag = $_POST["flag"];
	$department =$_POST["department"];
//$flag = "0";
//	$department ='检验部';

switch ($flag) {
	case '0' :
	if($department='检验部')
	$sql1="SELECT * FROM message WHERE (workstate='完工' or workstate='检验') and state='0' ORDER BY time DESC";
	$res = $conn -> query($sql1);
	if ($res -> num_rows > 0) {
		$i = 0;
		while ($row = $res -> fetch_assoc()) {
			$arr[$i]['content'] = $row['content'];
			$arr[$i]['time'] = $row['time'];
			$arr[$i]['workstate'] = $row['workstate'];
			$arr[$i]['route'] = $row['route'];
			$arr[$i]['workshop'] = $row['workshop'];
			$arr[$i]['count'] = $row['count'];
			$arr[$i]['id'] = $row['id'];
			$i++;
		}
	}
	else{
	$sql2="SELECT * FROM message WHERE workstate!='完工' and workstate!='检验' and state='0' ORDER BY time DESC";
	$res = $conn -> query($sql2);
	if ($res -> num_rows > 0) {
		$i = 0;
		while ($row = $res -> fetch_assoc()) {
			$arr[$i]['content'] = $row['content'];
			$arr[$i]['time'] = $row['time'];
			$arr[$i]['workstate'] = $row['workstate'];
			$arr[$i]['route'] = $row['route'];
			$arr[$i]['workshop'] = $row['workshop'];
			$arr[$i]['count'] = $row['count'];
			$arr[$i]['id'] = $row['id'];
			$i++;
		}
	}
	}
	$json = json_encode($arr);
    echo $json;
	break;
	case '1' :
	if($department='检验部')
	$sql1="SELECT * FROM message WHERE (workstate='完工' or workstate='检验') and state='1' ORDER BY utime DESC";
	$res = $conn -> query($sql1);
	if ($res -> num_rows > 0) {
		$i = 0;
		while ($row = $res -> fetch_assoc()) {
			$arr[$i]['content'] = $row['content'];
			$arr[$i]['time'] = $row['time'];
			$arr[$i]['workstate'] = $row['workstate'];
			$arr[$i]['route'] = $row['route'];
			$arr[$i]['workshop'] = $row['workshop'];
			$arr[$i]['count'] = $row['count'];
			$arr[$i]['id'] = $row['id'];
			$i++;
		}
	}
	else{
	$sql2="SELECT * FROM message WHERE workstate!='完工' and workstate!='检验' and state='1' ORDER BY utime DESC";
	$res = $conn -> query($sql2);
	if ($res -> num_rows > 0) {
		$i = 0;
		while ($row = $res -> fetch_assoc()) {
			$arr[$i]['content'] = $row['content'];
			$arr[$i]['time'] = $row['time'];
			$arr[$i]['workstate'] = $row['workstate'];
			$arr[$i]['route'] = $row['route'];
			$arr[$i]['workshop'] = $row['workshop'];
			$arr[$i]['count'] = $row['count'];
			$arr[$i]['id'] = $row['id'];
			$i++;
		}
	}
	}
	$json = json_encode($arr);
    echo $json;
	break;
	case '2' :
	$messageid =$_POST["messageid"];
	$sql1="UPDATE message SET state = '1' WHERE id = $messageid";
	$res = $conn -> query($sql1);
	$json = json_encode($res);
    echo $json;
	break;
	case '3' :
	$messageid =$_POST["messageid"];
	$sql1="DELETE FROM message WHERE id = $messageid";
	$res = $conn -> query($sql1);
	$json = json_encode($res);
    echo $json;
	break;
}
?>