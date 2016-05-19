<?php
include 'dataobject.php';

header('Content-Type: text/html; charset=utf-8');

$table = isset($_GET['table']) ? $_GET['table'] : "";
$viewname = isset($_GET['view']) ?  $_GET['view'] :  "*";

if($table != "")
{
    $viewDO = DataObjectMaker::NewObject("configuracion");
    if($viewDO->FindRecord(null, "tipo = 'vista' AND nombre = '$viewname' AND tabla = '$table'"))
    {
        $columns = "";
        $records = "";
        $jsonFormat = "";
        $dataObject = DataObjectMaker::NewObject($table);
        /* { title: "Oficina" },
        * { title: "Extn.", orderData: [], orderable: false, searchable: false, visible: false } */
        foreach ($dataObject->metadata->fieldDefinition as $key => $fieldDefinition) {
        {
            $columns .= ($columns != "" ? "," : "") . "{ title: '".$fieldDefinition["description"]."'".(strtolower($fieldDefinition["isVisible"]) == "0" ? ", visible: false" : "")." }";
        }

        $numRecords = $dataObject->GetRecords(null, $viewDO->entity["condicion"]);
        if($numRecords > 0)
        {
            $records = "";
            foreach($dataObject->rows as $row)
            {
                $jsonFormat = "";
                foreach($dataObject->metadata->fieldDefinition as $key => $fieldDefinition)
                {
                    $jsonFormat .= ($jsonFormat != "" ? "," : "") . "\"".$row[$key]."\"";
                }
                $records .= ($records != "" ? "," : "") . "[ $jsonFormat ]";
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
    <link href="./media/css/jquery.dataTables.min.css" rel="stylesheet">
    <!--General-->
    <link href="./styles/main.css" rel="stylesheet">
</head>
<body>
    <table id="datagrid" class="display" width="100%"></table>
    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="./scripts/jquery-2.2.1.min.js"></script>
    <script src="./media/js/jquery.dataTables.min.js"></script>
    <!--General-->
    <script src="./scripts/main.js"></script>
    <script>
        <?php echo utf8_encode("var data = {columns:[$columns], records:[$records] };"); ?>
        $(document).ready(function() {
            $('#datagrid').DataTable( {
                language: {
                    processing:     "Procesando...",
                    search:         "Buscar:",
                    lengthMenu:     "Muestra _MENU_ elementos",
                    info:           "Mostrando del _START_ al _END_, de _TOTAL_ registros",
                    infoEmpty:      "Mostrando del 0 al 0 de 0 conincidencias",
                    infoFiltered:   "(Buscado en _MAX_ registros)",
                    infoPostFix:    "",
                    loadingRecords: "Cargando...",
                    zeroRecords:    "Nothing found - sorry",
                    emptyTable:     "No hay datos disponibles",
                    paginate: {
                        first:      "Inicio",
                        previous:   "Atras",
                        next:       "Siguiente",
                        last:       "Final"
                    },
                    aria: {
                        sortAscending:  ": ordenar ascendente",
                        sortDescending: ": ordenar descendente"
                    }
                },
                data: data.records,
                columns: data.columns
            } );
        } );
    </script>
  </body>
</html>
<?php
    }
}
?>