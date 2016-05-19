<?php
include 'dataobject.php';
include 'xml.php';

header('Content-Type: text/html; charset=utf-8');

$table = isset($_GET['table']) ? $_GET['table'] : "";
$numRows = 0;

if($table != "")
{
    $listResult = "";
    $columns = "";
    $dataObject = DataObjectMaker::NewObject($table);
    if (file_exists("./screens/$table.xml"))
    {
        $columns = "'$dataObject->keyFields'";
        $numRows = $dataObject->GetRecords($dataObject->keyFields);
        if($numRows > 0)
        {
            foreach ($dataObject->rows as $row) {
                $listResult .= '<li onclick="loadAction('.$row["id_$table"].')"><p class="'.$dataObject->keyFields.'">'.join(' ', array_slice($row, 1)).'</span></li>';
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
    <link href="./w2ui/w2ui-1.4.3.css" rel="stylesheet">
    <!--Modal-->
    <link href="./styles/jquery.modal.css" rel="stylesheet" />
    <link href="./highlight/github.css" rel="stylesheet" />
    <!--Dialog-->
    <link href="./dialogs/jquery.alerts.css" rel="stylesheet">
    <!--FileTree
    <link href="./styles/jqueryFileTree.css" rel="stylesheet">-->
    <link href="./filetree/dist/themes/default/style.min.css" rel="stylesheet" />
    <!--General-->
    <link href="./styles/main.css" rel="stylesheet">
</head>
<body>
    <div data-layout='{"type": "border", "hgap": 3, "vgap": 3}' class="layout">
        <div id="search" class="west">
            <div id="filter" class="display" width="100%">
                <div>
                    <input type="text" id="filterinput" class="search" placeholder="Buscar" />
                    <span id="clearfilter" class="clearfilter" onclick="clearFilter();"><img src="./styles/images/img_cleanfilter.png" alt="Limpiar" height="20" width="20"></span>
                    <input type="button" id="closefilter" value="X" onclick="searchAction();">
                </div>
                <div id="result"><ul class="list"><?php echo $listResult; ?></ul></div>
            </div>  
        </div>
        <div class="center w2ui-panel">
            <?php readXMLScreen($table, $dataObject); ?>
        </div>
        <div id="fileTree" style="float: left;"></div>
    </div>
    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="./scripts/jquery-2.2.1.min.js"></script>
    <script src="./w2ui/w2ui-1.4.3.js"></script>
    <!--Modal-->
    <script src="./scripts/jquery.modal.js"></script>
    <script src="./highlight/highlight.pack.js"></script>
    <!--Layout-->
    <script src="./scripts/jquery.sizes.js"></script>
    <script src="./scripts/jlayout.border.js"></script>
    <script src="./scripts/jquery.jlayout.js"></script>
    <!--List-->
    <script src="./scripts/list.js"></script>
    <!--Dialog-->
    <script src="./dialogs/jquery.alerts.js"></script>
    <!--FileTree
    <script src="./scripts/jquery.easing.js"></script>
    <script src="./scripts/jqueryFileTree.js"></script>-->
    <script src="./filetree/dist/jstree.min.js"></script>
    <!--General-->
    <script src="./scripts/main.js"></script>
    <script src="./scripts/data.js"></script>
    <script src="./scripts/control.js"></script>
<?php if(file_exists("./scripts/screen_$table.xml")) { echo "<script src='./scripts/screen_$table.js'></script>"; } ?>
    <script charset="utf-8">
        <?php echo "var data = {columns:[$columns] };"; ?>
        hljs.initHighlightingOnLoad();

        var container = $('.layout');

        function relayout() {
            container.layout({resize: false});
        }
        relayout();

        $(window).resize(relayout);

        if($('#toolbar'))
        {
            $('#toolbar').w2toolbar({
                name: 'toolbar',
                items: [
                    { type: 'html',    id: 'label',         html: '<legend style="margin: 5px 20px;"><h2 id="caption"></h2></legend>' },
                    { type: 'button',  id: 'btn_search',    caption: '<span class="toolbarbutton">Buscar</span>', img: 'icon-search', onClick: searchAction },
                    { type: 'button',  id: 'btn_add',       caption: '<span class="toolbarbutton">Agregar</span>', img: 'icon-add', onClick: addAction },
                    { type: 'button',  id: 'btn_edit',      caption: '<span class="toolbarbutton">Editar</span>', img: 'icon-edit', onClick: editAction, disabled : true },
                    { type: 'button',  id: 'btn_delete',    caption: '<span class="toolbarbutton">Eliminar</span>', icon: 'icon-delete', onClick: deleteAction, disabled : true },
                    { type: 'button',  id: 'btn_save',      caption: '<span class="toolbarbutton">Guardar</span>', icon: 'icon-save', onClick: saveAction, disabled : true },
                    { type: 'button',  id: 'btn_cancel',    caption: '<span class="toolbarbutton">Cancelar</span>', icon: 'icon-cancel', onClick: cancelAction, disabled : true, test: "test" }
                ]
            });
            $('#toolbar h2#caption').html($('#toolbar').attr('screentitle'));
        }
<?php
if($numRows > 0) {
    echo "var options = { valueNames: data.columns }; var userList = new List('filter', options);";
}
?>
        function searchAction()
        {
            $('.west').animate({width: 'toggle'}, {duration: 1, complete: relayout, step: relayout});
        }
        
        function clearFilter()
        {
            if(userList)
            {
                $("#filterinput").val("");
                userList.search('');
            }
        }

        searchAction();
        
        /*$(function () { $('#fileTree').jstree({
  "core" : {
    "animation" : 0,
    "check_callback" : true,
    "themes" : { "stripes" : true },
    'data' : {
      'url' : function (node) {
        return node.id === '#' ?
          'root.json' : 'root.json';
      },
      'data' : function (node) {
        return { 'id' : node.id };
      }
    }
  },
  "types" : {
    "#" : {
      "max_children" : 1,
      "max_depth" : 4,
      "valid_children" : ["root"]
    },
    "root" : {
      "icon" : "/static/3.3.1/assets/images/tree_icon.png",
      "valid_children" : ["default"]
    },
    "default" : {
      "valid_children" : ["default","file"]
    },
    "file" : {
      "icon" : "glyphicon glyphicon-file",
      "valid_children" : []
    }
  },
  "plugins" : [
    "contextmenu", "dnd", "search",
    "state", "types", "wholerow"
  ]
}); });*/
    </script>
  </body>
</html>
<?php
    }
    else
    {
        echo "Error: el archivo $table.xml no existe.";
    }
}
/*$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
fwrite($myfile, "$field => $value");
fclose($myfile); */
?>