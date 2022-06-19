const express = require('express');//we installed express using npm previously and we are indicating that it would be used here
const app = express(); //this assigns express to the variable "app" - anything else can be used.
const bodyParser = require('body-parser');//body-parser makes it easier to deal with request content by making it easier to use
const dotenv = require('dotenv').config();//indicates we would be using .env
const morgan = require('morgan');//this logs requests so you can easily troubleshoot
const connectMongo = require('./server/database/connect');//requires connect.js file
const PORT = process.env.PORT || 3100; //uses either what's in our env or 3100 as our port (you can use any unused port)


app.set('view engine', 'ejs');//Put before app.use, etc. Lets us use EJS for views
//use body-parser to parse requests
app.use(bodyParser.urlencoded({extended:true}));
//indicates which is the folder where static files are served from
app.use(express.static('assets'));
//use morgan to log http requests
app.use(morgan('tiny'));

//connect to Database
connectMongo(); 

//load the routes
app.use('/',require('./server/routes/routes'));//Pulls the routes file whenever this is loaded


app.listen(PORT, function() {//specifies port to listen on
	console.log('listening on '+ PORT);
	console.log(`Welcome to the Drug Monitor App at http://localhost:${PORT}`);
})