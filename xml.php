<?php
chmod("./", 777);
chmod("./screens/", 777);

include 'controls.php';

header('Content-Type: text/xml; charset=utf-8');

function readControls($dataObject, $xmlControls)
{
    $controlType = "";
    $dataSource = "";
    foreach ($xmlControls->type as $type) {
        $controlType = strtolower($type);
    }

    if($controlType == "field")
    {
        foreach ($xmlControls->source as $source) {
            $dataSource = $source;
            $label = $dataObject->metadata->fieldDefinition["$source"]["description"];
            $tipo = strtolower($dataObject->metadata->fieldDefinition["$source"]["type"]);
            $size = $dataObject->metadata->fieldDefinition["$source"]["length"];
            $editable = $dataObject->metadata->fieldDefinition["$source"]["isEditable"];
            $required = $dataObject->metadata->fieldDefinition["$source"]["isRequired"];
            if($dataObject->metadata->fieldDefinition["$source"]["search"] != "")
            {
                $search = $dataObject->metadata->fieldDefinition["$source"]["search"];
            }
            else
            {
                $search = $dataObject->metadata->tableName.".".$source;
            }
        }
        new Field($dataSource, $label, $tipo, $size, $search, $editable, $required);
    }
    else if($controlType == "custom")
    {
        foreach ($xmlControls->source as $source) {
            echo $source;
        }
    }
    else if($controlType == "container")
    {
        $containerTitle = "";
        foreach ($xmlControls->title as $title) {
            $containerTitle = $title;
        }
        echo "<div class='' style='float:left; display: block; width:100%;'>";
        echo "<div style='margin-top: 20px; padding: 3px; font-weight: bold; color: #777; text-align:left;'>$containerTitle</div>";
        foreach ($xmlControls->controls as $controls) {
            echo "<div class='w2ui-group'>";
            foreach ($controls as $control) {
                readControls($dataObject, $control);
            }
            echo "</div>";
        }
        echo "</div>";
    }
}

function readXMLScreen($screenName, $dataObject) {
    $titleScreen = "";
    $label = "";
    $size = 0;
    $search = "";
    $editable = true;
    $xml = simplexml_load_file("./screens/$screenName.xml");
    //print_r($xml);
    foreach ($xml->table as $table) {
    }
    foreach ($xml->title as $title) {
        $titleScreen = $dataObject->metadata->description;
    }
    foreach ($xml->toolbar as $toolbar) {
        echo '<div id="toolbar" screentitle="'.$titleScreen.'"></div>';
    }
    foreach ($xml->body as $body) {
        echo '<div id="form" style="height: 480px;" name="form" class="w2ui-reset">';
        foreach ($body->control as $control) {
            readControls($dataObject, $control);
        }
        echo '</div><div id="modalsearch" style="display:none;"><div id="contentsearch"></div></div>';
    }
}
?>