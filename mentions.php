<?php
/**
*	Get data daily from socialmention.com api
*	and add to database
*
*	Maybe extend to: https://www.googleapis.com/customsearch/v1?key=AIzaSyBqyBaFQvuakhnz3B6NtNEySHLuU9FRK1g&cx=003518754598116034377:laglkpvhxn4&q=bigfoot
*	And: https://developers.google.com/image-search/v1/devguide
*/
// Include the toolkit for db work
require('toolkit.class.php');
$toolkit 	= new Toolkit;

$hoax 		= array('bigfoot','tablets','mermaid');
$hoaxes 	= array(
					array('bigfoot','sasquatch','yeti','abominable snowman'), // Include all the pseudonyms here for each one
					array('prediction tablets','prophecy carvings'),
					array('fishman','mermaid')
					);

echo 'Started...'.PHP_EOL;

for($n=0;$n<count($hoax);$n++) {
	$keywords = $hoaxes[$n];
	for($j=0;$j<count($hoaxes[$n]);$j++) {
		$url = 'http://api2.socialmention.com/search?q='.$hoaxes[$n][$j].'&f=json&t[]=all&lang=en&strict=false';
 
		//  Initiate curl
		$ch = curl_init();
		// Disable SSL verification
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		// Will return the response, if false it print the response
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// Set the url
		curl_setopt($ch, CURLOPT_URL,$url);
		// Execute
		$result = curl_exec($ch);
		$data = json_decode($result,true);

		// Loop through the data and add to the database
		for($i=0;$i<$data['count'];$i++) {
			$title		= $data['items'][$i]['title'];
			$user		= $data['items'][$i]['user'];
			$user_image	= $data['items'][$i]['user_image'];
			$user_link	= $data['items'][$i]['user_link'];
			$link		= $data['items'][$i]['link'];
			$image		= $data['items'][$i]['image'];
			$embed		= $data['items'][$i]['embed'];
			$type		= $data['items'][$i]['type'];
			$source		= $data['items'][$i]['source'];
			$favicon	= $data['items'][$i]['favicon'];
			$timestamp 	= $data['items'][$i]['timestamp'];
			$datetime	= date('l jS F Y H:i:s', $timestamp);
			$hash		= md5($title.$user);
		
			// if timestamp exists, save to db - we want to graph them
			if(strlen($timestamp) > 1) {		
				$sql = 'INSERT INTO mentions VALUES("","'.$title.'","'.$user.'","'.$user_image.'","'.$user_link.'","'.$link.'","'.$image.'","'.$embed.'","'.$type.'","'.$source.'","'.$favicon.'","'.$timestamp.'","'.$datetime.'","'.$hoax[$n].'","'.$hash.'")';
				$toolkit->updateDB($sql);
			}
			
			// Update the cli output
			echo '.';
		}
	}
	
	echo PHP_EOL.ucfirst($hoax[$n]).' complete.'.PHP_EOL;
}

echo 'Finished collecting data.'.PHP_EOL;

echo 'Parsing all data into JSON file...'.PHP_EOL;

$data = array();

for($i=0;$i<count($hoax);$i++) {
	$sql = 'SELECT m.timestamp AS x, COUNT(*) AS y, DATE_FORMAT(FROM_UNIXTIME(m.timestamp), "%m") AS month, DATE_FORMAT(FROM_UNIXTIME(m.timestamp), "%Y") AS year FROM mentions AS m WHERE hoax = "'.$hoax[$i].'" AND m.timestamp > 1372636800 GROUP BY month ORDER BY year, month';
	$q = $toolkit->getDBData($sql);

	if($q !== FALSE) {
		$j = 0;
		while($row = mysqli_fetch_array($q)) {
			$data[$hoax[$i]][$j]['x'] = (int)$row['x'];
			$data[$hoax[$i]][$j]['y'] = (int)$row['y'];
			$j++;
		}
	}
}

if(file_put_contents('/data/tracking.json',json_encode($data))) {
	echo 'JSON saved.'.PHP_EOL;
} else {
	echo 'JSON may not have been saved, please verify.'.PHP_EOL;
}