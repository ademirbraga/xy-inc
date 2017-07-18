'use strict';
let mongoose = require('mongoose');
let Schema = mongoose.Schema;

let PoiSchema = new Schema({
    name: {
        type: String,
        required: true,
        unique : true
    },
    coordinates: {
        type: [Number],
        required: true,   // [<longitude>, <latitude>]
        index: '2d'      // create the geospatial index
    }
});

module.exports = mongoose.model('Pois', PoiSchema);
