'use strict';
let Joi = require('joi');
let validate = require('express-validation');

module.exports = function(app) {
    let pois = require('../../controllers/controller');

    /**
     * rotas para encontrar todos os pontos de interesses e
     * criar um novo ponto de interesse
     *
     * @verbs [GET, POST]
     */
    app.route('/pois')
        .get(pois.getAllPois)
        .post(pois.createPoi);

    /**
     * rota para encontrar os pontos de interesse mais próximos
     * de acorod com as coordenadas informadas
     *
     * @verb [GET]
     */
    app.route('/locations')
        .get(pois.getPoisByCoordinates);

    /**
     * Rotas para atualizar e remover um ponto de interesse
     *
     * @verbs [PUT, DELETE]
     */
    app.route('/pois/:id')
        .put(pois.updatePoi)
        .delete(pois.deletePoi);
};
