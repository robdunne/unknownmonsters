<?php
/*
*	Rob Dunne
*	PHP Toolkit Class
*	Some commonly needed methods for when a framework is overkill
*	September 2013 onwards
*
*	Usage: 
*	Include class file
*	$toolkit = new Toolkit;
*	$toolkit->method();
*/

class Toolkit {
	function __construct() {
		// Anything needed on initialisation
		
		// Uncomment to connect the database
		$this->connectDB();
	}

	function connectDB() {
		$ra			= $_SERVER['REMOTE_ADDR'];
		$server 	= 'localhost';
		$username 	= ($ra == '::1' ? 'root' : 'username');
		$password 	= ($ra == '::1' ? 'bitnami' : 'password');
		$database 	= ($ra == '::1' ? 'um_social' : 'database');
		
		$this->db = new mysqli($server, $username, $password, $database) or die('no connection');	
	}
	
	function getDBData($sql) {
		// Do something to the database
		if ($this->db->connect_errno) { echo "Failed to connect to MySQL: (" . $this->db->connect_errno . ") " . $this->db->connect_error; }
		
		$response = $this->db->query($sql);
		return $response;
	}

	function updateDB($sql) {
		// Do something to the database
		if ($this->db->connect_errno) { echo "Failed to connect to MySQL: (" . $this->db->connect_errno . ") " . $this->db->connect_error; }
		
		$response = ($this->db->query($sql) ? TRUE: FALSE);
		return $response;
	}

	function cleanInput($input) {
		// Pass the $_GET, $_POST or $_REQUEST array before any database work
		$output = array();
		
		foreach ($input as $key=>$value) {
			$o = $value;
			
			// Make sure it's within the max length for the database
			$o = substr($o,0,256);
			
			// Tidy up line breaks
			$o = preg_replace('/\n{2,}/', "\n\n", $o);
			$o = nl2br($o,FALSE);
		
			// Strip any odd characters
			$o = preg_replace('/[^A-Za-z0-9\. -\!\?\(\)\<\>]/', "", $o);
			
			// Escape strings
			$o = $this->db->escape_string($o);
			
			// Put the data back in the array
			$output[$key] = $o;
		}
		
		// Return the array
		return $output;
	}
	
	function sendEmail($to,$subject,$message) {
		/*
		*	Sends an HTML email
		*	$to			- email address, csv for multiple addresses
		*	$subject	- Subject string
		*	$message	- HTML message
		*/
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$response = mail($to, $subject, $message, $headers);
		
		return $response;
	}
}
