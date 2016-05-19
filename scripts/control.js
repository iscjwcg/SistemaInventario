var stopped;
var failedField = new Array();
/*function handler( event ) {
  alert( event.data.foo );
}

$( "p" ).bind( "click", { foo: "bar" }, handler );

$( "div.test" ).bind(
{ 
  click: function() { $( this ).addClass( "active" );  },
  mouseenter: function() { $( this ).addClass( "inside" ); },
  mouseleave: function() { $( this ).removeClass( "inside" ); }
});*/

$(document).bind("data:refresh", function(e, myName, myValue) {
    $("[role='field']").each(function(index, control) {
        if($(control).attr("type") == "checkbox")
        {
            $(control).attr("checked", dataObject.data.Header[$(control).attr("id")]);
        }
        else
        {
            $(control).val(dataObject.data.Header[$(control).attr("id")]);
        }
    });
});

$(document).bind("data:validate:save", function(e, myName, myValue) {
    stopped = false;
    failedField = new Array();
    $(".w2ui-required").each(function(index, field) {
        var label = $(field).children("label");
        var input = $(field).children("div").children("[role='field']");
        var value = $(input).val();
        
        if($(input).attr("type") == "number")
        {
           stopped = (isNaN(value) || (!isNaN(value) && parseFloat(value) == 0));
        }
        else
        {
           stopped = ($(input).val() == "");
        }
        if(stopped)
        {
            failedField.push($(label).text());
        }
    });
    stopped = failedField.length > 0;
});

$(document).bind("controls:enable", function(e, myName, myValue) {
    $(".w2ui-field").each(function(index, field) {
        var input = $(field).children("div").find("[role='field'], [type='file']");

        if($(input).attr("type") == "file")
        {
            /*var attachCtrl = $(field).children("div").find("[role='attachment']");
            var field = { type:"file", options:{ placeholder:"" } };
            if(dataObject.mode === "ADD")
            {
                field.options.placeholder = "Arrastrar y soltar o clic para seleccionar archivos a adjuntar";
            }
            else if(dataObject.mode === "EDIT")
            {
                field.options.placeholder = "Buscar files";
            }
            $(attachCtrl).w2field($.extend({}, field.options, { type: field.type, selected: [] }));*/
            if(dataObject.mode === "ADD")
            {
                $(field).children("div").find(".w2ui-enum-placeholder").html("Arrastrar y soltar o clic para seleccionar archivos a adjuntar");
            }
            else if(dataObject.mode === "EDIT")
            {
                $(field).children("div").find(".w2ui-enum-placeholder").html("Buscar");
            }

            $(field).children("div").find(".w2ui-list").removeClass("w2ui-readonly");
            $(field).children("div").find(".w2ui-list").find("div.w2ui-list-readonly").addClass("w2ui-list-remove").removeClass("w2ui-list-readonly");
        }

        $(input).removeAttr("disabled");
        $(input).next().show();

        if($(field).hasClass("required"))
        {
            $(field).removeClass("required").addClass("w2ui-required");
        }
    });

    if(dataObject.mode == "ADD" || dataObject.mode == "EDIT")
    {
        w2ui['toolbar'].set('btn_search',   { disabled : true });
        w2ui['toolbar'].set('btn_add',      { disabled : true });
        w2ui['toolbar'].set('btn_edit',     { disabled : true });
        w2ui['toolbar'].set('btn_delete',   { disabled : true });
        w2ui['toolbar'].set('btn_save',     { disabled : false });
        w2ui['toolbar'].set('btn_cancel',   { disabled : false });
    }
});

