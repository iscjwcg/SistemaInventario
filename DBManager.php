<?php

//header('Content-Type: text/xml; charset=utf-8');

class DataManager {
    protected $server;
    protected $port;
    protected $database;
    protected $user;
    protected $pass;
    protected $coneccion;
    protected $sql;
    protected $numRows;
    protected $dataset;
    protected $objDBM;

    function DataManager() {
            $this->coneccion = "";
            $this->sql = "";
            $this->numRows = 0;
            $this->dataset = array();
            $attrs = parse_ini_file("config.ini", TRUE);
            $this->server = $attrs['database']['server'];
            $this->port = $attrs['database']['port'];
            $this->database = $attrs['database']['name'];
            $this->user = $attrs['database']['user'];
            $this->pass = $attrs['database']['pass'];
            $this->objDBM = new DBManager($attrs['database']['type']);
    }
    protected function conectar() {
            $this->objDBM->conectarDB($this->server, $this->port, $this->database, $this->user, $this->pass);
    }
    protected function desconectar() {
            $this->objDBM->desconectarDB();
    }
    public function error() {
            $this->objDBM->errorMessage();
    }
    public function consultar() {
            switch(func_num_args()) {
                    case 1:
                            $sql = func_get_arg(0);
                            $this->alternoA($sql);
                            break;
                    case 3:
                            $table = func_get_arg(0);
                            $values = func_get_arg(1);
                            $condicion = func_get_arg(2);
                            $this->alternoB($table, $values, $condicion);
                            break;
            }
    }
    private function alternoA ($sql = '') {
            if($sql != '')
                    $this->sql = $sql;
            $this->conectar();
            $this->executar(TRUE);
    }
    private function alternoB ($table, $fields, $condicion) {
            $txtFields = "";
            if($fields != NULL)
                    $txtFields = implode(",", $fields);
            else
                    $txtFields = "*";
            $this->sql = "SELECT $txtFields FROM $table;";
            if($condicion != NULL) {
                    $where = $this->formarCondicion($condicion);
                    $this->sql = "SELECT $temp FROM $table WHERE $where;";
            }
            $this->conectar();
            $this->executar(TRUE);               
    }
    private function executar($flag) {
            $this->conectar();
            if($flag) {
                    $result = $this->objDBM->executarSQL($this->sql);
                    if($result != NULL) {
                            $this->numRows = $this->objDBM->numeroRows($result);
                            if($this->numRows > 0) {
                                    $this->generarDataset($this->objDBM->getDataset($result));
                            }
                            $this->objDBM->limpiarDataset($result);
                    }
            }
            else
                    $this->objDBM->executarSQL($this->sql);
    }
    public function insertar($table, $values) {
            unset($values["id_$table"]);
            $data = $this->formarNValues($values);
            $this->sql = "INSERT INTO $table $data;";
            $this->executar(FALSE);
    }
    public function actualizar($table, $values, $condicion) {
            unset($values["id_$table"]);
            $data = $this->formarMValues($values);
            $this->sql = "UPDATE $table SET $data;";

            if($condicion != NULL) {
                    $where = (gettype($condicion) == 'string') ? $condicion : $this->formarCondicion($condicion);
                    $this->sql = "UPDATE $table SET $data WHERE $where;";
            }
            $this->executar(FALSE);
    }
    public function eliminar($table, $condicion) {
            $where = (gettype($condicion) == 'string') ? $condicion : $this->formarCondicion($condicion);
            $this->sql = "DELETE FROM $table WHERE $where;";
            $this->executar(FALSE);
    }
    public function numeroFilas () {
            return $this->numRows;
    }
    public function dataset () {
            return $this->dataset;
    }
    private function formarNValues ($array) {
            $exp = "";
            $fields = "";
            $values = "";
            foreach($array as $field => $value) {
                    $fields .= "$field, ";
                    switch(gettype($value)) {
                            case 'integer':
                            case 'double':
                            case 'boolean':
                                    $values .= "$value,";
                                    break;
                            case 'string':
                                    $values .= "'$value',";
                                    break;
                            case 'object':
                                    $values .= "'". date_format($value, 'Y-m-d H:i:s')."',";
                                    break;
                    }
            }
            $fields = substr($fields, 0, strlen($fields) - 2);
            $values = substr($values, 0, strlen($values) - 1);
            $exp = "($fields) VALUES ($values)";
            return $exp;
    }
    private function formarMValues ($array) {
            $exp = "";
            foreach($array as $field => $value) {
                    switch(gettype($value)) {
                            case 'integer':
                            case 'double':
                            case 'boolean':
                                    $exp .= "$field = $value, ";
                                    break;
                            case 'string':
                                    $exp .= "$field = '$value', ";
                                    break;
                            case 'object':
                                    $exp .= "$field = '". date_format($field, 'Y-m-d H:i:s')."', ";
                                    break;
                    }
            }
            $exp = substr($exp, 0, strlen($exp) - 2);
            return $exp;
    }
    private function formarCondicion ($array) {
            $exp = "";
            foreach($array as $field=>$value) {
                    switch(gettype($value)) {
                            case 'integer':
                            case 'double':
                            case 'boolean':
                                    $exp .= "$field = $value AND ";
                                    break;
                            case 'string':
                                    $exp .= "$field = '$value' AND ";
                                    break;
                            case 'object':
                                    $exp .= "$field = '". date_format($field, 'Y-m-d H:i:s')."' AND ";
                                    break;
                    }
            }
            $exp = substr($exp, 0, strlen($exp) - 5);
            return $exp;
    }
    private function generarDataset($data) {
            $n = 0;
            $this->dataset = array();
            foreach($data as $row) {
                    $this->dataset[$n] = array();
                    foreach($row as $field => $value) {
                            if(gettype($value) == "object")
                                    $this->dataset[$n][$field] = "" . date_format($value, 'Y-m-d H:i:s');
                            else
                                    $this->dataset[$n][$field] = "" . $value;
//						settype($value, "string");
                    }
                    $n++;
            }
    }
}
class DBManager {
        protected $DBM;
        protected $conexion;
        protected $database;
        protected $sql;
        protected $port;
        protected $numRows;
        protected $dataset;
        protected $error;
        public function DBManager($DBM) {
                $this->numRows = 0;
                $this->dataset = NULL;
                switch($DBM) {
                        case 'mysql':
                                $this->DBM = 0;
                                $this->port = "3306";
                                break;
                        case 'mysql*':
                                $this->DBM = 1;
                                $this->port = "3306";
                                break;
                        case 'pgsql':
                                $this->DBM = 2;
                                $this->port = "5432";
                                break;
                        case 'mssql':
                                $this->DBM = 3;
                                $this->port = "1433";
                                break;
                }
        }
        public function conectarDB($server, $port, $database, $user, $pass) {
                switch($this->DBM) {
                        case 0:
                                $host = $server;
                                if($port != "")
                                        $host .= ":". $port;
                                else
                                        $host .= ":". $this->port;
                                $this->conexion = mysql_connect ($host, $user, $pass);
                                $this->database = mysql_select_db($database);
                                break;
                        case 1:
                                $host = $server;
                                if($port != "")
                                        $host .= ":". $port;
                                else
                                        $host .= ":". $this->port;
                                $this->conexion = mysqli_connect($host, $user, $pass, $database);
                                break;
                        case 2:
                                $conexion = "host=".$server;
                                if($port != "")
                                        $conexion .= " port=".$port;
                                else
                                        $conexion .= " port=".$this->port;
                                $conexion .= " dbname=".$database;
                                $conexion .= " user=".$user . " password=".$pass;
                                $this->conexion = pg_connect($conexion);
                                break;
                        case 3:
                                $host = $server;
                                if($port != "")
                                        $host .= ",". $port;
                                else
                                        $host .= ",". $this->port;
/*					$this->conexion = mssql_connect ($host, $user, $pass);
                                $this->database = mssql_select_db($database);*/
                                $connectionInfo = array("Database"=>$database, "UID"=>$user, "PWD"=>$pass);
                                $this->conexion = sqlsrv_connect($host, $connectionInfo);
                                break;
                }
        }
        public function desconectarDB() {
                switch($this->DBM) {
                        case 0:
                                mysql_close($this->conexion);
                                break;
                        case 1:
                                mysqli_close($this->conexion);
                                break;
                        case 2:
                                pg_close($this->conexion);
                                break;
                        case 3:
//					mssql_close ($this->conexion);
                                sqlsrv_close($this->conexion); 
                                break;
                }
        }
        public function errorMessage() {
            return $this->error;
        }
        public function executarSQL($sql = '') {
                $result;
                $this->sql = $sql;
                $this->error = "";
                switch($this->DBM) {
                        case 0:
                                $result = mysql_query($this->sql);
                                $this->error = mysql_errno($this->conexion) . ": " . mysql_error($this->conexion);
                                return $result;
                                break;
                        case 1:
                                $result = mysqli_query($this->conexion, $this->sql);
                                $this->error = mysqli_errno($this->conexion) . ": " . mysqli_error($this->conexion);
                                return $result;
                                break;
                        case 2:
                                return pg_query($this->sql);
                                break;
                        case 3:
                                $flag = FALSE;
//					return mssql_query($this->sql);
                                $result = sqlsrv_query($this->conexion, $this->sql);
                                if($result != NULL) {
                                        $i = 0;
                                        $dataset = array();
                                        while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                                                $dataset[$i++] = $row;
                                        }
                                        sqlsrv_free_stmt($result);
                                        $this->numRows = $i;
                                        $this->dataset = $dataset;
                                        $flag = TRUE;
                                }
                                return $flag;
                                break;
                }
                $this->desconectarDB();
        }
        public function numeroRows($result) {
                switch($this->DBM) {
                        case 0:
                                return mysql_num_rows($result);
                                break;
                        case 1:
                                return mysqli_num_rows($result);
                                break;
                        case 2:
                                return pg_num_rows($result);
                                break;
                        case 3:
//					return mssql_num_rows($result);
//					return sqlsrv_num_rows($result);
                                return $this->numRows;
                                break;
                }
        }
        public function getDataset($result) {
                switch($this->DBM) {
                        case 0:
                                $i = 0;
                                $this->dataset = array();
                                while($row = mysql_fetch_assoc($result)) {
                                //while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                                        $this->dataset[$i++] = $row;
                                }
                                return $this->dataset;
                                break;
                        case 1:
                                $i = 0;
                                $this->dataset = array();
                                while($row = mysqli_fetch_assoc($result)) {
                                        $this->dataset[$i++] = $row;
                                }
                                return $this->dataset;
                                break;
                        case 2:
                                return pg_fetch_all($result);
                                break;
                        case 3:
/*					$i = 0;
                                $dataset = array();
//					while($row = mssql_fetch_row ($result)) {
/*					while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                                        $dataset[$i++] = $row;
                                }
                                return $dataset;*/
                                return $this->dataset;
                                break;
                }
        }
        public function limpiarDataset($result) {
                switch($this->DBM) {
                        case 0:
                                mysql_free_result($result);
                                break;
                        case 1:
                                mysqli_free_result($result);
                                break;
                        case 2:
                                pg_free_result($result);
                                break;
                        case 3:
//					mssql_free_result($result);
//					sqlsrv_free_stmt($result);
                                break;
                }
        }
}
?>