var F8_KEY = 119;
var F9_KEY = 120;

var Utils = {};

Utils.URL_BASE         = URL_MODULE_BASE;
Utils.isLISTA          = (ACTION != 'registro') ? true : false;

/**
 * Exibe mensagens apos eventos, subindo na tela
 *
 * @param string titulo: titulo que sera exibido na caixa
 * @param string mensagem: mensagem exibida
 * @param string class: gritter-success | gritter-warning | gritter-info
 */
Utils.gritter = function (titulo, mensagem, tipo, callBack, time) {
    $.gritter.add({
        title: titulo,
        text: mensagem,
        class_name: tipo+' gritter-light',
        time: time || 3000
    });

	if (callBack) {
		setTimeout(function () {callBack()}, 4000);
	}
};

Utils.info = function (id, url_base, msg, lista) {
	msg = Utils.isEmpty(msg) ? "Registro salvo com sucesso." : msg;
	
	bootbox.dialog({
		message : "<span class='bigger-160' style='text-align:center'>"+msg+"</span>",
		buttons : {
			"success" : {
				"label" : "<i class='ace-icon fa fa-check'></i> Continuar",
				"className" : "btn-sm btn-success",
				"callback" : function() {
					if (lista) {
						window.location.href = url_base;
					} else {
						window.location.href = url_base + "/registro/"+id;
					}
				}
			},
			"button" : {
				"label" : "Sair",
				"className" : "btn-sm",
				"callback" : function() {
					window.location.href = url_base;
				}
			}
		}
	});
};

Utils.cancelar = function (id, url_base) {
	bootbox.dialog({
		message : "<span class='bigger-160' style='text-align:center'>Deseja realmente cancelar/inativar este registro?</span>",
		buttons : {
			"success" : {
				"label" : "<i class='ace-icon fa fa-check'></i> Continuar",
				"className" : "btn-sm btn-success",
				"callback" : function() {
					Utils.cancelarRegistro(id, url_base);
				}
			},
			"main" : {
				"label" : "Sair",
				"className" : "btn-sm",
				"callback" : function() {
					
				}
			}
		}
	});
};

Utils.fail = function (responseText, status) {
	bootbox.dialog({
		message : "<span class='bigger-160' style='text-align:center'>"+responseText+"</span>",
		buttons : {
			"danger" :
			{
				"label" : "Fechar!",
				"className" : "btn-sm btn-danger",
				"callback": function() {
					
				}
			}
		}
	});
};

Utils.save = function (URL_SAVE, URL_BACK, data, upload, callBack) {
	if (Utils.formValid()) {
		
		$( ".required" ).closest( "div.input-group" ).removeClass('has-error');
		$( ".required" ).closest( "div.form-group" ).removeClass('has-error');
		$(".submit").attr('disabled', 'disabled');
		
		if (upload) {
			var params = {
				method: "POST",
				url: URL_SAVE,
				data: data,
				processData: false,
				contentType: false
			};
		} else {
			var params = {
				method: "POST",
				url: URL_SAVE,
				data: data,
			};
		}
		
		$.ajax(params).
		success(function (res, sucesso, objeto) {
			if (!Utils.isEmpty(objeto.responseText)) {
				var result = jQuery.parseJSON(objeto.responseText);
				
				if (!Utils.isEmpty(result.error)) {
					Utils.fail(result.error);
				} else {
					if (upload) {
						result.url_base = URL_BACK;
					}
					Utils.info(result.id, result.url_base);

                    if (callBack) {
                        callBack();
                    }
				}
			}
			$(".submit").removeAttr('disabled');
		})
		.fail(function (res) {
			if (!Utils.isEmpty(res)) {
				Utils.fail(res.responseText, res.status);
			}
			$(".submit").removeAttr('disabled');
		})
		.done(function () {
			setTimeout(function() {
				$(".submit").removeAttr('disabled');
			}, 1500);
		});
	} else {
        var frm = "frm-crud";
        $( ".form-group .input-group" ).removeClass('has-error');

        var ids = $("#"+frm+" .required").map(function() {
            return this.id;
        }).get();

        for (var i in ids) {
            if (Utils.isEmpty($('#'+ids[i]).val())) {
                $('#'+ids[i]).addClass('has-error');

                $('#'+ids[i]+ ".required" ).closest( "div.input-group" ).addClass('has-error');
                $('#'+ids[i]+ ".required" ).closest( "div.form-group" ).addClass('has-error');

            }
        }
		//$( ".required" ).closest( "div.input-group" ).addClass('has-error');
		//$( ".required" ).closest( "div.form-group" ).addClass('has-error');
		return false;
	}
};

