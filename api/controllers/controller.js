'use strict';

let mongoose = require('mongoose'),
    Poi = mongoose.model('Pois');

/**
 * Recupera todos os 'pois', pontos de interesses
 *
 * @param req
 * @param res
 */
exports.getAllPois = function(req, res) {
    Poi.find({}, 'name coordinates _id')
        .then(pois => res.send(pois));
};

/**
 * Cria um novo 'poi', ponto de interesse
 *
 * @param req
 * @param res
 */
exports.createPoi = function(req, res) {
    const poi = new Poi({
        name: req.body.name,
        coordinates: req.body.coordinates
    });
    poi.save()
        .then(newPoi => res.status(201).send({"message": "Poi criado com sucesso", "result": newPoi}))
        .catch(err => res.status(409).send({'error': 'Não foi possível criar o POI.'}));
};

/**
 * Atualiza um 'poi', ponto de interesse
 *
 * @param req
 * @param res
 */
exports.updatePoi = function(req, res) {
    Poi.findOne({_id: req.params.id})
        .then(result => {
            if (result) {
                return Poi.update({_id: req.params.id}, req.body);
            }
        })
        .then((result) => {
            if (result) {
                return res.status(201).send({"message": "Poi atualizado com sucesso", result})
            }
            return res.status(204).send({"message": 'Poi não encontrado.'});
        })
        .catch(err => res.status(404).send({"message": 'Poi não encontrado.'}));
};

/**
 * Remove um 'poi', ponto de interesse
 *
 * @param req
 * @param res
 */
exports.deletePoi = function(req, res) {
    Poi.remove({_id: req.params.id})
        .then((result) => {
            if (result.result.n == 0) {
                res.status(204).send({'message': 'Nenhum POI foi encontrado'});
            } else {
                res.status(200).send({"message": "Poi removido com sucesso", "_id": req.params.id, result})
            }
        })
        .catch(err => res.status(404).send({'message': 'Poi não encontrado.'}));
};

/**
 * Este serviço receberá uma coordenada X e uma coordenada Y, especificando um ponto de referência,
 * em como uma distância máxima (d_max) em metros.
 * O serviço retornará todos os POIs da base de dados que estejam a uma distância
 * menor ou igual a d_max a partir do ponto de referência
 *
 * @param req
 * @param res
 */
exports.getPoisByCoordinates = function (req, res, next) {
    // coordinates [ <longitude> , <latitude> ]
    let coords = [];
    coords[0] = req.query.x;
    coords[1] = req.query.y;

    Poi.find({
            coordinates: {
                $near: coords,
                $maxDistance: req.query.d_max
            }
        },  'name coordinates -_id')
        .exec()
        .then((pois) => {
            if (pois.length == 0) {
                res.status(204).send({'message': 'Nenhum POI foi encontrado'});
            }else {
                res.status(200).send(pois)
            }
        })
        .catch(err => res.status(404).send({'message': 'Para buscar um poi é necessário utilizar um query string como a seguir: /locations?x=:x&y=:y&d_max=:d_max'}));
};
