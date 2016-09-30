<?php
//////////////////////////////////////////////////////////////////////////////////////////////
/*
*	Author: 			Ryan Conklin
*	Date:				9/14/2016
*	Email:				rwc4@pct.edu
*	Repository:			https://github.com/ryanc16/PHP-PDO_MySQL
*	Original filename:	pdo_mysql_class.php
*	Version:			1.0
*	Description: This is a PDO based, easy to use, nearly drop-in replacement for mysqli_* 
*	functions that allows for a more secure interaction with a MySQL database in a website
*	or web application that runs PHP. This will allow anyone to develop applications
*	that utilize a MySQL database to have access to prepared statements and the accompanying
*	functionality that is needed in order to build their application with a
*	security-oriented approach.
*	Note: This is an object-oriented implementation of this library and its contained functions.
*/
//////////////////////////////////////////////////////////////////////////////////////////////
class PDO_MySQL{
	
	private $USER_SET_ERR_MODE = false;
	private $ERR_MODE;
	private $ERR_MODES = 
	[
		"SILENT"=>PDO::ERRMODE_SILENT,
		"WARNING"=>PDO::ERRMODE_WARNING,
		"EXCEPTION"=>PDO::ERRMODE_EXCEPTION,
		0=>PDO::ERRMODE_SILENT,
		1=>PDO::ERRMODE_WARNING,
		2=>PDO::ERRMODE_EXCEPTION
	];
	private $TESTING=false;//set to false as fail-safe;
	private $DB,$HOSTNAME,$USERNAME,$PASSWORD,$DATABASE;
	public function __construct(){
		
	}
/*
///////////////
// IMPORTANT //
///////////////
Use this for setting wether this is being used in a testing environment or a production/deployed environment.
If testing is set to true, warning and error messages will be shown in the browser.
Otherwise, when set to false, as would be desired of a production or deployed environment, no warning or error messages will be shown.
*/
	public function set_testing($istesting){
		$this->TESTING = $istesting;
	}
/*
Manually overrides the the error mode to one of the predefined error modes.
//// NOTE /////
Will do this regardless if testing has been set to true or false for testing or 
production/deployed environment. This means the user will have to try/catch their own exceptions!
Returns true if a valid mode is used and throws exception if error mode is not silent.
If a database connection is already present, it will set the connection to the new mode immediately.
Otherwise it will set it upon first successful connection.
$mode = a string or number representation of the error mode to be used from
the $ERR_MODES array. Valid values are "SILENT" or 0, "WARNING" or 1, "EXCEPTION" or 2.
*/
	public function set_err_mode($mode){
		try{
			$mode = strtoupper($mode);
		if(array_key_exists($mode,$this->ERR_MODES)){
			$this->ERR_MODE = $this->ERR_MODES[$mode];
			if($this->is_connected())
				$this->DB->setAttribute(PDO::ATTR_ERRMODE, $this->ERR_MODE);
			$this->USER_SET_ERR_MODE = true;
			return true;
		}
		else{throw new Exception("Unknown err_mode given.");}
		}
		catch(Exception $e){$this->handle_exception($e);}
		return false;
	}
/*
Resumes normal error mode determination, displaying, and exception throwing.
*/
	public function set_err_mode_default(){
		$this->USER_SET_ERR_MODE = false;
		$this->reconnect();
	}
/*
Use this for creating your connection.
Returns true if connection is successful and false if it is not.
$host = url/address of database server.
$user = username of database user.
$pass = password for the user.
$database = name of the database.
*/
	public function connect($host,$user,$pass,$database){
		if(isset($this->DB)) unset($this->DB);
		$this->HOSTNAME = $host;
		$this->USERNAME = $user;
		$this->PASSWORD = $pass;
		$this->DATABASE = $database;
		if(!$this->USER_SET_ERR_MODE){
			if($this->TESTING)
				$this->ERR_MODE = $this->ERR_MODES['EXCEPTION'];
			else
				$this->ERR_MODE = $this->ERR_MODES['SILENT'];
		}
		
		try{
			$this->DB = new PDO('mysql:host='.$this->HOSTNAME.';dbname='.$this->DATABASE.';charset=utf8', $this->USERNAME, $this->PASSWORD);
			$this->DB->setAttribute(PDO::ATTR_ERRMODE, $this->ERR_MODE);
			$this->DB->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			return true;
		}
		catch(Exception $e){return $this->handle_exception($e);}
		return false;
	}
/*
Returns the PDO class itself for if/when more methods and functionality is desired
*/
	function pdo_self(){
		return $this->DB;
	}
/*
This is a static constructor that can be used for one line instantiation.
Ex: $pdo = PDO_MySQL::create_connection("localhost","username","password","db");
$host = url/address of database server.
$user = username of database user.
$pass = password for the user.
$database = name of the database.
*/
	public static function create_connection($host,$user,$pass,$database){
		$instance = new PDO_MySQL();
		$instance->connect($host,$user,$pass,$database);
		return $instance;
	}
/*
Returns a boolean which determins whether there is a current connection to a database;
*/
	public function is_connected(){
		if(isset($this->DB))
			return true;
		else return false;
	}
/*
Sets the host to the new value given to the function.
Disconnects the current session.
$hostname = url/address of database server.
*/
	public function change_host($hostname){
		if(isset($this->DB)) unset($this->DB);
		$this->HOSTNAME = $hostname;
	}
/*
Unsets (disconnects) the current user from the database connection and sets
the user to a new one that will be used to create a new connection.
$username = username of database user.
$password = password for the user.
*/
	public function change_user($username,$password){
		if(isset($this->DB)) unset($this->DB);
		$this->USERNAME = $username;
		$this->PASSWORD = $password;
	}
/*
Sets the database name to the new value given to the function.
Disconnects the current session.
$database = name of the database.
*/
	public function change_database($database){
		if(isset($this->DB)) unset($this->DB);
		$this->DATABASE = $database;
	}
/*
Creates a new PDO object and attempts a connection using the current
values of HOSTNAME, USERNAME, PASSWORD, and DATABASE.
Returns true if connection is successful and false if it is not.
Useful for reconnecting after using change_host(), change_user(), or
change_database().
Disconnects current connection if already connected.
*/
	public function reconnect(){
		return $this->connect($this->HOSTNAME,$this->USERNAME,$this->PASSWORD,$this->DATABASE);
	}

/*
Uses current connection to prepare a SQL query, then queries the database.
Returns a multi-dimenstional array as a result set to be used with other functions
that use result sets.
$sql is the sql string to be prepared and queried.
$valuesIN is an array containing values to be queried in place of the values in the prepared statement.
$array_type = NUM, ASSOC or BOTH. Defaults to ASSOC
*/
	public function query_prepared($sql,$valuesIN=array(),$array_type='ASSOC'){
		try{
		if(!$this->is_connected()){throw new Exception("No current connection to database!");}
		if($array_type=='ASSOC')
			$array_type = PDO::FETCH_ASSOC;
		else if($array_type=='NUM')
			$array_type = PDO::FETCH_NUM;
		else $array_type = PDO::FETCH_BOTH;
		$stmt = $this->DB->prepare($sql);
		$stmt->execute($valuesIN);
		if(!$stmt) return false;
		$valuesOUT = array();
		$i = 0;
		while($row = $stmt->fetch($array_type)){
			$valuesOUT[$i] = $row;
			$i++;
		}
		return $valuesOUT;
		}
		catch(Exception $e){return $this->handle_exception($e);}
		return false;
	}
/*
Uses current connection and sends a SQL query to it.

NOTICE THIS CANNOT UTILIZE PREPARED STATEMENTS.

Returns a multi-dimenstional array as a result set to be used with other functions
that use result sets.

$sql is the sql string to be prepared
$array_type = NUM, ASSOC or BOTH. Defaults to ASSOC
*/
	public function query($sql,$array_type='ASSOC'){
		try{
		if(!$this->is_connected()){throw new Exception("No current connection to database!");}
		if($array_type=='ASSOC')
			$array_type = PDO::FETCH_ASSOC;
		else if($array_type=='NUM')
			$array_type = PDO::FETCH_NUM;
		else $array_type = PDO::FETCH_BOTH;
		$stmt = $this->DB->query($sql);
		if(!$stmt) return false;
		$valuesOUT = array();
		$i = 0;
		while($row = $stmt->fetch($array_type)){
			$valuesOUT[$i] = $row;
			$i++;
		}
		return $valuesOUT;
		}
		catch(Exception $e){return $this->handle_exception($e);}
		return false;
	}
/*
Takes a result set returned by either PDO_query_prepared() or PDO_query() functions.
Returns either true or false depending on whether or not the result set 
contained any rows.
*/
	public function has_rows($PDO_result){
		if($this->num_rows($PDO_result) > 0)
			return true;
		else return false;
	}
/*
Takes a result set returned by either PDO_query_prepared() or PDO_query() functions.
Returns the number of rows contained in the result set.
*/
	public function num_rows($PDO_result){
		return count($PDO_result);
	}
/*
Takes a result set returned by either query_prepared() or query() functions passed by reference.
Returns the next row in the result set and removes it from the set.
If no rows are left in the result set, it returns false.
Can be used in a while loop to iterate over all rows until result set is empty.
$PDO_result = a result set returned by either query_prepared() or query() functions passed by reference.
*/
	public function fetch_row(&$PDO_result){
		if(!$PDO_result)
			return false;
		if(count($PDO_result) < 1)
			return false;
		else
		return array_shift($PDO_result);
	}
/*
Takes a result set returned by either query_prepared() or query() functions.
Returns an array of the field names present in the result set.
$PDO_result = a result set returned by either query_prepared() or query() functions.
*/
	public function field_names($PDO_result){
		if($this->num_rows($PDO_result))
			return array_keys($PDO_result[0]);
		else return array();
	}
/*
For internal use.
Handles exceptions based on the ERR_MODE.
*/
	private function handle_exception($e){
		if($this->TESTING) echo $e;
		else if($this->USER_SET_ERR_MODE && $this->ERR_MODE > 0) throw $e;
		return false;
	}
}//END PDO_MySQL class
?>