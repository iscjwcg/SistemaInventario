<?php
	class Functions {
		function Functions() {}
		function getCategorias() {
//	    	$list = array();
			$mysql = new DataManager();
			$mysql->consultar("SELECT P.id_categoria, P.categoria, P.id_parent, (SELECT COUNT(C.id_categoria) FROM Categoria C WHERE C.id_parent = P.id_categoria) as childs FROM Categoria P ORDER BY P.id_parent, P.id_categoria;");//WHERE P.id_parent = 0
			$numRows = $mysql->numeroFilas();
			$dataset = $mysql->dataset();
			$mysql = null;

			$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
			$contMain = 0;
			$main = array();
			$contT = 0;
			$list = array();
			for($i = 0; $i < $numRows; $i++) {
				if($dataset[$i][2] == 0)
					$main[$contMain++] = $dataset[$i];
				else
					$list[$contT++] = $dataset[$i];
			}
			
			$xml .= "<categoria>";
			for($i = 0; $i < $contMain; $i++) {
				$idC = $main[$i][0];
				$noChilds = 0 + $main[$i][3];
				if($noChilds > 0) {
					$xml .= "<subcategoria titulo='".$main[$i][1]."' childs='$noChilds'>";
					$childs = $this->findChilds($idC, $list, 'child');
					for($j = 0; $j < count($childs); $j++)
						$xml .= $childs[$j];
					$xml .= "</subcategoria>";
				}
				else
					$xml .= "<subcategoria titulo='".$main[$i][1]."' childs='0'></subcategoria>";
			}
			$xml .= "</categoria>";
			$file = "./categorias.xml";
			fwrite(fopen($file, 'w'), $xml);
			
/*			$final = $parents = $childs = $list = array();
			$cont = $pibote = $contTotal = $contParents = 0;
			for($i = 0; $i < count($dataset); $i++)
			{
				if($dataset[$i][2] == 0) {
					$parents[$cont] = $dataset[$i];
					$childs[$cont++] = 0;
				}
				else
					break;
			}
			$pibote = count($parents);
			for($i = 0; $i < count($parents); $i++)
			{
				$final[$contTotal++] = $parents[$i];
				for($j = $pibote; $j < count($dataset); $j++)
				{
					if($dataset[$j][2] == $parents[$i][0]) {
						$final[$contTotal++] = $dataset[$j];
						$childs[$i]++;
						$pibote++;
					}
					else
						break;
				}
			}
			$pibote = 0;
			for($i = 0; $i < count($final); $i++)
			{
				if($final[$i][2] == 0) {
					$flagParent = TRUE;
					if($childs[$contParents++] == 0)
						$flagParent = FALSE;
//						$list[$i] = "<optgroup id='' label='".$final[$i][1]."'></optgroup>";
//						$list[$i] = "<option id='' value='".$final[$i][0]."'>".$final[$i][1]."</option>";
					$list[$i] = ($flag == 1) ? $this->getLabelCategory($final[$i][0], $final[$i][1], TRUE) : $this->getOptionCategory($final[$i][0], $final[$i][1], $flagParent);
				}
				else
					$list[$i] = ($flag == 1) ? $this->getLabelCategory($final[$i][0], $final[$i][1], FALSE) : $this->getOptionCategory($final[$i][0], $final[$i][1], FALSE);
//					$list[$i] = "<option id='' value='".$final[$i][0]."'>".$final[$i][1]."</option>";
			}*/
			
/*			$i = 0;
			$x = 0;
			$pibote = 0;
			while($i < count($dataset)) {
				if($dataset[$i][2] == 0) {
					if($flag == 1)
						$list[$x++] = "<dt class='category mainCat' onclick='displaySubCat(".$dataset[$i][0].")' id='cat".$dataset[$i][0]."'>".$dataset[$i][1]."</dt>";
					else if($flag == 2)
						$list[$x++] = "<optgroup id='' label='".$dataset[$i][1]."'></optgroup>";//"<option id='' value='".$dataset[$i][0]."'>".$dataset[$i][1]."</option>";
					$pibote = $i + 1;
					$temp = "";
					for($n = $pibote; $n < count($dataset); $n++) {
						if($dataset[$n][2] == $dataset[$i][0] ) {
							if($flag == 1)
								$list[$x++] = "<dd class='category subCat' onclick='localSearch(this);' categoria='".$dataset[$i][0]."'>".$dataset[$n][1]."</dd>";
							else if($flag == 2)
								$list[$x++] = "<option id='' value='".$dataset[$n][0]."'>".$dataset[$n][1]."</option>";
						}
					}
				}
				$i++;
			}
	    	return $list;*/
	    }
		function findChilds($idC, $list, $tag) {
			$noChild = 0;
			$childs = array();
			for($i = 0; $i < count($list); $i++) {
				$tempId = $list[$i][0];
				if($list[$i][2] == $idC) {
					$txtTemp = "";
					$tNoChilds= $list[$i][3];
					if($tNoChilds > 0){
						$tempChilds = $this->findChilds($tempId, $list, $tag.($noChild+1));
						for($j = 0; $j < $tNoChilds; $j++)
							$txtTemp .= $tempChilds[$j];
					}
					$childs[$noChild++] = "<$tag".($noChild)." titulo='".$list[$i][1]."' childs='$tNoChilds'>".$txtTemp."</$tag".($noChild).">";
				}
			}
			return $childs;
		}
	    function saveUser($id, $nombre, $apellidos, $user, $password) {
	    	$mysql = new DataManager();
	    	if($id == 0) {
	    		$data = array('nombre' => $nombre, 'apellidos' => $apellidos, 'user' => $user, 'password' => $password);
				$mysql->insertar("Usuario", $data);
	    	}
			else if($id > 0) {
				$data = array('nombre' => $nombre, 'apellidos' => $apellidos, 'user' => $user, 'password' => $password);
				$condicion = array('id_user' => $id);
				$mysql->actualizar("Usuario", $data, $condicion);
			}
			$mysql = null;
	    }
		function saveProducto($id, $nombre, $descripcion, $stock, $costoMayoreo, $costoMenudeo, $foto, $categoria) {
	    	$mysql = new DataManager();
	    	if($id == 0) {
	    		$data = array('nombre' => $nombre, 'descripcion' => $descripcion, 'stock' => $stock, 'costo_mayoreo' => $costoMayoreo, 'costo_menudeo' => $costoMenudeo, 'Ranking' => 0, 'imagen' => $foto, 'categoria' => $categoria);
				$mysql->insertar("Producto", $data);
	    	}
			else if($id > 0) {
				$data = array('nombre' => $nombre, 'descripcion' => $descripcion, 'stock' => $stock, 'costo_mayoreo' => $costoMayoreo, 'costo_menudeo' => $costoMenudeo, 'Ranking' => 0, 'imagen' => $foto, 'categoria' => $categoria);
				$condicion = array('id_producto' => $id);
				$mysql->actualizar("Producto", $data, $condicion);
			}
			$mysql = null;
	    }
		function generateFormatXML($tabla) {
			$names = array('id_producto','','','cantidad');
			$xml = "<listado>";
			foreach($tabla as $producto) {
				$xml .= "<producto>";
				foreach ($producto as $field => $value) {
					if($field == 0 || $field == 3)
						$xml .= "<".$names[$field].">$value</".$names[$field].">";
				}
				$xml .= "</producto>";
			}
			$xml .= "</listado>";
			return $xml;
		}
		function generateFormatTable($tabla) {
			$cont = 1;
			$total = 0;
			$parImpar = "";
			$table = "<table border='1' style='border: solid thin black; border-collapse: collapse; text-align: center;'>";
			$table .= "<thead><tr style='background-color: #ADD8E6;'>";
			$table .= "<td style='padding: 2px 6px;'>Producto</td><td style='padding: 2px 6px;'>N&uacute;mero de unidades</td>";
			$table .= "<td style='padding: 2px 6px;'>Costo x unidad</td><td style='padding: 2px 6px;'>Sub-Total</td></tr></thead>";
			$table .= "<tbody>";
			foreach($tabla as $producto) {
				$subtotal = 0;
				$parImpar = (($cont%2) > 0) ? "background-color: white;" : "background-color: #E9E9E9;";
				$table .= "<tr style='$parImpar'>";
				foreach ($producto as $field => $value) {
					if($field > 1) {
						$table .= ($field == 4) ? "<td>$ $value</td>" : "<td>$value</td>";
						if($field == 3)
							$subtotal += $value;
						else if($field == 4) {
							$subtotal *= $value;
							$table .= "<td>$ $subtotal</td></tr>";
						}
					}
				}
				$total += $subtotal;
				$cont++;
			}
			$table .= "</tbody>";
			$table .= "<tfoot><tr><td colspan='3' style='text-align: right; padding: 2px 6px;'>Total $</td><td>$total</td></tr></tfoot>";
			$table .= "</table>";
			return $table;
		}
		private function getOptionCategory($id, $caption, $flag) {
			$option = "";
			if($flag)
				$option = "<optgroup id='' label='$caption'></optgroup>";
			else
				$option = "<option id='' value='$id'>$caption</option>";

			return $option;
		}
		private function getLabelCategory($id, $caption, $flag) {
			$label = "";
			if($flag)
				$label = "<dt class='category mainCat' onclick='displaySubCat($id)' id='cat$id'>$caption</dt>";
			else
				$label = "<dd class='category subCat' onclick='localSearch(this);' categoria='$id'>$caption</dd>";

			return $label;
		}
	}
?>