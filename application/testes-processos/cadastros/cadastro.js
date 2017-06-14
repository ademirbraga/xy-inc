var telaLogin  = require('../telas/login.js');
var telaModelo = require('../telas/modelo.js');
var jsonModelo = require('../data/modelo.json');

var url = 'http://xyinc.dev/';

var casper = require('casper').create({
    verbose: true,
    //logLevel: "debug"
});

casper.options.viewportSize = { width: 950, height: 950 };

casper.start(url, function() {
    this.echo(this.getTitle());

    casper.then(function() {
        this.echo('Entrando na tela de login', 'GREEN_BAR');
    });

    //setando os dados para login
    casper.then(function() {
        telaLogin.login('admin@admin.com', 'password');
    });

    this.wait(1000, function() {
        this.echo("Aguardando validação.");
    });

    casper.then(function() {
        this.echo('Login realizado', 'GREEN_BAR');
    });

    //entrar na tela de modelos
    telaModelo.entrarNaTela();

    this.wait(1000, function() {
        this.echo("Entrando na tela de modelos.");
    });


    casper.then(function() {
        telaModelo.cadastrar(jsonModelo);
    });



    casper.then(function() {
        this.echo("Realizar logoff.");

        casper.then(function() {
            telaLogin.logout();

            casper.then(function() {
                casper.capture('logout.png');
            });
        });
    });

});


casper.run();
