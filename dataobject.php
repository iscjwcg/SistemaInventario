<?php
include 'metadata.php';
include 'DBManager.php';

//header('Content-Type: text/xml; charset=utf-8');

class DataObjectMaker
{
    static function NewObject($tableName, $isChild = false)
    {
        $class = "class_$tableName";
        if(class_exists($class))
        {
            return new $class($isChild);
        }
        else
        {
            return new BaseObject($tableName, $isChild);
        }
    }
}

class BaseObject
{
    public $tableName;
    public $metadata;
    public $entity;
    public $originalEntity;
    public $rows;
    public $mode;
    public $keyFields;
    public $selectedFields;
    public $idRecord;
    public $condition;
    public $errorMessage;
    public $isChild;
    public $children;

    function BaseObject($tableName, $isChild = false) {
        $this->idRecord = 0;
        $this->tableName = $tableName;
        $this->errorMessage = "";
        $this->isChild = $isChild;
        $this->children = array();

        $this->metadata = new Metadata();
        $this->metadata->getMetadata($tableName);

        $this->entity = $this->metadata->defaultValue;
        $this->originalEntity = $this->entity;

        $this->keyFields = "id_$this->tableName";
        foreach ($this->metadata->keyFields as $key) {
            $this->keyFields = $this->keyFields . "," . $this->metadata->fieldDefinition["$key"]['columndefinition'];
        }
        
        $this->selectedFields = "";
        foreach ($this->metadata->fieldDefinition as $fieldDefinition) {
            $this->selectedFields = $this->selectedFields . $fieldDefinition['columndefinition'] . ",";
        }
        $this->selectedFields = rtrim($this->selectedFields, ",");
    }
    function RegisterChild($childName)
    {
		$name = strtolower($childName);
        $this->children[$name] = DataObjectMaker::NewObject($name, true);
    }
    function Load($id) {
        $this->mode = "VIEW";
        $connector = new DataManager();
        $connector->consultar("SELECT $this->selectedFields FROM $this->tableName WHERE id_$this->tableName = $id;");
        $numRows = $connector->numeroFilas();
        $this->rows = $connector->dataset();
        $this->errorMessage = $connector->error();
        $connector = null;
        
        $ok = $this->errorMessage == "";
        
        if($ok && $numRows == 1) {
            $this->idRecord = $id;
            foreach ($this->children as $childName => $child)
			{
				//$child->GetRecords(null, "id_$this->tableName = $this->idRecord");
                $this->children[$childName]->GetRecords(null, "id_$this->tableName = $this->idRecord");
            }
            foreach ($this->metadata->fieldDefinition as $key => $fieldDefinition) {
                switch (strtolower($fieldDefinition["type"]))
                {
                    case "varchar":
                    case "bit":
                    {
                        $this->entity[$key] = $this->rows[0][$key];
                        break;
                    }
                    case "int":
                    case "decimal":
                    {
                        $this->entity[$key] = 0 + $this->rows[0][$key];
                        break;
                    }
                    case "date":
                    {
                        $tempDate = new DateTime($this->rows[0][$key]);
                        $this->entity[$key] = $tempDate->format("m/d/y, g:i a");
                        break;
                    }
                }
            }
            $this->originalEntity = $this->entity;
        }
        
        return $ok;
    }
    function Add() {
        $this->mode = "ADD";
        $this->idRecord = 0;
        $this->entity = $this->metadata->defaultValue;
        $this->originalEntity = $this->entity;
    }
    function Edit() {
        $this->mode = "EDIT";
    }
    function Cancel() {
        $this->mode = "VIEW";

        if($this->idRecord > 0)
        {
            $this->Load($this->idRecord);
        }
        else
        {
            $this->entity = $this->metadata->defaultValue;
            $this->originalEntity = $this->entity;
        }
    }
    function GetChanges()
    {
        $dataChanged = array();
        foreach($this->entity as $field => $value)
        {
            if($field != "id_$this->tableName")
            {
                $fieldDefinition = $this->metadata->fieldDefinition["$field"];
                $originalValue = $this->originalEntity["$field"];

                switch(strtolower($fieldDefinition["type"]))
                {
                    case "varchar":
                    {
                        if(strcmp($originalValue, $value) != 0) { $dataChanged["$field"] = $value; }
                        break;
                    }
                    case "bit":
                    {
                        if($originalValue == !$value) { $dataChanged["$field"] = $value; }
                        break;
                    }
                    case "int":
                    case "decimal":
                    {
                        if($originalValue != $value) { $dataChanged["$field"] = $value; }
                        break;
                    }
                    case "date":
                    {
                        if($originalValue->diff($value)->days != 0) { $dataChanged["$field"] = $value; }
                        break;
                    }
                }
            }
        }

        return $dataChanged;
    }
    function Save() {
        $this->mode = "VIEW";
        $connector = new DataManager();
        $dataToSave = $this->GetChanges();

        if(count($dataToSave) > 0)
        {
            if($this->idRecord == 0)
            {
                $connector->insertar($this->tableName, $dataToSave);
            }
            else
            {
                $connector->actualizar($this->tableName, $dataToSave, "id_$this->tableName = $this->idRecord");
            }

            $this->errorMessage = $connector->error();
            $ok = $this->errorMessage == "";

            if($ok)
            {
                if($this->idRecord == 0)
                {
                    $connector->consultar("SELECT MAX(id_$this->tableName) AS id_$this->tableName FROM $this->tableName;");
                    $result = $connector->dataset();
                    $this->idRecord = (0 + $result[0]["id_$this->tableName"]);
                }
                $connector = null;

                $this->Load($this->idRecord);
            }
        }
        else
        {
            $this->errorMessage = "No hay datos para guardar.";
            $ok = false;
        }

        return $ok;
    }
    function Delete() {
        $this->mode = "VIEW";
        $connector = new DataManager();
        
        if($this->idRecord > 0)
        {
            $connector->eliminar($this->tableName, "id_$this->tableName = $this->idRecord");
        }
        
        $this->errorMessage = $connector->error();
        $ok = $this->errorMessage == "";
        
        if($ok)
        {
            $this->idRecord = 0;
            $this->entity = $this->metadata->defaultValue;
            $this->originalEntity = $this->entity;
        }
    }
    function FindRecord($fields = "", $condition = "") {
        $this->mode = "VIEW";
        
        if($fields == "")
        {
            $fields = $this->selectedFields;
        }

        if($condition != "")
        {
            $this->condition = " WHERE $condition";
        }

        $connector = new DataManager();
        $connector->consultar("SELECT $fields FROM $this->tableName $this->condition LIMIT 1;");
        $numRows = $connector->numeroFilas();
        $this->rows = $connector->dataset();
        $connector = null;
        
        if($numRows > 0)
        {
            $this->Load(0 + $this->rows[0]["id_$this->tableName"]);
        }

        return $numRows > 0;
    }
    function GetRecords($fields = "", $condition = "") {
        $this->mode = "VIEW";
        
        if($fields == "")
        {
            $fields = $this->selectedFields;
        }

        if($condition != "")
        {
            $this->condition = " WHERE $condition";
        }

        $connector = new DataManager();
        $connector->consultar("SELECT $fields FROM $this->tableName $this->condition;");
        $numRows = $connector->numeroFilas();
        $this->rows = $connector->dataset();
        $connector = null;

        return $numRows;
    }
}
?>