Utils.isEmpty = function (value) {
	return value == '' || value == null || value == undefined;
};

Utils.cancelarRegistro = function (id, URL_DELETE) {
	$.ajax({
  		method: "POST",
		url: URL_DELETE,
		data: {"id" : id}
	}).
	 success(function (res, sucesso, objeto) {
		 if (!Utils.isEmpty(objeto.responseText)) {
				var result = jQuery.parseJSON(objeto.responseText);
				
				if (!Utils.isEmpty(result.error)) {
					Utils.fail(result.error);
				} else {
					Utils.info(result.id, result.url_base, 'Registro cancelado com sucesso.', true);
				}
			}
	 })
	 .fail(function (res) {
		 if (!Utils.isEmpty(res)) {
			 Utils.fail(res.responseText, res.status);
		 }
		 $(".submit").removeAttr('disabled');
	 })
	 .done(function () {
		 setTimeout(function() {
			 $(".submit").removeAttr('disabled');
		 }, 1500);
	 });
};

Utils.formValid = function (frm) {
	frm = frm || "frm-crud";

	jQuery.validator.messages.required = "";

	if ($('#'+frm).valid()) {
		$('.required').removeClass('has-error');
		return true;
	} else {
        $( ".form-group" ).removeClass('has-error');

        var ids = $("#"+frm+" .required").map(function() {
            return this.id;
        }).get();

        for (var i in ids) {
            if (Utils.isEmpty($('#'+ids[i]).val())) {
                $('#'+ids[i]).addClass('has-error');

            }
        }

		return false;
	}
};

Utils.ajax = function (url, params, callBack) {
	$.ajax({
		url: url,
		data: params,
		method: "POST"
	}).success(function (res, sucesso, objeto) {
		if (callBack && !Utils.isEmpty(objeto.responseText)) {
			var result = jQuery.parseJSON(objeto.responseText);
			callBack(result)
		}
	  }).fail(function (res, sucesso, objeto) {
        Utils.gritter("Erro", res.responseText, "gritter-warning");
    });
};

Utils.removeRequired = function (campo) {
	campo.closest( "div.input-group" ).removeClass('has-error');
	campo.closest( "div.form-group" ).removeClass('has-error');
};

Utils.edit = function (id, url) {
	console.log(id, url);
//	window.location.href = url+'/'+id;
};

jQuery(function($) {
	$( "input, select, textarea, radio" ).blur(function() {
		if (!Utils.isEmpty($(this).val())) {
			Utils.removeRequired($(this));
		}
	});
	$(document).on('keyup', function(e){
		if (e.keyCode == F8_KEY) {
			jQuery("#grid-table").jqGrid('filterToolbar',{searchOperators : true});
		} else if (e.keyCode == F9_KEY) {
			jQuery("#grid-table").jqGrid('filterToolbar',{searchOperators : false});
		}
	});


});


Utils.setGridHeader = function (grid, columns) {
    columns.forEach(function (col, idx) {
        var thd = jQuery("thead:first", grid[0].grid.hDiv)[0];
        jQuery("tr.ui-jqgrid-labels th:eq(" + idx + ")", thd).attr("title", col);
    });
};

