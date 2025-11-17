# MongoDB Connection Setup

## Current Status

✅ **Installed:**
- MongoDB PHP extension (`ext-mongodb`) v1.20.1
- `mongodb/mongodb` PHP library v2.1.2
- `jenssegers/mongodb` Laravel package v5.5.0
- MongoDB connection configured in `config/database.php`
- Test command created: `php artisan test:mongo`

## Issue Encountered

TLS handshake error when connecting to MongoDB Atlas cluster:
```
TLS handshake failed: error:0A000438:SSL routines::tlsv1 alert internal error
```

This is a known issue with MongoDB PHP driver on Windows when connecting to Atlas clusters using certain TLS configurations.

## Workarounds

### Option 1: Use MongoDB Compass or mongosh
Connect via MongoDB Compass (GUI) or mongosh (CLI) which have their own SSL implementations:
```bash
mongosh "mongodb+srv://fuyadswe:%40geif247@cluster0.ifcb6cw.mongodb.net/?retryWrites=true&w=majority"
```

### Option 2: Use Local MongoDB
Install MongoDB Community Server locally and connect without TLS:
```bash
# In .env
MONGO_DSN="mongodb://127.0.0.1:27017"
MONGO_DATABASE=laravel
```

### Option 3: Try Different PHP Build
The issue may be related to the PHP build (thread-safe vs non-thread-safe). Consider:
1. Using WSL2 with PHP/MongoDB Linux binaries
2. Using XAMPP/WAMP which bundles compatible SSL libraries
3. Using Docker with official PHP images

### Option 4: Use REST API
MongoDB Atlas provides a Data API (REST) that bypasses the driver entirely:
```php
// Use HTTP client to query MongoDB Atlas Data API
$response = Http::withHeaders([
    'api-key' => env('MONGO_API_KEY'),
])->post('https://data.mongodb-api.com/app/YOUR-APP-ID/endpoint/data/v1/action/insertOne', [
    'dataSource' => 'Cluster0',
    'database' => 'laravel',
    'collection' => 'test_collection',
    'document' => ['message' => 'Hello from Laravel!'],
]);
```

## Connection String in .env

```env
MONGO_DSN="mongodb+srv://fuyadswe:%40geif247@cluster0.ifcb6cw.mongodb.net/?retryWrites=true&w=majority&tls=true&tlsAllowInvalidCertificates=true"
MONGO_DATABASE=laravel
```

**Note:** Password contains `@` which is URL-encoded as `%40`.

## Test Command

Run the test command to verify connection:
```bash
php artisan test:mongo
```

## Using MongoDB in Laravel

Once connected, you can use MongoDB with Eloquent models:

```php
// app/Models/MongoModel.php
use Jenssegers\Mongodb\Eloquent\Model;

class MongoModel extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'my_collection';
}
```

Or use the raw MongoDB client:

```php
use MongoDB\Client;

$client = new Client(config('database.connections.mongodb.dsn'));
$collection = $client->selectDatabase('laravel')->selectCollection('test');
$result = $collection->insertOne(['key' => 'value']);
```

## Next Steps

1. **Recommended:** Use WSL2 or Docker for reliable MongoDB connectivity
2. Install MongoDB Community locally for development
3. Or use MongoDB Data API for serverless access
