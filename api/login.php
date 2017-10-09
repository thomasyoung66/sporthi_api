<?php
require_once 'sph_lib.php';

foreach( $_GET as $key => $value ) {
	msg_info($key."-----get----".$value);
}
foreach( $_POST as $key => $value ) {
	msg_info($key."-----post----".$value);
}


function get_open_id()
{
	$url='https://api.weixin.qq.com/sns/jscode2session?appid='.WX_APP_ID .'&secret='.WX_APP_KEY.'&js_code='.get("code").'&grant_type=authorization_code';
	$res=http_post($url,null);
	msg_info("====".$res);
	echo $res;
}
function get_match_rule()
{
	$arr=array();

	$item=array();
	$item["brand"]="Koomii";	
	$item["match_type"]="sid";
	$item["corp"]="张国盛公司";
	$item["icon"]="launch_sop.png";
	$item["heart"]=1;
	$item["sid"]="F7903;F7902";
	$item["sidfield"]="name";
	$item["parser"]="ble_koomii";
	array_push($arr,$item);

	$item=array();
	$item["brand"]="mj";	
	$item["match_type"]="sid";
	$item["corp"]="mj";
	$item["icon"]="mj.png";
	$item["sidfield"]="localName";
	$item["heart"]=1;
	$item["sid"]="M5";
	$item["parser"]="ble_movnow";
	array_push($arr,$item);

	$item=array();
	$item["brand"]="movnow";	
	$item["match_type"]="sid";
	$item["corp"]="movnow";
	$item["icon"]="movnow.png";
	$item["sidfield"]="localName";
	$item["heart"]=0;
	$item["sid"]="W007L";
	$item["parser"]="ble_movnow";
	array_push($arr,$item);
	return $arr;
}
function save_userinfo()
{
	/* {"openid":"oL_ce0fULctebi2CkVqKux80-OR0",
	   "expires_in":1504657200935}
	   userInfo={"nickName":"66顺",
	   "gender":1,
	   "language":"zh_CN",
	   "city":"Guangzhou",
	   "province":"Guangdong",
	   "country":"China",
	   "avatarUrl":"https://wx.qlogo.cn/mmopen/vi_32/Bgd6dlF2gqDDLznmPLMvDw1R2VsGHt4nwvwtDewc0DWxNF9Tx9CSDtHMDjYuu96Qaq9oXC3OTJDczSGnbnyJzQ/0"}
	 */
	$user=json_decode(base64_decode(get("user")),true);
	$userInfo=json_decode(base64_decode(get("userInfo")),true);

	msg_info("user=".json_encode($user)." userInfo=".json_encode($userInfo));
	$db=db_connect();

	$statement = $db->prepare("select * from sph_users where open_id=?");
	$result = $statement->execute(array($user["openid"]));

	$id=0;
	$dev="";
	$dest=7000;
	$util=0;
	$height=170;
	$weight=70;
	$phone="";
	$gender=0;
	if ($statement->fetch(PDO::FETCH_NUM)==null){
		$statement = $db->prepare("insert into sph_users(open_id,nickname,gender,language,city,province,country,avatar_url,create_time,last_login_time,login_sums) values(?,?,?,?,?,?,?,?,?,?,?)");
		$arr=array($user["openid"],$userInfo["nickName"],$userInfo["gender"],
			$userInfo["language"],$userInfo["city"],$userInfo["province"],$userInfo["country"],
			$userInfo["avatarUrl"], date('Y-m-d H:i:s'),date('Y-m-d H:i:s'),1);
		$result = $statement->execute($arr);
		msg_info(json_encode($arr)." result=".$result);
		$id=$db->lastInsertId();
	}
	else{
		$statement = $db->prepare("update sph_users set nickname=?,gender=?,language=?,city=?,province=?,country=?,avatar_url=?,last_login_time=?,login_sums=login_sums+1 where open_id=?");
		$result = $statement->execute(array($userInfo["nickName"],$userInfo["gender"],
			$userInfo["language"],$userInfo["city"],$userInfo["province"],$userInfo["country"],
			$userInfo["avatarUrl"], date('Y-m-d H:i:s'),$user["openid"]));
		//msg_info($user["openid"]." update result=".$result);

		$statement = $db->prepare("select id,devices,step_dest,metering_type,height,weight,phone,gender from sph_users where open_id=?");
		$result = $statement->execute(array($user["openid"]));
		$data = $statement->fetch(PDO::FETCH_NUM);
		$id=$data[0];
		//msg_info("user....".$user["openid"]."....".$id);
		if ($data[1]!=null && $data[1]!=''){
			$dev=$data[1];
			$dest=$data[2];
			$util=$data[3];
			$height=$data[4];
			$weight=$data[5];
			$phone=$data[6];
			$gender=$data[7];
		}
	}
	$arr=array();
	$arr["code"]=0;
	$arr["msg"]="succeed";
	$arr["id"]=$id;
	$arr["dest"]=$dest;
	$arr["util"]=$util;
	$arr["height"]=$height;
	$arr["weight"]=$weight;
	$arr["phone"]=$phone;
	$arr["gender"]=$gender;
	$statement = $db->prepare("select * from sph_devices where uid=?");
	$result = $statement->execute(array($id));
	
	$arr["devices"]=$statement->fetchAll(PDO::FETCH_CLASS);
	$arr["match_rule"]=get_match_rule();
	msg_info(json_encode($arr));
	die(json_encode($arr));

}
if (get("action")=="get_open_id"){
	get_open_id();
}
else if (get("action")=="save_userinfo"){
	save_userinfo();
}
else
	die("unkown action command!");
?>
