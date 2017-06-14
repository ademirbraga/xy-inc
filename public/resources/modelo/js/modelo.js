var editor; // use a global for the submit and return data rendering in the examples

var Controller = {
    id_modelo: ''
};

$(document).ready(function() {

    if (Utils.isLISTA) {

        var dataTable = $('#grid-modelo').DataTable( {
            "processing": true,
            "serverSide": true,
            "ajax":{
                url :Utils.URL_BASE + '/getModelos',
                type: "post",
               // data : {},
                error: function(){  // error handling

                }
            }
        } );

    } else {
       // CKEDITOR.replace('editor1');

        editor = new $.fn.dataTable.Editor({
            ajax: "../php/staff.php",
            table: "#example",
            fields: [{
                label: "Nome:",
                name: "nome"
            }, {
                label: "Tipo:",
                name: "type"
            }, {
                label: "Obrigatório:",
                name: "required"
            }, {
                label: "Tamanho:",
                name: "tamanho"
            }, {
                label: "Unico:",
                name: "unico"
            }, {
                label: "Ativo:",
                name: "ativo"
            }
            ]
        });
        var table = $('#example1').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": Utils.URL_BASE+'/carregarInputs'
        });

        $("#btn-input").click(function() {
            table.row.add({
                "nome": "Campo1",
                "tipo": "string",
                "obrigatorio": "false",
                "unique": "false",
                "ativo": "true"
            }).draw();
        });

    }
});
