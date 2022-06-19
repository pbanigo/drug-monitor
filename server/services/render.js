
exports.homeRoutes= function(req, res) {//this listens for a get request for "/" the homepage
  
  res.render('index');
}

exports.addDrug =  function(req, res) {//this listens for a get request for "/add-drug" from any hyperlink
  res.render('add_drug'); //tells server to respond with add_drug.ejs (.ejs is optional)
}

exports.updateDrug =  function(req, res) {
  res.render('update_drug'); 
}
