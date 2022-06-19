const mongoose = require('mongoose');//mongoose is a JS library that works with MongoDB


const connectDb = async () => {//an async function to prevent blocking
    try{
        const conn = await mongoose.connect(process.env.MONGO_STR, {//connect using connection string
        	//outline features that would be used
            useNewUrlParser: true,
            useUnifiedTopology: true,
        })

        //Display on console if connection is successful
        console.log(`Database successfully connected at ${conn.connection.host}`);
    }catch(err){//catch errors
        console.log(err);
        process.exit(1);//exit from the process on error
    }
}

module.exports = connectDb; //exports the connection function for use anywhere