<?php
    include 'Session.php';
	session_start();
	$objSession = new SessionUser();
	if($objSession->getStatus() == 1) {
		$objSession->cerrarSesion();
//		header("Location: index.php");
	}
	echo "<span></span>";
?>