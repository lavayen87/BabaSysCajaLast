<?php 
session_start();
if($_SESSION['active'])
{
	$_SESSION['active'] = false;
	session_destroy();
	echo 'ok';
}
?>