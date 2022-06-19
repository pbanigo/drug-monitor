const axios = require('axios');//http client used for making client & server side http requests in node

exports.homeRoutes= function(req, res) {
    // Make a get request to /api/users
    axios.get(`${process.env.BASE_URI}:${process.env.PORT}/api/drugs`)//get request to pull drugs
        .then(function(response){
            res.render('index', { drugs : response.data });// response from API request stored as drugs
        })
        .catch(err =>{
            res.send(err);
        })
}

exports.addDrug =  function(req, res) {//this listens for a get request for "/add-drug" from any hyperlink
  res.render('add_drug'); //tells server to respond with add_drug.ejs (.ejs is optional)
}

exports.updateDrug =  function(req, res) {
  res.render('update_drug'); 
}
