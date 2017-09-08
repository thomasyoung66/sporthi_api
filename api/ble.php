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
if (get("action")=="update_device"){
	update_device();
}
else
	die("unkown action command!");
?>
