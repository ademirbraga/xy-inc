

exports.login = function(email, password) {

    casper.then(function () {
        casper.thenOpen('http://xyinc.dev/login/logar');
    });

    casper.run(function() {
        casper.then(function () {
            casper.sendKeys('#username', email);
            casper.sendKeys('#password', password);
        });

        casper.then(function() {
            casper.capture('login-1.png');
        });

        casper.then(function () {
            this.click('#btn-login');
        });

        casper.then(function() {
            casper.capture('login-2.png');
        });
    });
};

exports.logout = function() {
    casper.then(function () {
        casper.thenOpen('http://xyinc.dev/login/logar');
    });
};