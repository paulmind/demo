<?php
/**
 * @class User
 */
class User extends Model {
	const TBL_NAME = 'user';

	function all($params, $where=null) {

		if(isset($params['start']) && isset($params['limit'])){
			$where = "
				WHERE usr.id > ".abs(intval($params['start']))."
			";
			$limit = "
				LIMIT ".abs(intval($params['limit']))."
			";
		}

		$sql = "
			SELECT usr.id,
				usr.username as userName,
				edu.edu_name as eduName,
				GROUP_CONCAT(DISTINCT c.city_name ORDER BY c.city_name ASC SEPARATOR ', ') as cityName
			FROM ".static::TBL_USER." as usr
			LEFT JOIN ".static::TBL_EDU." as edu
				ON usr.education_id = edu.id
			LEFT JOIN ".static::TBL_USER_CITY." as uc
				ON usr.id = uc.user_id
			LEFT JOIN ".static::TBL_CITY." as c
				ON uc.city_id = c.id
			$where
			GROUP BY usr.id ASC
		";
		$this->dbh->query($sql);
		if ($this->dbh->execute()) {
			$res = $this->dbh->fetch();
		}
		// print_r($res);
		return $res;
	}

	function getDict($tblName, $valueFieldName){
		$sql = "
			SELECT *
			FROM $tblName
		";
		$this->dbh->query($sql);
		if ($this->dbh->execute()) {
			$res = $this->dbh->fetch();
		}
		$a=array();
		foreach($res as $v){
			$a[$v['id']] = $v[$valueFieldName];
		}
		return $a;
	}

	function create($params, $update=false, $id=false) {
		$isup = false;
		if($update && !empty($id) )
			$isup = true;
		$sql1 = "
			INSERT INTO ".static::TBL_USER." (username, education_id) VALUES (:username, :eduId)
		";
		if($isup){
			$sql1="
				UPDATE ".static::TBL_USER." as usr
				SET
				usr.username = :username,
				usr.education_id = :eduId
				WHERE usr.id = ".intval($id)."
			";
		}
		$cityAll = array_flip($this->getDict(static::TBL_CITY, 'city_name'));
		$eduAll = array_flip($this->getDict(static::TBL_EDU, 'edu_name'));

		if(isset($eduAll[$params->eduName])){
			$eduId = $eduAll[$params->eduName];
		}else{
			return array('failure' => true, 'msg' => "Ошибка");
		}

		$last_insert_id = $isup ? $id : 'last_insert_id()';

		if(is_array($params->cityName)){
			foreach($params->cityName as $v){
				if(isset($cityAll[$v])){
					$cityIds[]="($last_insert_id, {$cityAll[$v]})";
				}
			}
		}else{
			return array('failure' => true, 'msg' => "Ошибка");
		}

		$sql2 = "
			INSERT INTO ".static::TBL_USER_CITY." (user_id, city_id) VALUES ".join(',', $cityIds)."
		";

		try {
			$this->dbh->beginTransaction();
			
			$this->dbh->query($sql1);
			$this->dbh->bind(':username', $params->userName);
			$this->dbh->bind(':eduId', $eduId);
			$this->dbh->execute();

			if($isup){
				$sql3="
					DELETE FROM ".static::TBL_USER_CITY." WHERE user_id = ".intval($id);
					// print_r($sql3);
				$this->dbh->query($sql3);
				$this->dbh->execute();
				$lastId = $id;
			}else{
				$lastId = $this->dbh->lastInsertId();
			}

			$this->dbh->query($sql2);
			$this->dbh->execute();

			$this->dbh->commit();

			$where = "
				WHERE usr.id = $lastId
			";
			$res = $this->all(null, $where);
      $this->addToQueue();
			return $res;
		} catch (Exception $e) {
			$this->dbh->rollBack();
			return array('failure' => true, 'msg' => "Ошибка: ".$e->getMessage() );
		}
	}

	function update($id, $params) {
		$res = $this->create($params, $update=true, $id);
		$this->addToQueue();
		return $res;
	}

  function addToQueue() {
    //$this->mail();
  }

}