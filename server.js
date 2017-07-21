let express          = require('express'),
    app              = express(),
    config           = require('./config/config'),
    Poi              = require('./api/models/model'),
    bodyParser       = require('body-parser'),
    cors             = require('cors'),
    expressWinston   = require('express-winston-2'),
    winston          = require('winston'),
    helmet           = require('helmet'),
    methodOverride   = require('method-override'),
    compression      = require('compression');


app.use(bodyParser.urlencoded({ extended: true }));
app.use(bodyParser.json());
app.use(compression());
app.use(methodOverride());
app.use(cors());
app.use(helmet());

if (app.get('env') == 'development') {
    winston.level = 'debug';

    app.use(expressWinston.logger({
        transports: [
            new winston.transports.Console({
                json: true,
                colorize: true,
                level: config.get('Customer.log.level')
            })
        ]
    }));
}


let routes = require('./api/routes/v1/routes');
routes(app);

if (app.get('env') == 'development') {
    app.use(expressWinston.errorLogger({
        transports: [
            new winston.transports.Console({
                json: true,
                colorize: true,
                level: config.get('Customer.log.level')
            })
        ]
    }));
}

app.listen(config.get('Customer.dbConfig.port'), () => {
    console.log('Pois RESTful API server started on: ' + config.get('Customer.dbConfig.port'));
});

module.exports = app;
