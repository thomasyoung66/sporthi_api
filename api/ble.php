<?php
require_once 'sph_lib.php';

foreach( $_GET as $key => $value ) {
	msg_info($key."-----get----".$value);
}
foreach( $_POST as $key => $value ) {
	msg_info($key."-----post----".$value);
}


function update_device()
{

/*
id: wx.getStorageSync('serverId'),
deviceId: serviceId,
brand:item.brand,
crop: item.corp
*/

	$db=db_connect();

	$arr=array();
	$statement = $db->prepare("select id,devices from sph_users where id=?");
	$result = $statement->execute(array(get("id")));
	if ( ($data=$statement->fetch(PDO::FETCH_NUM))==null){
		$res=array();
		$res["code"]=1;
		$res["msg"]="succeed";
		die(json_encode($res));
	}
	else{
		if ($data[1]==null||$data[1]==''){
			$item=array();
			$item["deviceId"]=get("deviceId");
			$item["brand"]=get("brand");
			$item["corp"]=get("corp");
			$item["select"]=1;
			array_push($arr,$item);
		}
		else{
			$arr=json_decode($data[1],true);	
			for($n=0;$n<count($arr);$n++){
				$arr[$n]["select"]=0;
				if ($arr[$n]["deviceId"]==get("deviceId")){
					$arr[$n]["deviceId"]=get("deviceId");
					$arr[$n]["brand"]=get("brand");
					$arr[$n]["corp"]=get("corp");
					$arr[$n]["name"]=get("name");
					$arr[$n]["select"]=1;
				}
			}
		}
		msg_info("found====");
		$statement = $db->prepare("update sph_users set devices=? where id=?");
		$result = $statement->execute(array(json_encode($arr),get("id")));
	}
	$res=array();
	$res["code"]=0;
	$res["msg"]="succeed";
	$res["devices"]=$arr;
	msg_info(json_encode($res));
	die(json_encode($res));

}
function save_step_data()
{
	$step_obj=json_decode(base64_decode(get("step_data")),true);
	$sleep_obj=json_decode(base64_decode(get("sleep_data")),true);

	$db=db_connect();
	foreach($step_obj as $obj=> $val){
		msg_info("insert..........".$obj);
		$statement = $db->prepare("select id from step_history where uid=? and sport_date=?");
		$result = $statement->execute(array(get("uid"),$obj));
		if ( ($data=$statement->fetch(PDO::FETCH_NUM))==null){
			$statement = $db->prepare("insert into  step_history(uid,sport_date,total_step,step_json) values(?,?,?,?)");
			$result = $statement->execute(array(get("uid"),$obj,$val["step"],json_encode($val)));
			msg_info($obj." step insert status=".$result);
		}
	}
	msg_info("step data=".json_encode($step_obj));
	msg_info("sleep data=".json_encode($sleep_obj));
	foreach($sleep_obj as $obj=>$val){
		msg_info("sleep----".$obj);
		$statement = $db->prepare("select id from step_history where uid=? and sport_date=?");
		$result = $statement->execute(array(get("uid"),$obj));
		if ( ($data=$statement->fetch(PDO::FETCH_NUM))!=null){
			$statement = $db->prepare("update step_history set sleep_json=? where uid=? and sport_date=?");
			$result = $statement->execute(array(json_encode($val),get("uid"),$obj));
			msg_info($obj." sleep update status=".$result);
		}
	}


	$arr=array();
	$arr["code"]=0;
	$arr["msg"]="succeed";
	die(json_encode($arr));
}
function save_util()
{
	$db=db_connect();
	$statement = $db->prepare("update sph_users set metering_type=? where id=?");
	$result = $statement->execute(array(get("util"),get("uid")));
	db_close($db);

	$arr=array();
	$arr["code"]=0;
	$arr["msg"]="succeed";
	die(json_encode($arr));
	return ;
}
function save_dest()
{

	$db=db_connect();
	$statement = $db->prepare("update sph_users set step_dest=? where id=?");
	$result = $statement->execute(array(get("dest"),get("uid")));
	db_close($db);

	$arr=array();
	$arr["code"]=0;
	$arr["msg"]="succeed";
	die(json_encode($arr));
	return ;
}
function save_phone()
{
	$db=db_connect();
	$statement = $db->prepare("update sph_users set phone=? where id=?");
	$result = $statement->execute(array(get("phone"),get("uid")));
	db_close($db);

	$arr=array();
	$arr["code"]=0;
	$arr["msg"]="succeed";
	die(json_encode($arr));
}
function save_height()
{
	$db=db_connect();
	$statement = $db->prepare("update sph_users set height=? where id=?");
	$result = $statement->execute(array(get("height"),get("uid")));
	db_close($db);

	$arr=array();
	$arr["code"]=0;
	$arr["msg"]="succeed";
	die(json_encode($arr));
}
function save_weight()
{
	$db=db_connect();
	$statement = $db->prepare("update sph_users set weight=? where id=?");
	$result = $statement->execute(array(get("weight"),get("uid")));
	db_close($db);

	$arr=array();
	$arr["code"]=0;
	$arr["msg"]="succeed";
	die(json_encode($arr));
}
function query_order()
{
	$db=db_connect();
	$statement = $db->prepare("SELECT b.id as uid,a.id as hid,b.city,b.nickname,b.gender,b.avatar_url,a.total_step,a.praises  FROM step_history a,sph_users b WHERE a.uid=b.id and a.sport_date=? ORDER BY total_step  DESC LIMIT 100");
	$result = $statement->execute(array(date("Y-m-d")));
	$allData = $statement->fetchAll(PDO::FETCH_CLASS);
	$allData=json_decode(json_encode($allData),true);
	for($n=0;$n<count($allData);$n++){
		$allData[$n]["id"]=$n;
		$statement = $db->prepare("select id from praise_list where history_id=?");
		$result = $statement->execute(array($allData[$n]["hid"]));
		if ( ($data=$statement->fetch(PDO::FETCH_NUM))==null)
			$allData[$n]["pf"]=0;
		else
			$allData[$n]["pf"]=1;
	}
	db_close($db);

	$arr=array();
	$arr["code"]=0;
	$arr["msg"]="succeed";
	$arr["items"]=$allData;
	die(json_encode($arr));
}
function query_history()
{
	$db=db_connect();
	$statement = $db->prepare("SELECT sport_date,total_step FROM step_history WHERE uid=? ORDER BY sport_date DESC LIMIT 30");
	$result = $statement->execute(array(get("uid")));
	$allData = $statement->fetchAll(PDO::FETCH_CLASS);

	$allData=json_decode(json_encode($allData),true);
		
	db_close($db);

	$arr=array();
	$arr["code"]=0;
	$arr["msg"]="succeed";
	$arr["items"]=$allData;
	msg_info("data...".json_encode($arr));
	die(json_encode($arr));
}
function save_today_step_data()
{
	$val=json_decode(base64_decode(get("step_data")),true);
	$db=db_connect();
	$statement = $db->prepare("select id from step_history where uid=? and sport_date=?");
	$result = $statement->execute(array(get("uid"),get("run_date")));
	if ( ($data=$statement->fetch(PDO::FETCH_NUM))==null){
		$statement = $db->prepare("insert into  step_history(uid,sport_date,total_step,step_json) values(?,?,?,?)");
		$result = $statement->execute(array(get("uid"),get("run_date"),$val["step"],json_encode($val)));
		msg_info($obj." step insert status=".$result);
	}
	else{
		$statement = $db->prepare("update step_history set total_step=?,step_json=? where uid=? and sport_date=?");
		$result = $statement->execute(array($val["step"],json_encode($val),get("uid"),get("run_date")));
		msg_info($obj." step update status=".$result);
	}
	db_close($db);
}
function praise()
{
	$db=db_connect();
	$statement = $db->prepare("SELECT * from praise_list where history_id=? and uid=?");
	$result = $statement->execute(array(get("id"),get("uid")));
	if ( ($data=$statement->fetch(PDO::FETCH_NUM))==null){
		$statement = $db->prepare("insert into praise_list(history_id,uid,ptime) values(?,?,?)");
		$result = $statement->execute(array(get("id"),get("uid"),date("Y-m-d H:i:s")));
		msg_info($obj." insert praise_list  status=".$result);

		$statement = $db->prepare("update step_history set praises=praises+1 where id=?");
		$result = $statement->execute(array(get("id")));
		msg_info($obj." update step_history+1  status=".$result);

	}
	else{
		$statement = $db->prepare("delete from praise_list where history_id=? and uid=?");
		$result = $statement->execute(array(get("id"),get("uid")));
		msg_info($obj." delete praise_list  status=".$result);

		$statement = $db->prepare("update step_history set praises=praises-1 where id=?");
		$result = $statement->execute(array(get("id")));
		msg_info($obj." update step_history-11  status=".$result);
	}
	db_close($db);
	$arr=array();
	$arr["code"]=0;
	$arr["msg"]="succeed";
	die(json_encode($arr));

}
function init_user()
{
	if (get("uid")=="")
		die("uid is must...");
	$db=db_connect();
	$statement = $db->prepare("delete from sph_users where id=?");
	$result = $statement->execute(array(get("uid")));
	echo "delete sph_users status:".$result."</p>";	
	$statement = $db->prepare("delete from step_history where uid=?");
	$result = $statement->execute(array(get("uid")));
	echo "delete step_history status:".$result."</p>";	
	$statement = $db->prepare("delete from sph_devices where uid=?");
	$result = $statement->execute(array(get("uid")));
	echo "delete sph_devices status:".$result."</p>";	
	db_close($db);
	echo "=========end==========";
}
if (get("action")=="update_device"){
	update_device();
}
else if (get("action")=="save_step_data"){
	save_step_data();
}
else if (get("action")=="save_util"){
	save_util();
}
else if (get("action")=="save_dest"){
	save_dest();
}
else if (get("action")=="save_phone"){
	save_phone();
}
else if (get("action")=="save_height"){
	save_height();
}
else if (get("action")=="save_weight"){
	save_weight();
}
else if (get("action")=="query_order"){
	query_order();
}
else if (get("action")=="query_history"){
	query_history();
}
else if (get("action")=="praise"){
	praise();
}
else if (get("action")=="save_today_step_data"){
	save_today_step_data();
}
else if (get("action")=="init_user"){
	init_user();
}
else
	die("unkown action command!".date("Y-m-d"));
?>
