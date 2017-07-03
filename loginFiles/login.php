<?php
	$userID = $_POST['username'];
	$p1 = null;
	if ( isset($_POST['p1']) ) {
		$p1 = $_POST['p1'];
	};
	$otp = null;
	if ( isset($_POST['otp']) ) {
		$otp = $_POST['otp'];
	};
	
	$database = new Database("localhost", "root", "", "myDB");
	if ($otp == null) {
		$row = $database->retrieve($userID, $p1);
		if ($row == null) {
			echo "Login Unsuccessful";
		} else {
			$responseString = "<div id='id02' class='w3-modal'><div class='w3-modal-content'><div class='w3-container' style='text-align: center; font-size: 18px;'><p style='padding: 20px 50px;'>Please enter the OTP sent to your phone/email.</p><input type='text' id='otp' style='font-size: 20px; padding: 10px 50px; text-align: center; margin-bottom: 20px;'/><button id='generateBtn'>Login <i id='sendIcon' class='material-icons'>send</i></button></div></div></div>:::" . (new OTP)->generateOTP($row["username"]);
			echo $responseString;
		}

	} else {
		$row = $database->retrieveOTP($userID, $otp);
		
		if ($row == null) {
			echo "Login unsuccessful";
		} else {
			echo "Logged in as " . $row["accountType"] . "!";
		}

	}
	
	
	$database->close();
	
	class Database {
		// Connect to database
		private $servername;
		private $username;
		private $password;
		private $dbname;
		private $conn;
		
		public function __construct($servername, $username, $password, $dbname) {
			$this->servername = $servername;
			$this->username = $username;
			$this->password = $password;
			$this->dbname = $dbname;
			
			$this->connect();
		}
		
		private function connect() {
			$this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
				if ($this->conn->connect_error) exit("Connection to database failed: " . $this->conn->connect_error);
		}
		
		public function query() {
			if (func_num_args() > 1) {
				$this->multiQuery(func_get_args());
			} else if (func_num_args() == 0) {
				exit("Invalid number of arguments passed in: 'Method expects at least 1 argument but 0 arguments were passed'");
			} else {
				$this->conn->query(func_get_arg(0));
					if ($this->conn->query(func_get_arg(0)) != TRUE) exit("<br/>Query error: " . $this->conn->error);
			}
		}
		
		private function multiQuery() {
			if (func_num_args <= 0) exit("Invalid number of arguments passed in: 'Method expects at least 1 argument but 0 arguments were passed'");
			
			for ($i = 0; $i < func_num_args; $i++) {
				$this->conn->query(func_get_arg($i));
					if ($this->conn->query(func_get_arg($i)) != TRUE) exit("<br/>Query error: " . $this->conn->error);
			}
		}
		
		public function retrieve($userID, $p1) {
			$retrieveData = "SELECT * FROM `admin` WHERE username='$userID' AND passwd='$p1';";
				if ($this->conn->query($retrieveData) != TRUE) exit("<br/>Error retrieving data: " + $this->conn->error);
			
			$row = $this->conn->query($retrieveData)->fetch_assoc();
			return $row;
		}
		
		public function retrieveOTP($userID, $otp) {
			$retrieveData = "SELECT * FROM `admin` WHERE username='$userID' AND otp='$otp';";
				if ($this->conn->query($retrieveData) != TRUE) exit("<br/>Error retrieving data: " + $this->conn->error);
			
			$row = $this->conn->query($retrieveData)->fetch_assoc();
			return $row;
		}
		
		public function close() {
			if ($this->conn == true) $this->conn->close();
		}
	}
	
	final class OTP {
		private $string = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		private $string_shuffled;
		
		public function generateOTP($username) {
			$this->string_shuffled = str_shuffle($this->string);
			$otp = substr(bin2hex( base64_encode( substr($this->string_shuffled, 0, 5) ) ), 0, 6);
			$query = mysql_query("UPDATE `admin` SET otp='".$otp."' WHERE username='".$username."';");
			return substr(bin2hex( base64_encode( substr($this->string_shuffled, 0, 5) ) ), 0, 6);
		}
	}
?>