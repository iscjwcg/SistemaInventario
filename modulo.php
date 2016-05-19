<?php
include 'dataobject.php';

//header('Content-Type: text/xml; charset=utf-8');

$tablename = (isset($_GET['table']) ? $_GET['table'] : "");

$configDO = DataObjectMaker::NewObject("configuracion");
$numRecords = $configDO->FindRecord(null, "tipo = 'opciones' AND tabla = '$tablename'");

$vistas = "{ id:'vista0', text:'Todos', onClick: loadPage, url:'./view.php?table=$tablename' }";
$reportes = "";
$acciones = "";
$mantenimiento = "";

if($numRecords > 0)
{
    $data = json_decode($configDO->entity['propiedades'], true);

    $opcionDO = DataObjectMaker::NewObject("opcion");
    
    if(count($data["vistas"]) > 0)
    {
        $counter = 1;
        if($configDO->GetRecords(null, "tipo = 'vista' AND id_configuracion IN(" . implode(",", $data["vistas"]) . ")") > 0)
        {
            $vistas .= ",{ id:'vista$counter', text:'".$row["titulo"]."', onClick: loadPage, url:'".$row["url"]."' }";
        }
    }

    /*if(count($data["reportes"]) > 0)
    {
        $counter = 1;
        if($configDO->GetRecords(null, "tipo = 'reporte' AND id_configuracion IN(" . implode(",", $data["reportes"]) . ")") > 0)
        {$reportes = "";}
    }*/

    if(count($data["acciones"]) > 0)
    {
        $counter = 1;
        if($opcionDO->GetRecords(null, "tipo = 'accion' AND id_opcion IN(" . implode(",", $data["acciones"]) . ")") > 0)
        {
            $acciones = "";
        }
    }

    if(count($data["mantenimiento"]) > 0)
    {
        $counter = 1;
        if($opcionDO->GetRecords(null, "tipo = 'mantenimiento' AND id_opcion IN(" . implode(",", $data["mantenimiento"]) . ")") > 0)
        {
            foreach($opcionDO->rows as $row)
            {
                $mantenimiento .= ($mantenimiento != "" ? "," : "") . "{ id:'mantenimiento$counter', text:'".$row["titulo"]."', onClick: loadPage, url:'".$row["url"]."' }";
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Test</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="./w2ui/w2ui-1.4.3.css" rel="stylesheet">
    <!--General-->
    <link href="./styles/main.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="./bootstrap/js/html5shiv.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
  </head>

  <body>
      <div data-layout='{"type": "border", "hgap": 3, "vgap": 3}' class="layout">
          <div class="west">
              <div id="sidebar" style="border-right: 1px solid #dfdfdf;"></div>
          </div>
          <div class="center">
              <iframe id="frameModule" class="" frameborder="0" src=""></iframe>
          </div>
    </div>
    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="./scripts/jquery-2.2.1.min.js"></script>
    <script src="./w2ui/w2ui-1.4.3.js"></script>
    <!--Layout-->
    <script src="./scripts/jquery.sizes.js"></script>
    <script src="./scripts/jlayout.border.js"></script>
    <script src="./scripts/jquery.jlayout.js"></script>
    <!--General-->
    <script src="./scripts/main.js"></script>
    <script>
        <?php echo utf8_encode("var data = {vistas:[$vistas], reportes:[$reportes], acciones:[$acciones], mantenimiento:[$mantenimiento] };"); ?>
        jQuery(function($)
        {
            var container = $('.layout');

            function relayout() {
                container.layout({resize: false});
            }
            relayout();

            $(window).resize(relayout);
        });
        $(function () {
            $('#sidebar').w2sidebar({
                name: 'sidebar',
                nodes: [ 
                    { id: 'views', text: 'Vistas', img: 'img_view', expanded: true, group1: true,
                      nodes: data.vistas
                    },
                    { id: 'reports', text: 'Reportes', img: 'img_report', expanded: true, group1: true,
                      nodes: data.reportes
                    },
                    { id: 'actions', text: 'Acciones', img: 'img_action', expanded: true, group1: true,
                      nodes: data.acciones
                    },
                    { id: 'maintenance', text: 'Mantenimiento', img: 'img_maintenance', expanded: true, group1: true,
                      nodes: data.mantenimiento
                    },
                ]
            });

            w2ui['sidebar'].click('vista0', event); 
        });
        
        function loadPage(event)
        {
            $('#frameModule').attr("src", event.object.url);
        }
    </script>
  </body>
</html>
