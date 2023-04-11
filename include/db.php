<?php

if (!defined('BOT_MIDRASH')) die('{"code":200}');

class db {
	static private $connection = NULL;

	static private $info = array(
		'last_query' => null,
		'num_rows' => null,
		'insert_id' => null
	);
	static private $where;
	static private $limit;
	static private $order;
	
	static public function connect($username = "", $password = "", $dbname = "", $host = "localhost") {
		if (is_null(self::$connection)) {
			self::$connection = new mysqli($host, $username, $password, $dbname);

			self::$connection->set_charset("utf8");
		}
	}

	static public function close() {
		if (!is_null(self::$connection)) {
			self::$connection->close();
		}
	}

	static private function set($field, $value) {
		self::$info[$field] = $value;
	}
	
	static public function last_query() {
		return self::$info['last_query'];
	}
	
	static public function num_rows() {
		return self::$info['num_rows'];
	}
	
	static public function insert_id() {
		return self::$info['insert_id'];
	}

	private function __where($info, $type = 'AND') {
		$where = self::$where;
		foreach ($info as $row => $value) {
			if (empty($where)) {
				$where = sprintf("WHERE `%s`='%s'", $row, self::$connection->real_escape_string($value));
			}
			else {
				$where .= sprintf(" %s `%s`='%s'", $type, $row, self::$connection->real_escape_string($value));
			}
		}
		self::$where = $where;
	}
	
	public function where($field, $equal = null) {
		if (is_array($field)) {
			self::__where($field);
		}
		else {
			self::__where(array($field => $equal));
		}
		return $this;
	}
	
	public function and_where($field, $equal = null) {
		return self::where($field, $equal);
	}
	
	public function or_where($field, $equal = null) {
		if (is_array($field)) {
			self::__where($field, 'OR');
		}
		else {
			self::__where(array($field => $equal), 'OR');
		}
		return $this;
	}

	public function limit($limit) {
		self::$limit = 'LIMIT ' . $limit;
		return $this;
	}

	public function order_by($by, $order_type = 'DESC') {
		$order = self::$order;
		if (is_array($by)) {
			foreach ($by as $field => $type) {
				if (is_int($field) && !preg_match('/(DESC|desc|ASC|asc)/', $type)) {
					$field = $type;
					$type = $order_type;
				}
				if (empty($order)) {
					$order = sprintf("ORDER BY `%s` %s", $field, $type);
				}
				else {
					$order .= sprintf(", `%s` %s", $field, $type);
				}
			}
		}else {
			if (empty($order)) {
				$order = sprintf("ORDER BY `%s` %s", $by, $order_type);
			}
			else {
				$order .= sprintf(", `%s` %s", $by, $order_type);
			}
		}
		self::$order = $order;
		return $this;
	}

	static private function extra() {
		$extra = '';

		if (!empty(self::$where)) $extra .= ' ' . self::$where;
		if (!empty(self::$order)) $extra .= ' ' . self::$order;
		if (!empty(self::$limit)) $extra .= ' ' . self::$limit;

		self::$where = null;
		self::$order = null;
		self::$limit = null;
		return $extra;
	}

	static public function query($query, $return = false) {
		self::set('last_query', $query);
		$result = self::$connection->query($query);

		if (is_object($result)) { // is_resource
			self::set('num_rows', $result->num_rows);
		}

		if ($return) {
			$data = $result->fetch_all(MYSQLI_ASSOC);
			$result->free_result();
			return $data;
		}

		return true;
	}

	static public function select($table, $select = '*') {
		if (is_array($select)) {
			$cols = '';
			foreach($select as $col) {
				$cols .= "`{$col}`, ";
			}
			$select = substr($cols, 0, -1);
		}

		$sql = sprintf("SELECT %s FROM %s %s", $select, $table, self::extra());

		return self::query($sql, true);
	}

	static public function selectFirst($table, $select = '*', $notExistValue = false) {
		return self::select($table, $select)[0] ?? $notExistValue;
	}

	static public function insert($table, $data) {
		$fields = '';
		$values = '';

		foreach ($data as $col => $value) {
			$fields .= sprintf("`%s`,", $col);
			if (is_null($value)) {
				$values .= "NULL, ";
			}
			elseif ($value == "CURRENT_TIMESTAMP") {
				$values .= "CURRENT_TIMESTAMP, ";
			}
			else {
				$values .= sprintf("'%s',", self::$connection->real_escape_string($value));
			}
		}

		$fields = substr($fields, 0, -1);
		$values = substr($values, 0, -1);

		$sql = sprintf("INSERT INTO %s (%s) VALUES (%s)", $table, $fields, $values);

		$res = self::query($sql);

		self::set('insert_id', self::$connection->insert_id);
		
		return $res;
	}
	
	static public function update($table, $info) {
		if (empty(self::$where)) {
			return false;
		}

		else {
			$update = '';
			foreach ($info as $col => $value) {
				$update .= sprintf("`%s`='%s', ", $col, self::$connection->real_escape_string($value));
			}
			
			$update = substr($update, 0, -2);

			$sql = sprintf("UPDATE %s SET %s%s", $table, $update, self::extra());

			return self::query($sql);
		}
	}
	
	static public function delete($table) {
		if (empty(self::$where)) {
			return false;
		}
		else {
			$sql = sprintf("DELETE FROM %s %s", $table, self::extra());

			return self::query($sql);
		}
	}
}
