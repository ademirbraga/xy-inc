//process.env.NODE_ENV = 'test';
let mongoose = require("mongoose");
let Poi = require('../../../api/models/model');
const seeds = require('../../../test/seeds/seeds');

//Require the dev-dependencies
let chai = require('chai');
let chaiHttp = require('chai-http');
let server = require('../../../server');
let should = chai.should();

chai.use(chaiHttp);

describe('Pois', () => {
    beforeEach((done) => {
        Poi.remove({}, (err) => {
            done();
        });

        Poi.insertMany(seeds);
    });

    /*
      * Test the /GET route
      */
    describe('/GET - getAllPois', () => {
        it('Retornara os 6 pois cadastrados via seed.', (done) => {
            chai.request(server)
                .get('/pois')
                .end((err, res) => {
                    res.should.have.status(200);
                    res.body.should.be.a('array');
                    res.body.length.should.be.eql(6);
                    done();
                });
        });
    });

    /**
     * test /GET para pois proximos
     */
    describe('/GET locations?x=20&y=10&d_max=10', () => {
        it('Retornara 4 pois proximos a coordenada informada', (done) => {
            chai.request(server)
                .get('/locations?x=20&y=10&d_max=10')
                .end((err, res) => {
                    res.should.have.status(200);
                    res.body.should.be.a('array');
                    res.body.length.should.be.eql(4);
                    done();
                });
        });
    });

    /**
     * test /GET para pois proximos
     */
    describe('/GET locations?x=2&y=1&d_max=0', () => {
        it('Retornara 0 POI proximos a coordenada informada', (done) => {
            chai.request(server)
                .get('/locations?x=2&y=1&d_max=0')
                .end((err, res) => {
                    res.should.have.status(204);
                    done();
                });
        });
    });

    /**
     * tenta buscar um poi com parametros incorretos
     */
    describe('/GET locations?x=20&y=10', () => {
        it('Retornara mensagem de erro informando os parametros obrigatorios', (done) => {
            chai.request(server)
                .get('/locations?x=20&y=10')
                .end((err, res) => {
                    res.should.have.status(404);
                    res.body.should.have.property('message').eql('Para buscar um poi é necessário utilizar um query string como a seguir: /locations?x=:x&y=:y&d_max=:d_max');
                    done();
                });
        });
    });

    /*
  * Test the /PUT/:id route
  */
    describe('/PUT/:id', () => {
        it('Atualiza poi informado', (done) => {
            let poi = new Poi({name: "Poi a ser atualizado", coordinates: [40, 50]});
            poi.save((err, poi) => {
                chai.request(server)
                    .put('/pois/' + poi._id)
                    .send({name: "Poi atualizado", coordinates: [12, 12]})
                    .end((err, res) => {
                        res.should.have.status(201);
                        res.body.should.have.property('message').eql('Poi atualizado com sucesso');
                        done();
                    });
            });
        });
    });

    /**
     * Atualizar um registro inexistente
     */
    describe('/PUT/:id', () => {
        it('Tentar atualizar poi inexistente - 404', (done) => {
            let poi = new Poi({name: "Poi a ser atualizado2", coordinates: [40, 50]});
            poi.save((err, poi) => {
                chai.request(server)
                    .put('/pois/5966e82cb3bc032bf1d39052x')
                    .send({name: "Poi atualizado2", coordinates: [12, 12]})
                    .end((err, res) => {
                        res.should.have.status(404);
                        res.body.should.have.property('message').eql('Poi não encontrado.');
                        done();
                    });
            });
        });
    });

    describe('/PUT/:id', () => {
        it('Tentar atualizar poi inexistente - 204', (done) => {
            let poi = new Poi({name: "Poi a ser atualizado3", coordinates: [40, 50]});
            poi.save((err, poi) => {
                chai.request(server)
                    .put('/pois/3966e82cb3bc032bf1d39059')
                    .send({name: "Poi atualizado3", coordinates: [40, 40]})
                    .end((err, res) => {
                        res.should.have.status(204);
                        done();
                    });
            });
        });
    });

    /*
  * Test /DELETE/:id poi
  */
    let poiDelete;
    describe('/DELETE - deletePoi', () => {
        it('Remove um poi informado', (done) => {
            poiDelete = new Poi({name: "Poi a ser removido", coordinates: [1, 2]});
            poiDelete.save((err, poiDelete) => {
                chai.request(server)
                    .delete('/pois/' + poiDelete._id)
                    .end((err, res) => {
                        res.should.have.status(200);
                        res.body.should.have.property('message').eql('Poi removido com sucesso');
                        done();
                    });
            });
        });
        it('Tentar remover um poi inexistente - Http:404', (done) => {
            let poi = new Poi({name: "Poi a ser removido", coordinates: [1, 2]});
            poi.save((err, poi) => {
                chai.request(server)
                    .delete('/pois/596d7eaa6b756c3a69479f69x')
                    .end((err, res) => {
                        res.should.have.status(404);
                        done();
                    });
            });
        });
    });

    describe('/DELETE - deletePoi', () => {
        it('Tentar remover um poi que ja foi removido - Http:204', (done) => {
            chai.request(server)
                .delete('/pois/' + poiDelete._id)
                .end((err, res) => {
                    res.should.have.status(204);
                    done();
                });
        });
    });


    /*
  * Test the /POST route
  */
    describe('/POST - createPoi {name: um novo poi, coordinates: [10, 20]}', () => {
        it('Cria um poi correto.', (done) => {
            let poi = {
                "name": "um novo poi",
                "coordinates": [10, 20]
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

    /**
     * tenta criar um poi sem coordenadas
     */
    describe('/POST - createPoi - {name: Restaurante} - Tentando criar um poi sem coordernadas', () => {
        it('Deve retonarnar uma mensagem de erro.', (done) => {
            let poi = {
                name: "Restaurante",
            };
            chai.request(server)
                .post('/pois')
                .send(poi)
                .end((err, res) => {
                    res.should.have.status(409);
                    res.body.should.be.a('object');
                    res.body.should.have.property('error');
                    done();
                });
        });
    });
});
