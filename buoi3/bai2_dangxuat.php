<?php
	session_start();
	define('M_RUNNING', 1);
	require_once __DIR__.'/classes/user.php';
	$user = new \MApp\User(null);
	$user->logout();
	header('location: bai2_dangnhap.php');
?>