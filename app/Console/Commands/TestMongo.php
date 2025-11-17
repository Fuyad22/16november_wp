<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MongoDB\Client;

class TestMongo extends Command
{
    protected $signature = 'test:mongo';
    protected $description = 'Test MongoDB connection';

    public function handle()
    {
        try {
            $dsn = config('database.connections.mongodb.dsn') 
                ?: sprintf(
                    'mongodb://%s:%s@%s:%s/%s',
                    config('database.connections.mongodb.username'),
                    config('database.connections.mongodb.password'),
                    config('database.connections.mongodb.host'),
                    config('database.connections.mongodb.port'),
                    config('database.connections.mongodb.database')
                );

            $this->info("Connecting to: " . preg_replace('/:[^:@]+@/', ':***@', $dsn));

            $client = new Client($dsn, [
                'tls' => true,
                'tlsAllowInvalidCertificates' => true,
                'tlsAllowInvalidHostnames' => true,
            ]);
            $db = $client->selectDatabase(config('database.connections.mongodb.database'));
            
            // Test insert
            $collection = $db->selectCollection('test_collection');
            $result = $collection->insertOne([
                'message' => 'Hello from Laravel!',
                'timestamp' => new \MongoDB\BSON\UTCDateTime(),
            ]);

            $this->info("✓ Insert successful! ID: " . $result->getInsertedId());

            // Test read
            $doc = $collection->findOne(['_id' => $result->getInsertedId()]);
            $this->info("✓ Read successful: " . $doc['message']);

            // Cleanup
            $collection->deleteOne(['_id' => $result->getInsertedId()]);
            $this->info("✓ MongoDB connection verified!");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("✗ MongoDB connection failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
