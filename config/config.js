const mongoose = require('mongoose'),
    config   = require('config');

mongoose.Promise = global.Promise;
mongoose.connect(config.get('Customer.dbConfig.host'));

module.exports = config;
