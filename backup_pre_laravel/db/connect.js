const { MongoClient } = require('mongodb');

async function connectToMongo() {
	const uri = process.env.MONGO_URI || 'mongodb://127.0.0.1:27017';
	const client = new MongoClient(uri, { serverSelectionTimeoutMS: 5000 });
	await client.connect();
	const db = client.db(process.env.MONGO_DB || 'test');
	return { client, db };
}

module.exports = { connectToMongo };

