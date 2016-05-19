var dataObject;
function dataClass()
{
    this.tableName = "";
    this.id = 0;
    this.mode = "";
    this.data = { Header:{}, Children:[] };
    this.errormessage = "";
    this.requestObject = null;
    this.getResponse = function(data)
    {
        if((dataObject.mode == "ADD" || dataObject.mode == "EDIT") && data.mode == "VIEW")
        {
            uploadFiles(data.id);
        }

        jQuery.extend(dataObject, data);

        $(document).trigger("data:refresh");
        
        if(dataObject.mode == "ADD" || dataObject.mode == "EDIT")
        {
            $(document).trigger("controls:enable");
        }
        else if(dataObject.mode == "VIEW")
        {
            $(document).trigger("controls:disable");
        }
    };
    this.loadRecord = function(){
        var data;

        //dataObject.mode = "VIEW";
        data = JSON.stringify(dataObject);
        new requestObject("requestBO.php", "json", {data:data, option:"l"}, null, this.getResponse, null);
    };
    this.addRecord = function(){
        var data;

        //dataObject.mode = "ADD";
        dataObject.data = { Header:{}, Children:[] };
        data = JSON.stringify(dataObject);
        new requestObject("requestBO.php", "json", {data:data, option:"a"}, null, this.getResponse, null);
    };
    this.editRecord = function(){
        var data;

        //dataObject.mode = "EDIT";
        data = JSON.stringify(dataObject);
        new requestObject("requestBO.php", "json", {data:data, option:"e"}, null, this.getResponse, null);
    };
    this.cancelRecord = function(){
        var data;
        
        //dataObject.mode = "VIEW";
        data = JSON.stringify(dataObject);
        new requestObject("requestBO.php", "json", {data:data, option:"c"}, null, this.getResponse, null);
    };
    this.saveRecord = function(){
        var data;
        
        $(document).trigger("data:validate:save");
        if(stopped)
        {
            //jAlert("Los campos: " + failedField.join(", ") + " son requeridos.", "Datos requeridos.");
            w2alert("Los siguientes campos son requeridos: " + failedField.join(", ") + ".", "Datos requeridos.");
        }
        else
        {
            //dataObject.mode = "VIEW";
            data = JSON.stringify(dataObject);
            new requestObject("requestBO.php", "json", {data:data, option:"w"}, null, this.getResponse, null);

            refreshSearch();
        }
    };
    this.deleteRecord = function(){
        var data;

        //dataObject.mode = "VIEW";
        data = JSON.stringify(dataObject);
        new requestObject("requestBO.php", "json", {data:data, option:"d"}, null, this.getResponse, null);
    };
};

function refreshSearch()
{
}

function loadAction(id)
{
    if(id)
    {
        dataObject.id = id;
    }

    dataObject.loadRecord();
}

function addAction(id)
{
    if(id)
    {
        dataObject.id = id;
    }

    dataObject.addRecord();
}

function editAction()
{
    dataObject.editRecord();
}

function deleteAction()
{
    dataObject.deleteRecord();
}

function saveAction()
{
    dataObject.saveRecord();
}

function cancelAction()
{
    dataObject.cancelRecord();
}

$(document).ready(function () {
    dataObject = new dataClass();
    dataObject.tableName = getURLParameter("table") ? getURLParameter("table") : "";
    dataObject.id = getURLParameter("id") ? parseInt(getURLParameter("id")) : 0;
    dataObject.mode = getURLParameter("mode") ? getURLParameter("mode") : "VIEW";

    if(dataObject.id > 0)
    {
        dataObject.loadRecord();
    }
});

$(document).bind("data:changed", function(e, fieldName, currentValue) {
    dataObject.data.Header[fieldName] = currentValue;
});