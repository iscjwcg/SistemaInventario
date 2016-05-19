<?php
include 'dataobject.php';

//header('Content-Type: text/xml; charset=utf-8');

$opcionDO = DataObjectMaker::NewObject("opcion");
$numRecords = $opcionDO->GetRecords(null, "tipo = 'modulo'");

if($numRecords > 0)
{
    $counter = 1;
    $tabs = "";
    foreach($opcionDO->rows as $row)
    {
        $tabs .= ($tabs != "" ? "," : "") . "{ id: 'tab$counter', caption: '".$row["titulo"]."', onClick: loadModule, url: '".$row["url"]."' }";
        $counter++;
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
        <div class="north">
            <div id="tabs" style="width: 100%;"></div>
        </div>
        <div class="center">
            <iframe id="framePage" class="" frameborder="0" src="" ></iframe>
        </div>
    </div>

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="./scripts/jquery-2.2.1.min.js"></script>
    <script src="./w2ui/w2ui-1.4.3.min.js"></script>
    <!--Layout-->
    <script src="./scripts/jquery.sizes.js"></script>
    <script src="./scripts/jlayout.border.js"></script>
    <script src="./scripts/jquery.jlayout.js"></script>
    <!--General-->
    <script src="./scripts/main.js"></script>
    <script>
        <?php echo "var data = {tabs:[$tabs] };"; ?>
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
            $('#tabs').w2tabs({
                name: 'tabs',
                active: 'tab1',
                routeData: { id: 14 },//idUser
                right: '<div><spam id="configure" class="w2ui-tab configure"><a href="./configuracion.php"><img src="./styles/images/settings.png" alt="Configuraci&oacute;n" height="25" width="25"></a></span></div>',
                tabs: data.tabs
            });
            
            $("div.w2ui-tab.active")[0].click();
        });
        
        function loadModule(event)
        {
            $('#framePage').attr("src", event.object.url);
        }
    </script>
  </body>
</html>
