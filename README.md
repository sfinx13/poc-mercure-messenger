# Demo Mercure 

Extract large data in asynchronous way using Messenger and Mercure for live notification

## Installation

Install dependencies
```bash
composer install
npm install
```

Install mercure hub
```bash
docker-compose up -d
```

Start the server in the background
```bash
symfony server:start -d
```

For the use case to export file in async way, running the messenger worker
```bash
bin/console messenger:consume
```