$(document).bind("controls:disable", function(e, myName, myValue) {
    $(".w2ui-field").each(function(index, field) {
        var input = $(field).children("div").find("[role='field'], [type='file']");
        
        if($(input).attr("type") == "file")
        {
            /*var attachCtrl = $(field).children("div").find("[role='attachment']");
            var field = { type:"file", options:{ placeholder:"" } };
            if(dataObject.id > 0)
            {
                field.options.placeholder = "Buscar";
            }
            $(attachCtrl).w2field($.extend({}, field.options, { type: field.type, selected: [] }));*/
            if(dataObject.id > 0)
            {
                $(field).children("div").find(".w2ui-enum-placeholder").html("Buscar");
            }
            else
            {
                $(field).children("div").find(".w2ui-enum-placeholder").html("");
            }

            $(field).children("div").find(".w2ui-list").addClass("w2ui-readonly");
            $(field).children("div").find(".w2ui-list").find("div.w2ui-list-remove").addClass("w2ui-list-readonly").removeClass("w2ui-list-remove");
        }

        $(input).attr("disabled", "true");
        $(input).next().hide();

        if($(field).hasClass("w2ui-required"))
        {
            $(field).removeClass("w2ui-required").addClass("required");
        }
    });

    if(dataObject.mode == "VIEW")
    {
        w2ui['toolbar'].set('btn_search',   { disabled : false });
        w2ui['toolbar'].set('btn_add',      { disabled : false });
        w2ui['toolbar'].set('btn_edit',     { disabled : dataObject.id == 0 });
        w2ui['toolbar'].set('btn_delete',   { disabled : dataObject.id == 0 });
        w2ui['toolbar'].set('btn_save',     { disabled : true });
        w2ui['toolbar'].set('btn_cancel',   { disabled : true });
    }
});

function loadSearch(url, parameters)
{
    $.ajax({
        url: getServer() + url,
        type: 'post',
        data: parameters,
        success: function(data, status) {
            $("#contentsearch").html(data);
        },
        error: function(xhr, desc, err) {
            console.log(xhr);
            console.log("Details: " + desc + "\nError:" + err);
        }
    });
}

function uploadFiles(id)
{
    $("[role='attachment']").each(function(index, control) {
        var filesToUpload = $(control).data('selected');
        
        if(filesToUpload.length > 0)
        {
            var formData = new FormData();
            var fileCtrl = $(control).parent().parent().find("[type=file]");

            $.each(fileCtrl[0].files, function(key, value)
            {
                formData.append("attachment"+key, value);
            });

            var request = new XMLHttpRequest();
            request.open("POST", "uploadFiles.php?table="+dataObject.tableName+"&id="+id+"&files");
            request.send(formData);
        }
    });
}

$(document).ready(function () {
    $("[role='field']").each(function(index, control) {
        $(control).change(function() {
            $(document).trigger("data:changed", [$(control).attr("id"), ($(control).attr("type") == "checkbox" ? $(control)[0].checked : $(control).val())]);
        });
    });
    $("[role='search']").each(function(index, control) {
        $(control).click(function() {
            var parameters = { search: $(control).attr("search") };
            loadSearch("search.php", parameters);
        });
    });
    $("[role='attachment']").each(function(index, control) {
        $(control).w2field('file', { placeholder:"" });
    });
    /*$('#fileTree').fileTree({ root: './Documentos/', script: './jqueryFileTree.php' }, function(file) { 
            alert(file);
    });*/
    $("[role='enum']").each(function(index, control) {
        $(control).w2field('enum', { 
            items: [],
            openOnFocus: false,
            selected: [{ id: 0, text: '787979' }, { id: 0, text: '999-87-4654' }]
        });

        var addCtrl = $(control).next("span");
        $(addCtrl).click(function() {
            var person = prompt("Please enter your name", "Harry Potter");

            if (person != null) {
                var selected= $(control).data('selected');
                selected.push({id: 0, text: person });
                
                var field = { type:"enum", options:{} };
                $(control).w2field($.extend({}, field.options, { type: field.type, selected: selected }));
            }
            /*var parameters = { search: $(control).attr("search") };
            loadSearch("search.php", parameters);*/
        });
    });
    setTimeout(function () {
        $("[type='file']").each(function(index, control) {
            $(control).parents(".w2ui-field").children("div").find(".w2ui-list").addClass("w2ui-readonly");
            $(control).parents(".w2ui-field").children("div").find(".w2ui-list").find("div.w2ui-list-remove").addClass("w2ui-list-readonly").removeClass("w2ui-list-remove");
            $(control).attr("disabled", "true");
        });
    }, 100);
});