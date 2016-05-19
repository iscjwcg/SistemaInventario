<?php
    include 'functions.php';
    class Usuario {
    	protected $id;
    	protected $usuario;
		protected $email;
		protected $grado;
		function Usuario($idUsuario, $usuario, $email, $grado) {
			$this->id = 0 + $idUsuario;
			$this->usuario = $usuario;
			$this->email = $email;
			$this->grado = 0 + $grado;
		}
		function iniciarSesion() {
			$now = $this->now();
			$mysql = new DataManager();
			$data = array('status' => 1, 'ultimoAcceso' => "$now");
			$where = array('id_usuario' => $this->id);
			$mysql->actualizar("Usuario", $data, $where);
			$mysql = null;
		}
		function cerrarSesion() {
			$mysql = new DataManager();
			$data = array('status' => 0);
			$where = array('id_usuario' => $this->id);
			$mysql->actualizar("Usuario", $data, $where);
			$mysql = null;
		}
		function IDUsuario() {
			return $this->id;
		}
		function nombreUsuario() {
			return $this->usuario;
		}
		function emailUsuario() {
			return $this->email;
		}
		function tipoUsuario() {
			return $this->grado;
		}
		function refreshData() {
			$mysql = new DataManager();
			$mysql->consultar("SELECT usuario, email FROM Usuario WHERE id_usuario = ".$this->id.";");
			$numRows = $mysql->numeroFilas();
			$dataset = $mysql->dataset();
			$mysql = null;
			if($numRows == 1) {
				$this->usuario = "" . $dataset[0][0];
				$this->email = "" . $dataset[0][1];
			}
		}
		function emailContacto()
		{
			$attrs = parse_ini_file("config.ini", TRUE);
			return $attrs['info']['email'];
		}
		function sendEmail ($email, $title, $content) {
			$correo = $this->emailContacto();
			$cabecera = "MIME-Version: 1.0\r\n";
			$cabecera .= "Content-type: text/html; charset=utf-8\r\n";
			$cabecera .= "From: $correo\r\n";
			$cabecera .= "Reply-To: $correo\r\n";
			return mail($email, $title, utf8_decode($content), $cabecera);
		}
		function now() {
			$horaServidor = date("H");
			$minServidor = date("i:s");
			$horarioServidor = date("O");
			$horarioMexico = "-0500";
			$diferencia = $horarioServidor - $horarioMexico;
			$diferencia = ($diferencia/-100);
			$horaServidor = $horaServidor + $diferencia;
			$now = date("Y-m-d") . " $horaServidor:$minServidor";
			return $now;
		}
    }
?>