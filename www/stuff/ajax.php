<?php
header('Content-Type: application/json; charset=UTF-8', true);
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	include_once dirname(__FILE__) . '/session.php';
	
	echo json_encode(array(
		'dyn_ip_address'=>$_SERVER['REMOTE_ADDR'],
		'dyn_session_datas'=>print_r($_SESSION, true
		)));	
} else echo '{}';
?>