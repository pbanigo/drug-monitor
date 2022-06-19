const express = require('express');// As in the server.js
const route = express.Router(); //Allows us use express router in this file
const services = require('../services/render');//uses the render.js file from services here


route.get('/', services.homeRoutes);


route.get('/add-drug', services.addDrug)
route.get('/update-drug', services.updateDrug)

module.exports = route;//exports this so it can always be used elsewhere
