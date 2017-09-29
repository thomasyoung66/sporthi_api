<?php

require_once '../sph_lib.php';

$db=db_connect();

   $query = "select * from test";
   $statement = $db->prepare($query);
   $result = $statement->execute();

	while(( $data = $statement->fetch(PDO::FETCH_NUM))){
		echo $data[0]."\r\n";
	}

db_close($db);
msg_info("ok...");
?>
