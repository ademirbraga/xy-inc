//process.env.NODE_ENV = 'test';
let mongoose = require("mongoose");
let Poi = require('../../../api/models/model');
const seeds = require('../../../test/seeds/seeds');

//Require the dev-dependencies
let chai = require('chai');
let chaiHttp = require('chai-http');
let server = require('../../../server');
let should = chai.should();
let _id;

chai.use(chaiHttp);


describe('Integration test', () => {
    before((done) => {
        Poi.remove({}, (err) => {
            done();
        });
    });

    after(() => {
        mongoose.connection.close();
    });

    /*
      * Test the /GET route
      */
    describe('/GET - getAllPois', () => {
        it('Retornara 0 POIs, pois nenhum POI foi cadastrado ainda.', (done) => {
            chai.request(server)
                .get('/pois')
                .end((err, res) => {
                    res.should.have.status(200);
                    res.body.should.be.a('array');
                    res.body.length.should.be.eql(0);
                    done();
                });
        });
    });

    /*
    * Test the /POST route
    */
    describe('/POST - createPoi {name: um novo POI, coordinates: [10, 20]}', () => {
        it('Cria um novo POI.', (done) => {
            let poi = {
                "name": "um novo poi",
                "coordinates": [27, 12]
            };

            chai.request(server)
                .post('/pois')
                .send(poi)
                .end((err, res) => {
                    res.should.have.status(201);
                    res.body.should.be.a('object');
                    res.body.should.have.property('message').eql('Poi criado com sucesso');
                    done();
                });
        });
    });

    /*
      * Test the /GET route
      */
    describe('/GET - getAllPois', () => {
        it('Retornara 1 POI, após o cadastro anterior.', (done) => {
            chai.request(server)
                .get('/pois')
                .end((err, res) => {
                    res.should.have.status(200);
                    res.body.should.be.a('array');
                    res.body.length.should.be.eql(1);
                    done();
                });
        });
    });

    /**
     * test /GET para pois proximos
     */
    describe('/GET /locations?x=20&y=10&d_max=10', () => {
        it('Retornara 1 POI proximo a coordenada informada', (done) => {
            chai.request(server)
                .get('/locations?x=20&y=10&d_max=10')
                .end((err, res) => {
                    res.should.have.status(200);
                    res.body.should.be.a('array');
                    res.body.length.should.be.eql(1);
                    done();
                });
        });
    });

    /*
     * Test the /PUT/:id route
     */
    describe('/PUT/:id', () => {
        it('Atualiza o POI informado', (done) => {
            chai.request(server)
                .get('/pois')
                .end((err, poi) => {
                    chai.request(server)
                        .put('/pois/' + poi.body[0]['_id'])
                        .send({name: "Poi atualizado", coordinates: [100, 100]})
                        .end((err, res) => {
                            res.should.have.status(201);
                            res.body.should.have.property('message').eql('Poi atualizado com sucesso');
                            done();
                        });
                });
        });
    });

    /**
     * test /GET para pois proximos
     */
    describe('/GET /locations?x=2&y=1&d_max=0', () => {
        it('Retornara 0 POI com a coordenada informada, pois o que existia foi atualizado e está fora da área de cobertura da distância máxima informada', (done) => {
            chai.request(server)
                .get('/locations?x=2&y=1&d_max=0')
                .end((err, res) => {
                    res.should.have.status(204);
                    done();
                });
        });
    });

    /*
     * Test /DELETE/:id poi
     */
    describe('/DELETE - deletePoi', () => {
        it('Remove um POI informado', (done) => {
            chai.request(server)
                .get('/pois')
                .end((err, poi) => {
                    chai.request(server)
                        .delete('/pois/'+poi.body[0]['_id'])
                        .end((err, res) => {
                            res.should.have.status(200);
                            res.body.should.be.a('object');
                            res.body.should.have.property('message').eql('Poi removido com sucesso');
                            done();
                        });
                });
        });
    });

    /*
      * Test the /GET route
      */
    describe('/GET - getAllPois', () => {
        it('Retornara 0 POIs, pois o único POI cadastrado foi removido.', (done) => {
            chai.request(server)
                .get('/pois')
                .end((err, res) => {
                    res.should.have.status(200);
                    res.body.should.be.a('array');
                    res.body.length.should.be.eql(0);
                    done();
                });
        });
    });

});