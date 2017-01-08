<?php
/**
 * @class User
 */
class Education extends Model {
	const TBL_NAME = 'education';

	function all($params, $where=null) {

		if(isset($params['start']) && isset($params['limit'])){
			$where = "
				WHERE tbl.id > ".abs(intval($params['start']))."
				ORDER BY tbl.id LIMIT ".abs(intval($params['limit']))."
			";
		}

		$sql = "
			SELECT id, edu_name as eduName
			FROM ".static::TBL_EDU." as tbl
			$where
		";
		$this->dbh->query($sql);
		if ($this->dbh->execute()) {
			$res = $this->dbh->fetch();
		}
		return $res;
	}

	function create($params, $update=false, $id=false) {
		$isup = false;
		if($update && !empty($id) )
			$isup = true;
		$sql1 = "
			INSERT INTO ".static::TBL_EDU." (edu_name) VALUES (:eduName)
		";
		$sql2 = "
			UPDATE ".static::TBL_EDU."
			SET
			city_name = :eduName
			WHERE id = ".intval($id)."
		";
		$this->dbh->query($isup ? $sql2 : $sql1);
		$this->dbh->bind(':eduName', $params->eduName);
		$this->dbh->execute();
		$lastId = $isup ? $id : $this->dbh->lastInsertId();
		$where = "
			WHERE tbl.id = $lastId
		";
		$res = $this->all(null, $where);
		return $res;
	}

	function update($id, $params) {
		$res = $this->create($params, $update=true, $id);
		return $res;
	}

}
