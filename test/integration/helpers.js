let supertest = require('supertest'),
    chai      = require('chai'),
    app       = require('../../server');

global.app = app;
global.request = supertest(app);
global.expect = chai.expect;
