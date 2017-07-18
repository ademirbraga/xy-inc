let express          = require('express'),
    app              = express(),
    config           = require('./config/config'),
    mongoose         = require('mongoose'),
    Poi              = require('./api/models/model'),
    bodyParser       = require('body-parser'),
    cors             = require('cors');

mongoose.Promise = global.Promise;
mongoose.connect(config.host);

app.use(bodyParser.urlencoded({ extended: true }));
app.use(bodyParser.json());
app.use(cors());

let routes = require('./api/routes/v1/routes');
routes(app);

app.listen(config.port);

console.log('Pois RESTful API server started on: ' + config.port);
module.exports = app;
