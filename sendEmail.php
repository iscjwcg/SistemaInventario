<?php
//	ini_set("default_charset", "utf-8");
	session_start();
    $nombre = $_POST['contacto_c1'];
	$empresa = $_POST['contacto_c2'];
	$telefono = $_POST['contacto_c3'];
	$correo = $_POST['contacto_c4'];
	$comentario = $_POST['contacto_c5'];
	$captcha = $_POST['contacto_captcha'];
	
	$results = "";
	$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
	
	$attrs = parse_ini_file("config.ini", TRUE);
	$contacto = "". $attrs['info']['email'];;
	$titulo = "Contacto.";
	
	$cabecera = "MIME-Version: 1.0\r\n";
	$cabecera .= "Content-type: text/html; charset=utf-8\r\n";
	$cabecera .= "From: $correo\r\n";
	$cabecera .= "Reply-To: $correo\r\n";
	
	$comentario = strip_tags($comentario);
	
	$mensaje = "<p>- - - - - - - - - - - - - - - - - - - - - - - - - -<br />";
	$mensaje .= "Contacto<br />";
	$mensaje .= "- - - - - - - - - - - - - - - - - - - - - - - - - -<br />";
	$mensaje .= " - Nombre: ".$nombre."<br />";
	$mensaje .= " - Empresa: ".$empresa."<br />";
	$mensaje .= " - Telefono: ".$telefono."<br />";
	$mensaje .= " - Correo: ".$correo."<br />";
	$mensaje .= " - Mensaje: ".$comentario."<br />";
	$mensaje .= "- - - - - - - - - - - - - - - - - - - - - - - - - -</p>";
	
	$captcha = strtolower($captcha);
	
	if($_SESSION['tmptxt'] == $captcha) {
		mail($contacto, $titulo, utf8_decode($mensaje), $cabecera);
		$results = "<result><data status='1'></data></result>";
	}
	else {
		$results = "<result><data status='0'>Problema con la seguridad AntiSpam. Vuelva a mandar su comentario. Gracias.</data></result>";
	}
	$xml .= $results;
	echo $xml;
?>
