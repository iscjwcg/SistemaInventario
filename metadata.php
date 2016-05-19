<?php
//    include 'DBManager.php';
define('CHARSET', 'ISO-8859-1');
define('REPLACE_FLAGS', ENT_COMPAT | ENT_XHTML);

//header('Content-Type: text/xml; charset=utf-8');

class Metadata {
    public $tableID;
    public $tableName;
    public $description;
    public $joinWith;
    public $fieldDefinition;
    public $defaultValue;
    public $keyFields;

    function Metadata() {
        $this->tableID = 0;
        $this->tableName = "";
        $this->description = "";
        $this->joinWith = "";
        $this->fieldDefinition = array();
        $this->defaultValue = array();
        $this->keyFields = array();
    }
    function GetMetadata($tableName) {
        $this->tableName = $tableName;
        $this->GetTableDefinition();
        $this->GetFieldDefinitions();
    }
    function GetTableDefinition() {
        $connector = new DataManager();
        $connector->consultar("SELECT * FROM metadatatable WHERE tablename = '$this->tableName';");
        $numRows = $connector->numeroFilas();
        $dataset = $connector->dataset();
        $connector = null;

        if($numRows == 1) {
            //list($this->tableID, $this->tableName, $this->description, $this->joinWith) = $dataset[0];
            $this->tableID = $dataset[0]["id_metadatatable"];
            $this->tableName = $dataset[0]["tablename"];
            $this->description = $dataset[0]["description"];
            $this->joinWith = $dataset[0]["joinwith"];
        }
    }
    function GetFieldDefinitions() {
        $connector = new DataManager();
        $connector->consultar("SELECT * FROM metadatafield WHERE id_metadatatable = $this->tableID ORDER BY `order`;");
        $numRows = $connector->numeroFilas();
        $dataset = $connector->dataset();
        $connector = null;

        if($numRows > 0) {
            /* id_metadatafield, fieldname, description, type, length, columndefinition, defaultvalue, class, id_metadatatable */
            foreach ($dataset as $fieldDefinition) {
                $this->fieldDefinition[$fieldDefinition["fieldname"]] = array(
                    'metadatafieldID' => $fieldDefinition["id_metadatafield"],
                    'fieldname' => $fieldDefinition["fieldname"],
                    'description' => $this->clearString($fieldDefinition["description"]),
                    'type' => $fieldDefinition["type"],
                    'length' => $fieldDefinition["length"],
                    'columndefinition' => $fieldDefinition["columndefinition"],
                    'search' => $fieldDefinition["search"],
                    'defaultvalue' => $fieldDefinition["defaultvalue"],
                    'class' => $fieldDefinition["class"],
                    'isVisible' => $fieldDefinition["isvisible"],
                    'isEditable' => $fieldDefinition["iseditable"],
                    'isRequired' => $fieldDefinition["isrequired"],
                    'metadatatableID' => $fieldDefinition["id_metadatatable"]);

                if(strtolower($fieldDefinition["class"]) == "key")
                {
                    $this->keyFields[count($this->keyFields)] = $fieldDefinition["fieldname"];
                }

                switch(strtolower($fieldDefinition["type"]))
                {
                    case "varchar":
                    {
                        $this->defaultValue[$fieldDefinition["fieldname"]] = "";
                        break;
                    }
                    case "bit":
                    {
                        $this->defaultValue[$fieldDefinition["fieldname"]] = false;
                        break;
                    }
                    case "int":
                    {
                        $this->defaultValue[$fieldDefinition["fieldname"]] = 0;
                        break;
                    }
                    case "decimal":
                    {
                        $this->defaultValue[$fieldDefinition["fieldname"]] = 0.0;
                        break;
                    }
                    case "date":
                    {
                        $defaultDate = new DateTime('1900-01-01');
                        $this->defaultValue[$fieldDefinition["fieldname"]] = $defaultDate->format("m/d/y, g:i a");
                        //date("m/d/y, g:i a", mktime(0, 0, 0, 1, 1, 1900));
                        //date("m/d/y, g:i a");
                        break;
                    }
                }
            }
        }
    }
    function clearString($string)
    {
        /*$spanishChar = array("á", "é", "í", "ó", "ú", "ñ");
        $charEncode = array("&aacute;", "&eacute;", "&iacute;", "&oacute;", "&uacute;", "&ntilde;");

        return str_replace($spanishChar, $charEncode, $string);*/
        return htmlspecialchars($string, REPLACE_FLAGS, CHARSET);
    }
}
?>