Utils.getAllDataGrid = function (grid) {
	return $("#"+grid).jqGrid('getRowData');
};

Utils.getAllIdsFromGrid = function (grid) {
	return $('#'+grid).jqGrid('getDataIDs');
};

Utils.getSelectedRowData = function (grid) {
    var myGrid = $('#'+grid),
        selRowId = myGrid.jqGrid ('getGridParam', 'selrow');
    return (selRowId) ? $("#"+grid).jqGrid("getRowData", selRowId) : null;
};


Utils.getCountRows = function (grid) {
    return $("#"+grid).jqGrid('getGridParam', 'records');
};


Utils.getSelectedDataRows = function (grid) {
    var selRowIds = $('#'+grid).jqGrid("getGridParam", "selarrrow");
    var registros = [];

    selRowIds.forEach(function (val, idx) {
        registros.push($("#"+grid).jqGrid("getRowData", val));
    });
    return registros;
};

Utils.getRowDataById = function (grid, rowId) {
    return $("#"+grid).jqGrid("getRowData", rowId);
};

Utils.getSelectedRowIds = function (grid) {
	return $("#"+grid).jqGrid ('getGridParam', 'selrow');
};

Utils.getAllRowIds = function (grid) {
	return $('#'+grid).jqGrid('getDataIDs');
};

Utils.getLastRowId = function (grid) {
	var ids = Utils.getAllRowIds(grid);
	return Utils.isEmpty(ids) ? 0 : parseInt(ids.pop());
};

Utils.setGridHeight = function (grid, height, force) {
	height                = height || window.innerHeight;
	var footerHeight      = $(".footer-content").height();
	var breadCrumbsHeight = $("#breadcrumbs").height();
	var navBarHieght      = $("#navbar").height();
	
	height = (height - footerHeight - breadCrumbsHeight - navBarHieght - 270);
    height = (height >= 250) ? height: 250;
    $('#'+grid).jqGrid('setGridHeight', height);
    $(window).triggerHandler('resize.jqGrid');
};


/**
 * Sistema de notificações
 *
 * @param where {}
 * @param int id_usuario_notificado
 */
Utils.showModalNotificacao = function (where, id_usuario_notificado, cabecalho) {
    $("#conversations").html('');

    Utils.ajax(Utils.URL_NOTIFICACOES + '/getNotificacoes', where, function (notificacoes) {
        if (!Utils.isEmpty(notificacoes)) {
            notificacoes.forEach(function (notificacao, idx) {
                var nota = '<div class="dialogs">'
                    +'<div class="itemdiv dialogdiv">'
                    +'<div class="user">'
                    +'<img alt="Alexa Avatar" src="'+Utils.URL_MEDIA +'/assets/avatars/avatar.png" />'
                    +'</div>'
                    +'<div class="body">'
                    +'<div class="time">'
                    +'<i class="ace-icon fa fa-clock-o"></i>'
                    +'<span class="green">'+notificacao['created_at']+'</span>'
                    +'</div>'
                    +'<div class="name">'
                    +'<a href="#">'+notificacao['notificador']+'</a>'
                    +'</div>'
                    +'<div class="text">'+notificacao['notificacao']+'</div>'
                    +'<div class="tools">'
                    +'</div>'
                    +'</div>'
                    +'</div>'
                    +'</div>';

                $("#conversations").append(nota);
            });
        }

        var conteudo = $("#divNotiticacoes").html();

        bootbox.dialog({
            message : conteudo,
            inputType: 'textarea',
            backdrop: true,
            buttons : {
                "success" : {
                    "label" : "<i class='ace-icon fa fa-share'></i> Enviar",
                    "className" : "btn-sm btn-success",
                    "callback" : function() {
                        var notificacao = where;
                        notificacao.id_usuario_notificado = id_usuario_notificado;
                        Utils.enviarNotificacao(notificacao, cabecalho);
                    }
                },
                "button" : {
                    "label" : "Sair",
                    "className" : "btn-sm",
                    "callback" : function() {

                    }
                }
            }
        });
        $(".modal-content").css({'background-color': '#EFF3F8'});
        $('.scrollable').each(function () {
            var $this = $(this);
            $(this).ace_scroll({
                size: $this.attr('data-size') || 200
            });
        });
    });
};

