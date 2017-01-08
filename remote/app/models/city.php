<?php
/**
 * @class User
 */
class City extends Model {
	const TBL_NAME = 'city';

	function all($params, $where=null) {

		if(isset($params['start']) && isset($params['limit'])){
			$where = "
				WHERE tbl.id > ".abs(intval($params['start']))."
				ORDER BY tbl.id LIMIT ".abs(intval($params['limit']))."
			";
		}

		$sql = "
			SELECT id, city_name as cityName
			FROM ".static::TBL_CITY." as tbl
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
			INSERT INTO ".static::TBL_CITY." (city_name) VALUES (:cityName)
		";
		$sql2 = "
			UPDATE ".static::TBL_CITY."
			SET
			city_name = :cityName
			WHERE id = ".intval($id)."
		";
		$this->dbh->query($isup ? $sql2 : $sql1);
		$this->dbh->bind(':cityName', $params->cityName);
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
