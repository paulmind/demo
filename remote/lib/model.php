<?php
/**
 * @class Model
 */
use \Database\Core as Core;

abstract class Model {
	// const DB = 'extjs_store';
	const TBL_USER = 'user';
	// const USER_TABLE = DB.'user'; in php 5.6
	const TBL_CITY = 'city';
	const TBL_EDU = 'education';
	const TBL_USER_CITY = 'user_city';

	public $id, $attributes;
	protected $dbh;

	public function __construct(){
		$this->dbh = Core::getInstance();
		if(!defined("static::TBL_NAME")){
			throw new Exception("Main constant TBL_NAME not defined");
		}
	}

	/*public getTblName($const){
		if(defined("self::$const"))
			return self::DB.'.'.constant("self::$const");
	}*/

	abstract protected function create($params);
	abstract protected function update($id, $params);
	// abstract protected function destroy($id);
	abstract protected function all($params);







	function destroy($id){

		if(is_array($id)){
			foreach($id as &$v)
				$v = (int)$v;
			$id = join(',', $id);
		}else{
			$id = (int)$id;
		}

		try{
			$sql="
				DELETE FROM ".static::TBL_NAME." WHERE id IN ($id)
			";
			$this->dbh->query($sql);
			$this->dbh->execute();
			return array();
		} catch (Exception $e) {
			return array('failure' => true, 'msg' => "Ошибка: ".$e->getMessage() );
		}

	}

}
