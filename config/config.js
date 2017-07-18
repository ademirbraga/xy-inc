const config = {
    host: process.env.MONGODB_URL || 'mongodb://localhost/xy-inc',
    port: process.env.PORT || 3000
};

module.exports = config;
