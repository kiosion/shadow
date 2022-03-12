<?php

// Prevent direct access
if (!isset($include)) {
	echo Res::fail(403, 'Forbidden');
	exit();
}

class User {
	public static function get_uploads($uid, $start, $sort, $order) {
		if (!isset($uid) || !isset($start)) return false;
		// Set query
		$sql = "SELECT * FROM files WHERE uid = $uid ORDER BY $sort $order LIMIT $start, 10";
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
	public static function get_upload_count($uid) {
		if (!isset($uid)) return false;
		// Set query
		$sql = "SELECT COUNT('id') AS count FROM files WHERE uid = '$uid'";
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
		if (!isset($uid)) return false;
		// Run query
		$sql = "SELECT role FROM users WHERE id = '$uid'";
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
