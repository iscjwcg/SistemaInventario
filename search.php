<?php
include 'dataobject.php';

//header('Content-Type: text/xml; charset=utf-8'); 

$search = isset($_POST['search']) ? $_POST['search'] : "";
$caller = isset($_POST['caller']) ? $_POST['caller'] : "";
$records = "";
$columns = "";

if($search != "")
{
    $tempSearch = explode(".", $search);
    $tablename = $tempSearch[0];
    $fielname = $tempSearch[1];
    
    $searchDO = DataObjectMaker::NewObject($tablename);
    $numRecords = $searchDO->GetRecords($searchDO->metadata->fieldDefinition["$fielname"]["columndefinition"], "");

    $columns = "'$fielname'";
    if($numRecords > 0)
    {
        foreach($searchDO->rows as $row)
        {
            $records .= "<li><p class='$fielname'>".$row[$fielname]."</p></li>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
</head>
<body>
    <div class="title"><?php echo $searchDO->metadata->description; ?></div>
    <div id="datasearch" class="display" width="100%">
        <div><input type="text" class="search" placeholder="Buscar" /></div>
        <div><ul class="list"><?php echo $records; ?></ul></div>
    </div>
    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="./scripts/list.js"></script>
    <script>
        <?php echo "var data = {columns:[$columns] };"; ?>
        var options = { valueNames: data.columns };
        var userList = new List('datasearch', options);
    </script>
  </body>
</html>