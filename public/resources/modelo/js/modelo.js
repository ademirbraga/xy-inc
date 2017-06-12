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
        CKEDITOR.replace('editor1');

        editor = new $.fn.dataTable.Editor({
            ajax: "../php/staff.php",
            table: "#example",
            fields: [{
                label: "First name:",
                name: "first_name"
            }, {
                label: "Last name:",
                name: "last_name"
            }, {
                label: "Position:",
                name: "position"
            }, {
                label: "Office:",
                name: "office"
            }, {
                label: "Extension:",
                name: "extn"
            }, {
                label: "Start date:",
                name: "start_date",
                type: "datetime"
            }, {
                label: "Salary:",
                name: "salary"
            }
            ]
        });

        // Activate an inline edit on click of a table cell
        $('#example1').on('click', 'tbody td:not(:first-child)', function (e) {
            editor.inline(this);
        });


        $("#example1").DataTable({
            order: [[1, 'asc']],
            columns: [
                {
                    data: null,
                    defaultContent: '',
                    className: 'select-checkbox',
                    orderable: false
                },
                {data: "nome"},
                {data: "tipo"},
                {data: "obrigatorio"},
                {data: "unique"},
                {data: "ativo"}
            ],
            select: {
                style: 'os',
                selector: 'td:first-child'
            },
            buttons: [
                {extend: "create", editor: editor},
                {extend: "edit", editor: editor},
                {extend: "remove", editor: editor}
            ]
        });
    }
});