/**
 * Enviar as notificações para a base
 *
 * @param notificacao {}
 */
Utils.enviarNotificacao = function (notificacao, cabecalho) {
    var rowId = $('#grid-table').jqGrid ('getGridParam', 'selrow');
    var texto = $('.bootbox-body').find('#texto_notificacao').val();

    if (Utils.isEmpty(texto)) {
        Utils.fail("Você não pode enviar uma notificação vazia.");

    } else {
        notificacao.nome_notificacao = cabecalho;
        notificacao.notificacao = texto;

        Utils.ajax(Utils.URL_NOTIFICACOES+'/notificar', notificacao, function (resposta) {
            Utils.gritter('Notificações.', 'Notificação enviada com sucesso.', 'gritter-success');
        });
    }
};


Utils.closeInLineEdit = function (grid, callBack) {
	var ids = $('#'+grid).jqGrid('getDataIDs');
	for (var i in ids) {
		$("#jSaveButton_" + ids[i]).click();
		$("#"+grid).saveRow(ids[i]);
	}
	if (callBack) {
		callBack();
	}
};

Utils.numberFormat = function (valor) {
    var str = valor + ''; 
    x = str.split('.'); 
    x1 = x[0]; x2 = x.length > 1 ? '.' + x[1] : ''; 
    var rgx = /(\d+)(\d{3})/; 
    while (rgx.test(x1)) { 
        x1 = x1.replace(rgx, '$1' + ',' + '$2'); 
    } 
    return (x1 + x2); 
};

String.prototype.replaceAll = function(find, replace) {
    var str = this;
    return str.replace(new RegExp(find.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1"), 'g'), replace);
};

Utils.number_format= function(number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number, 
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals), 
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep, 
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point, 
    s = '',
    toFixedFix = function (n, prec) {
    	var k = Math.pow(10, prec);
        return '' + Math.round(n * k) / k;
    };

    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');

    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }

    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }

    return s.join(dec);
};

Utils.formatterSwitch = function (cellvalue, options, cell) {
    setTimeout(function(){
        $(cell) .find('input[type=checkbox]')
            .addClass('ace ace-switch ace-switch-5')
            .after('<span class="lbl"></span>');
    }, 0);
};


Utils.exportGridPDF = function (grid, title, description) {
    $("#"+grid).jqGrid("exportToPdf",{
        title: title,
        orientation: 'portrait',
        pageSize: 'A4',
        description: description,
        customSettings: null,
        download: 'download',
        includeLabels : true,
        includeGroupHeader : true,
        includeFooter: true,
        fileName : "exportacao.pdf"
    })
};

Utils.exportGridExcel = function (grid) {
    $("#"+grid).jqGrid("exportToExcel",{
        includeLabels : true,
        includeGroupHeader : true,
        includeFooter: true,
        fileName : "exportacao.xlsx",
        maxlength : 200 // maxlength for visible string data
    })
};


Utils.uploadFiles = function (urlUpload, params) {
    var file_data = $('#Files').prop('files')[0];
    var form_data = new FormData();

    if (!Utils.isEmpty(params)) {
       for (key in params) {
		//   form_data.append(key, params[key]);
	   }
	}

    $.ajax({
        url: urlUpload, // point to server-side PHP script
        dataType: 'text',  // what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(response){
        	console.log('php_script_response::',response);
        	var resp = JSON.parse(response)

			if (resp.error) {
                Utils.gritter('Upload de Arquivo', resp.message, 'gritter-warning');
            } else {
                Utils.gritter('Upload de Arquivo', resp.message, 'gritter-success');
			}
        }
    });
};
