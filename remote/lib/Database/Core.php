<?php
namespace Database;

use \Database\Config as Config;
use \PDO as PDO;

class Core {
	public $dbh; // handle of the db connection
	private static $instance;
	private $error;
	private $stmt;

	private function __construct(){
		$dsn = 'mysql:host=' . Config::get('db.host') .
					 ';dbname='    . Config::get('db.basename') .
//					 ';port='      . Config::get('db.port') .
					 ';connect_timeout=15';
		// getting DB user from config
		$user = Config::get('db.user');
		// getting DB password from config
		$password = Config::get('db.password');

		$options = array(
			PDO::ATTR_PERSISTENT => true,
			PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION
//			PDO::ATTR_EMULATE_PREPARES => false
		);

		try{
			$this->dbh = new PDO($dsn, $user, $password, $options);
		}catch(PDOException $e){
			$this->error = $e->getMessage();
		}

	}

	public static function getInstance(){
		if (!isset(self::$instance)){
			$object = __CLASS__;
			self::$instance = new $object;
		}
		return self::$instance;
	}

	public function query($query){
		$this->stmt = $this->dbh->prepare($query);
	}

	public function bind($param, $value, $type = null){
		if (is_null($type)) {
			switch (true) {
				case is_int($value):
					$type = PDO::PARAM_INT;
					break;
				case is_bool($value):
					$type = PDO::PARAM_BOOL;
					break;
				case is_null($value):
					$type = PDO::PARAM_NULL;
					break;
				default:
					$type = PDO::PARAM_STR;
				}
		}
		$this->stmt->bindValue($param, $value, $type);
	}

	public function execute(){
		return $this->stmt->execute();
	}

	public function fetch(){
		$this->execute();
		return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function lastInsertId(){
		return $this->dbh->lastInsertId();
	}

	public function beginTransaction(){
		return $this->dbh->beginTransaction();
	}

	public function commit(){
		return $this->dbh->commit();
	}

	public function rollBack(){
		return $this->dbh->rollBack();
	}

	// others global functions
}