<?php
include 'dataobject.php';

//header('Content-Type: text/html; charset=utf-8');

$data = isset($_POST['data']) ? json_decode($_POST['data'], true) : "";
$option = isset($_POST['option']) ? $_POST['option'] : "";

$jsonFormat = "";
$id = intval($data["id"]);
$table = "".$data["tableName"];

$dataObject = DataObjectMaker::NewObject($table);
switch ($option)
{
    case "l":
    {
        $dataObject->Load($id);
        break;
    }
    case "a":
    {
        $dataObject->Add();
        break;
    }
    case "e":
    {
        //validar registro en uso (generar tabla)
        $dataObject->Load($id);
        $dataObject->Edit();
        break;
    }
    case "c":
    {
        if($id > 0){
            $dataObject->Load($id);
        }
        $dataObject->Cancel();
        break;
    }
    case "w":
    {
        $dataObject->Load($id);
	$dataObject->Edit();

        foreach ($dataObject->metadata->fieldDefinition as $key => $fieldDefinition) {
            $dataObject->entity["$key"] = $data["data"]["Header"]["$key"];
        }

        $dataObject->Save();
        break;
    }
    case "d":
    {
        $dataObject->Load($id);
        $dataObject->Delete();
        break;
    }
}

$data["id"] = $dataObject->idRecord;
$data["mode"] = $dataObject->mode;
$data["data"]["Header"] = $dataObject->entity;
//$data->data->Children = array();
echo json_encode($data);
?>