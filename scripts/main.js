var moduleObj = {
    genrateTabs: function ()
    {
        $("#tabs a").each(function(){
            $(this).click(function() {
                var pageSource = $(this).attr("data-source-page");
                $($("#tabs a.tabActive")[0]).removeClass("tabActive");
                $("#framePage")[0].src = pageSource;
                $(this).addClass("tabActive");
            });
        });
    }
};

var mainObj = {};

function requestObject(url, returnType, parameters, beforeSendFunc, successfulRequestFunc, failureRequestFunc)
{
    $.ajax({
      url: getServer() + url,
      type: 'post',
      data: parameters,
      success: function(data, status) {
        successfulRequestFunc.call(null, JSON.parse(data));
      },
      error: function(xhr, desc, err) {
        console.log(xhr);
        console.log("Details: " + desc + "\nError:" + err);
      }
    });

/*
beforeSend: beforeSendFunc ? beforeSendFunc : beforeSendFuncDefault
.fail(failureRequestFunc ? failureRequestFunc : failureRequestFuncDefault);*/
}

function beforeSendFuncDefault(xhr){};
function successfulRequestFuncDefault(data)
{
	alert("test");
};
function failureRequestFuncDefault(){};



function getURLParameter(name) {
    return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null;
}

function getServer() {
    var urlFormat = location.protocol + '//' + location.host + location.pathname;
    var lastIndex = urlFormat.lastIndexOf("/") + 1;
    urlFormat = urlFormat.substring(0, lastIndex);
    return urlFormat;
}
function goToPage(page) {	document.location = page;	}	//	window.location = page;
function validarUsuario(user, pass) {
    $.ajax({
        type: "POST",
        url: getServer() + 'validarSesion.php',
        dataType: "xml",
        data: { user: (user ? user : '') , pass: (pass ? pass : '') },
        success: function(xml) {
            $(xml).find('data').each(function() {
                var status = $(this).attr('status');
                var tipo = $(this).attr('tipo');
                //var data = $(this).text();
                if(status == 1) {
                    $("#linkAdmon").html("<a href='./' title='Administrar.'><img src='./Images/home.png' class='userbar' />Inicio</a>");
                    $("#blockUsuario a:first").attr("href", "javascript:verPerfilAdmon();");
                }
                else
                    goToPage('./login.html');
            });
        }
    });
}
function iniciarSesion() {
    validarUsuario($('#user').val(), $('#pass').val());
}
function cerrarSesion() {
//	document.location.reload(true);
	$.ajax({
		url: getServer() + "cerrarSesion.php",
		success: function(data) {	goToPage('./');	}
	});
}