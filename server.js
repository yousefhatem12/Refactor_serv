process.env.NODE_ENV = 'production';
const { loadEnvConfig } = require('@next/env');
loadEnvConfig(__dirname);

const fs = require('fs');
const { createServer } = require('http');
const { parse } = require('url');
const next = require('next');

const app = next({ dev: false, dir: __dirname });
const handle = app.getRequestHandler();

const port = process.env.PORT || 3000;

app.prepare()
  .then(() => {
    createServer((req, res) => {
      try {
        fs.appendFileSync(__dirname + '/tmp/access.log', `[${new Date().toISOString()}] ${req.method} ${req.url}\n`);
      } catch(e) {}
      const parsedUrl = parse(req.url, true);
      handle(req, res, parsedUrl);
    }).listen(port, (err) => {
      if (err) throw err;
      console.log(`> Ready on port ${port}`);
    });
  })
  .catch((err) => {
    console.error('Next.js startup error:', err);
  });
