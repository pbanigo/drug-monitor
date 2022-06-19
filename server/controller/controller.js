let Drugdb = require('../model/model');


// creates and saves a new user
exports.create = (req,res)=>{
    // validate incoming request
    if(!req.body){// if content of request (form data) is empty
        res.status(400).send({ message : "Content cannot be emtpy!"});// respond with this
        return;
    }

    //create new user
    const drug = new Drugdb({
        name : req.body.name,//take values from form and assign to schema
        card : req.body.card,
        pack: req.body.pack,
        perDay : req.body.perDay
    })

    //save created user to database
    drug
        .save(drug)//use the save operation on drug
        .then(data => {
            res.send(data) 
            //res.redirect('/add-drug');
        })
        .catch(err =>{
            res.status(500).send({//catch error
                message : err.message || "There was an error while adding the drug"
            });
        });

}


// can either retrieve all users from the database or retrieve a single user
exports.find = (req,res)=>{

    if(req.query.id){//if we are searching for user using their ID
        const id = req.query.id;

        Drugdb.findById(id)
            .then(data =>{
                if(!data){
                    res.status(404).send({ message : "Can't find drug with id: "+ id})
                }else{
                    res.send(data)
                }
            })
            .catch(err =>{
                res.status(500).send({ message: "Error retrieving drug with id: " + id})
            })

    }else{
        Drugdb.find()
            .then(drug => {
                res.send(drug)
            })
            .catch(err => {
                res.status(500).send({ message : err.message || "Error Occurred while retriving user information" })
            })
    }


}


// edits a user selected using their user ID
exports.update = (req,res)=>{

}


// deletes a user using their user ID
exports.delete = (req,res)=>{

}