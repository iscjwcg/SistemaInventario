<?php
include 'DBManager.php';

$attrs = parse_ini_file("config.ini", TRUE);
echo "DBSM = ". $attrs['database']['type'] ."<br />";
echo "Server = ". $attrs['database']['server'] ."<br />";
echo "Data Base = ". $attrs['database']['name'] ."<br />";
echo "User = ". $attrs['database']['user'] ."<br />";
echo "Pass = ". $attrs['database']['pass'] ."<br />";
echo "<br />";

$obj = new DataManager();
//$obj->consultar("cliente", NULL, NULL);
$obj->consultar("select * from usuario;");
echo "Num Rows: ".$obj->numeroFilas()."<br /><br />";
echo "Dataset: <br />";
$dataset = $obj->dataset();
echo "<table border='1'>";
foreach($dataset as $row) {
	echo "<tr>";
	foreach($row as $field => $value) {
		echo "<td>$value</td>";
	}
	echo "</tr>";
}
echo "</table>";


/*
//test insert
$values = array('code'=>'TEST1', 'title'=>'TEST', 'did'=>777, 'date_prod'=>'11/09/2001', 'kind'=>'Test', 'len'=>'120 minutes');
$obj->insertar($table, $values);
*/

/*
//test update
$condicion = array('code'=>'TEST1');
$values = array('title'=>'TEST2', 'did'=>111, 'date_prod'=>'11/09/2001', 'kind'=>'Test2', 'len'=>'150 minutes');
$obj->actualizar($table, $values, $condicion);
*/

/*
//test delete
$condicion = array('code'=>'TEST1');
$values = array('title'=>'TEST2', 'did'=>111, 'date_prod'=>'11/09/2001', 'kind'=>'Test2', 'len'=>'150 minutes');
$obj->eliminar($table, $condicion);
*/
?>