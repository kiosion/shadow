<?php

// Prevent direct access
if (!isset($include)) {
	echo Res::fail(403, 'Forbidden');
	exit();
}

class User {
	public static function get_uploads($uid, $start, $limit, $sort, $order, $filter) {
		GLOBAL $DB_PREFIX;
		if (!isset($uid) || !isset($start) || !isset($limit)) return false;
		// Set query
		$table = $DB_PREFIX . 'files';
		// Clean filter
		$filter = trim(str_replace('"', '', str_replace("'", '', $filter)));
		if (!empty($filter)) $sql = "SELECT * FROM $table WHERE uid = $uid AND ((og_name LIKE '%$filter%') OR (ul_name LIKE '%$filter%')) ORDER BY $sort $order LIMIT $start, $limit";
		else $sql = "SELECT * FROM $table WHERE uid = $uid ORDER BY $sort $order LIMIT $start, $limit";
		// Get results
		$res = runQuery($sql);
		$rows = array();
		// If results are found
		if ($res) {
			// Loop through results
			while ($row = $res->fetch_assoc()) {
				// Add to array
				$rows[] = $row;
			}
			// Return array
			return $rows;
		}
		// If no results are found
		else {
			return false;
		}
	}
	public static function get_upload_count($uid, $filter) {
		GLOBAL $DB_PREFIX;
		if (!isset($uid)) return false;
		// Set query
		$table = $DB_PREFIX . 'files';
		$filter = trim(str_replace('"', '', str_replace("'", '', $filter)));
		if (!empty($filter)) $sql = "SELECT count('id') AS count FROM $table WHERE uid = '$uid' AND ((og_name LIKE '%$filter%') OR (ul_name LIKE '%$filter%'))";
		else $sql = "SELECT COUNT('id') AS count FROM $table WHERE uid = '$uid'";
		// Get results
		$res = runQuery($sql);
		// If results are found
		if ($res) {
			// Return count
			return $res->fetch_assoc()['count'];
		}
		// If no results are found
		else {
			return false;
		}
	}
	public static function get_role($uid) {
		GLOBAL $DB_PREFIX;
		if (!isset($uid)) return false;
		// Run query
		$table = $DB_PREFIX . 'users';
		$sql = "SELECT role FROM $table WHERE id = '$uid'";
		$res = runQuery($sql);
		if ($res) {
			return $res->fetch_assoc()['role'];
		}
		// If no results are found
		else {
			return false;
		}
	}
}
