<?php
require_once 'sph_lib.php';

foreach( $_GET as $key => $value ) {
	msg_info($key."-----get----".$value);
}
foreach( $_POST as $key => $value ) {
	msg_info($key."-----post----".$value);
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

/*
      model:wx.getStorageSync("model"),
      mac:wx.getStorageSync("mac"),
      hw_version:wx.getStorageSync("hw_version"),
      sw_version:wx.getStorageSync("sw_version"),
      manufacturer:wx.getStorageSync("manufacturer"),
      power:wx.getStorageSync("power"),
      device_id:g_deviceId,
      uid: wx.getStorageSync('serverId'),
      config:util.objToBase64(wx.getStorageSync("config"))
*/
function query_order()
{
}
function save_all_setting()
{
	$db=db_connect();

	$statement = $db->prepare("select id from sph_devices where uid=? and device_id=?");
	$result = $statement->execute(array(get("uid"),get("device_id")));
	$config=base64_decode(get("config"));
	msg_info("config.........=".$config);
	if ( ($data=$statement->fetch(PDO::FETCH_NUM))==null){
		$statement = $db->prepare("insert into  sph_devices(uid,device_id,mac,type,sw_version,hw_version,product_type,manufacturer,power,config) values(?,?,?,?,?,?,?,?,?,?)");
		$result = $statement->execute(array(get("uid"),get("device_id"),get("mac"),"",get("sw_version"),get("hw_version"),get("model"),get("manufacturer"),get("power"),$config));
		msg_info("device insert status=".$result);
	}
	else{

		$statement = $db->prepare(" update  sph_devices set mac=?,type=?,sw_version=?,hw_version=?,product_type=?,manufacturer=?,power=?,config=? where uid=? and device_id=?");
		$result = $statement->execute(array(get("mac"),"",get("sw_version"),get("hw_version"),get("model"),get("manufacturer"),get("power"),$config,get("uid"),get("device_id")));
		msg_info("device update status=".$result);
	}

	db_close($db);

	$arr=array();
	$arr["code"]=0;
	$arr["msg"]="succeed";
	die(json_encode($arr));
}
if (get("action")=="save_all_setting"){	
	save_all_setting();
}
else if (get("action")=="save_step_data"){
}
else
	die("unkown action command!".date("Y-m-d"));
?>
