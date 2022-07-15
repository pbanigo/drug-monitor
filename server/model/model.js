const mongoose = require('mongoose');

let schema = new mongoose.Schema({
    name : {
        type : String,// name would be a string
        required: true,// name is a required property
        unique: true // the value of name must be unique
    },
    dosage : {
        type : String,// dosage would be a string
        required: true,// dosage is a required property
    },
    card : {
        type: Number, // card would be a number
        required: true
    },
    pack : {
        type: Number,
        required: true
    },
    perDay : {
        type: Number,
        required: true,
        unique: true
    },
})

const DrugDB = mongoose.model('drugs', schema);//drugs specifies the collection name, database to use was specified in connection string

module.exports = DrugDB;