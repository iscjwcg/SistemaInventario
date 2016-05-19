<?php
	include 'DBManager.php';
	include 'Usuario.php';
	session_start();
	class SessionUser {
		protected $status;
		protected $usuario;
		function SessionUser() {
			$this->status = 0;
			$this->usuario = NULL;
		}
		function iniciarSesion($user, $pass) {
			if($this->consultarUsuario($user, $pass)) {
				$_SESSION['session_user'] = $this->usuario;
//				$this->status = 1;
			}
			$this->usuario = NULL;
		}
		function cerrarSesion() {
			if(isset($_SESSION['session_user'])) {
				$this->usuario = $_SESSION['session_user'];
				$this->usuario->cerrarSesion();
				$_SESSION['session_user'] = $this->usuario = NULL;
//				$this->status = 0;
//				session_unregister('new_user');
			}
			session_destroy();
			session_unset();
		}
		function refresh() {
			if(isset($_SESSION['session_user'])) {
				$this->usuario = $_SESSION['session_user'];
				$this->usuario->refreshData();
				$_SESSION['session_user'] = $this->usuario;
				$this->usuario = NULL;
			}
		}
		function getIDUser() {
			$id = 0;
			if(isset($_SESSION['session_user'])) {
				$this->usuario = $_SESSION['session_user'];
				$id = $this->usuario->IDUsuario();
				$this->usuario = NULL;
			}
			return $id;
		}
		function getStatus() {
			if(isset($_SESSION['session_user'])) {
				$this->status = 1;
			}
			else {
				$this->status = 0;
			}
			return $this->status;
		}
		function getTipo() {
			$tipo = 0;
			if(isset($_SESSION['session_user'])) {
				$this->usuario = $_SESSION['session_user'];
				$tipo = $this->usuario->tipoUsuario();
				$this->usuario = NULL;
			}
			return $tipo;
		}
		function getAlias() {
			$alias = "";
			if(isset($_SESSION['session_user'])) {
				$this->usuario = $_SESSION['session_user'];
				$alias = $this->usuario->nombreUsuario();
				$this->usuario = NULL;
			}
			return $alias;
		}
		function getNombre() {
			$name = "";
			if(isset($_SESSION['session_user'])) {
				$this->usuario = $_SESSION['session_user'];
				$name = $this->usuario->nombreUsuario();
				$this->usuario = NULL;
			}
			return $name;
		}
		function consultarUsuario($user, $pass) {
			$usuario = FALSE;
			$mysql = new DataManager();
			$mysql->consultar("SELECT id_usuario, usuario, nombre, email FROM Usuario WHERE usuario = '$user' AND password = '$pass';");
			$numRows = $mysql->numeroFilas();
			$dataset = $mysql->dataset();
			$mysql = null;
			if($numRows == 1) {
				$idUsuario = 0 + $dataset[0][0];
				$alias = "" . $dataset[0][1];
				$email = "" . $dataset[0][3];
				$this->usuario = new Usuario($idUsuario, $alias, $email, 0);
				$this->usuario->iniciarSesion();
				$usuario = TRUE;
			}
			return $usuario;
		}
	}
?>