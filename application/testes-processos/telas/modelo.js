
exports.entrarNaTela = function() {
    casper.then(function () {
        casper.thenOpen('http://xyinc.dev/modelo');
    });
};

exports.cadastrar = function(modelo) {
    casper.then(function () {
        this.click("#btn-novo");
    });

    casper.then(function () {
        casper.thenOpen('http://xyinc.dev/modelo/registro');
    });

    casper.wait(1000, function() {
        this.echo("Click botão novo.");
    });

    casper.then(function() {
        casper.capture('modelo-1.png');
    });

    casper.then(function() {
        casper.sendKeys('#nome_modelo', modelo.nome_modelo);
    });


    casper.then(function () {
        //var ckeditor = document.querySelector('#editor1').contentWindow.CKEDITOR;
        //ckeditor.instances['editor1'].setData('sadsadsadasd');
    });

    casper.then(function () {
        this.click("#btn-input");
    });

    casper.then(function() {
        casper.capture('modelo-2.png');
    });

    casper.then(function () {
      //  this.click("#btn-enviar");
    });
};