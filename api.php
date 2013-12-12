<?php
/**
*	Output data from social mentions
*/
// Include the toolkit for db work
require('toolkit.class.php');
$toolkit = new Toolkit;

// Get the data from the database
$sql = "SELECT * FROM mentions WHERE id > 0";
$result = $toolkit->getDBData($sql);

/*
echo var_dump($result);
exit;

// Parse into an array
$i = 0;
$data = array();
foreach($result as $key => $value) {
	if($key == 'timestamp') {
		$data[$i][$key] = date('d/m/Y', $value);
	} else {
		$data[$i][$key] = $value;
	}
    $i++;
}
*/

// TODO: Count totals for source and keyword and group by month

// Array: month in number format, then source => total key value pair.

// Parse into an array
$i = 0;
$data = array();
while($row = mysqli_fetch_assoc($result)) {
	$data[$i]['id'] = $row['id'];
	$data[$i]['title'] = $row['title'];
	$data[$i]['user'] = $row['user'];
	$data[$i]['type'] = $row['type'];
	$data[$i]['source'] = $row['source'];
	$data[$i]['timestamp'] = date('d/m/Y', $row['timestamp']);
    $i++;
}

// Output as JSON
echo json_encode($data);
?>