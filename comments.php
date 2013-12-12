<?php
// Include the toolkit for db work
require('toolkit.class.php');
$toolkit = new Toolkit;
$t		 = time()-86400;
$sql 	 = 'SELECT * FROM mentions WHERE timestamp > '.$t;

$q = $toolkit->getDBData($sql);

$data = array();
$i = 0;

while($row = mysqli_fetch_array($q)) {
	// Sort data here
	$data[$i]['id'] 			= $row['id'];
	$data[$i]['user'] 			= $row['user'];
	$data[$i]['user_image'] 	= $row['user_image'];
	$data[$i]['user_link'] 		= $row['user_link'];
	$data[$i]['link'] 			= $row['link'];
	$data[$i]['image'] 			= $row['image'];
	$data[$i]['title'] 			= $row['title'];
	$data[$i]['type'] 			= $row['type'];
	$data[$i]['source'] 		= $row['source'];
	$data[$i]['favicon'] 		= $row['favicon'];
	$data[$i]['timestamp'] 		= $row['timestamp'];
	$data[$i]['datetime'] 		= $row['datetime'];
		
	$i++;
}

echo json_encode($data);
?>