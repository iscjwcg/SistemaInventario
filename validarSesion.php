<?php
	include 'Session.php';
        $user = $_POST['user'];
	$pass = $_POST['pass'];
	$result = "";
	$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
	$objSession = new SessionUser();
	if($objSession->getStatus() == 0) {
		if($user != '' && $pass != '')
			$objSession->iniciarSesion($user, $pass);
		if($objSession->getStatus() == 1) {
			if($objSession->getTipo() == 0) {
				$xml .= "<result><data status='1' tipo='0'></data></result>";
			}
			else if($objSession->getTipo() > 0) {
				$xml .= "<result><data status='1' tipo='".$objSession->getTipo()."'></data></result>";
			}
		}
		else {
			$xml .= "<result><data status='0' tipo='0'></data></result>";
		}
	}
	else if($objSession->getStatus() == 1) {
		if($objSession->getTipo() == 0) {
			$xml .= "<result><data status='1' tipo='0'></data></result>";
		}
		else if($objSession->getTipo() > 0) {
			$xml .= "<result><data status='1' tipo='".$objSession->getTipo()."'></data></result>";
		}
	}
	echo $xml;
?>