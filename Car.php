<?php
	class Car {
		protected $numArt;
		protected $articulos;
		function Car() {
			$this->numArt = 0;
			$this->articulos = array();
		}
		function addToCar($idArt, $cantidad) {
			$flag = false;
			if($this->numArt > 0) {
				for($i = 0; $i < $this->numArt; $i++) {
					$objProducto = $this->articulos[$i];
					if($objProducto->getID() == $idArt) {
						$flag = true;
						$objProducto->setCantidad($cantidad);
						$this->articulos[$i] = $objProducto;
						$objProducto = null;
						break;
					}
				}
			}
			if(!$flag) {
				$objProducto = new Product($idArt);
				$objProducto->setCantidad($cantidad);
				$this->articulos[$this->numArt++] = $objProducto;
				$objProducto = null;
			}
		}
		function removeOfCar($index) {
			$n = 0;
			$temp = array();
			for($i = 0; $i < $this->numArt; $i++) {
				if($i != $index) {
					$temp[$n++] = $this->articulos[$i];
				}
			}
			$this->articulos = $temp;
		}
		function getProduct($index)
		{
			$row = array();
			$producto = $this->articulos[$index];
			$row[0] = $producto->getID();
			$row[1] = $producto->getFoto();
			$row[2] = $producto->getNombre();
			$row[3] = $producto->getCantidad();
			$row[4] = $producto->getCosto();
			return $row;
		}
		function getCountProducts() {
			return $this->numArt;
		}
	}
	class Product {
		protected $id;
		protected $name;
		protected $quantity;
		protected $cost;
		protected $image;
		function Product($idProducto) {
			$this->id = $idProducto;
			$mysql = new DataManager();
			$mysql->consultar("SELECT nombre, costo_menudeo, imagen FROM Producto WHERE id_producto = ".$this->id.";");
			$dataset = $mysql->dataset();
			$mysql = null;
			$this->name = "" . $dataset[0][0];
			$this->cost = 0 + $dataset[0][1];
			$this->image = "" . $dataset[0][2];
			$this->quantity = 0;
		}
		function getID() {
			return $this->id;
		}
		function getNombre() {
			return $this->name;
		}
		function setCantidad($quantity) {
			$this->quantity += $quantity;
		}
		function getCantidad() {
			return $this->quantity;
		}
		function getCosto() {
			return $this->cost;
		}
		function getFoto() {
			return $this->image;
		}
	}
?>