const axios = require('axios');//http client used for making client & server side http requests in node
const PORT = process.env.PORT || 3100; //uses either what's in our env or 3100 as our port (you can use any unused port)
const BASE_URI = process.env.BASE_URI || 'http://localhost'; //uses either what's in our env or 3100 as our port (you can use any unused port)

exports.homeRoutes= function(req, res) {
    // Make a get request to /api/users
    axios.get(`${BASE_URI}:${PORT}/api/drugs`)//get request to pull drugs
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
