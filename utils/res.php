<?php
// Class to return error on success/failure
class Res {
	public static function success($code, $msg, $data) {
		$res = array(
			'status' => 'success',
			'code' => $code,
			'msg' => $msg
		);
		if (isset($data)) $res['data'] = $data;
		return json_encode($res);
	}
	public static function fail($code, $msg) {
		$res = array(
			'status' => 'error',
			'code' => $code,
			'msg' => $msg
		);
		return json_encode($res);
	}
}
