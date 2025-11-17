const http = require('http');
const { connectToMongo } = require('./db/connect');

const PORT = process.env.PORT || 3000;

let dbStatus = { connected: false, details: null };

(async function init() {
  try {
    const { client, db } = await connectToMongo();
    dbStatus = { connected: true, details: 'connected' };
    // keep client open for the lifetime of the process
    process.on('SIGINT', async () => {
      try { await client.close(); } catch (e) { }
      process.exit();
    });
  } catch (err) {
    dbStatus = { connected: false, details: err.message };
    console.warn('Warning: could not connect to MongoDB:', err.message);
  }

  const server = http.createServer((req, res) => {
    if (req.url === '/health') {
      res.writeHead(200, { 'Content-Type': 'application/json' });
      res.end(JSON.stringify({ ok: true, db: dbStatus }));
      return;
    }

    res.writeHead(200, { 'Content-Type': 'text/plain' });
    res.end('Project running. Visit /health for status.');
  });

  server.listen(PORT, () => {
    console.log(`Server listening on http://localhost:${PORT}`);
  });
})();
