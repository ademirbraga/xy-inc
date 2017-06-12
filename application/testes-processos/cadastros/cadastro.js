var url = 'http://xyinc.dev/';


var casper = require('casper').create({
    verbose: true,
    logLevel: "debug"
});

casper.options.viewportSize = { width: 950, height: 950 };

casper.start(url, function() {
    this.echo(this.getTitle());

    casper.then(function() {
        this.echo('saporra funciona', 'GREEN_BAR');
    });

    casper.then(function() {
        casper.capture('search-1.png');
    });

    casper.then(function() {
        casper.click("#a-invoice");
    });
    //q-search
    casper.then(function() {
        casper.capture('search-2.png');
    });

});


casper.run();
