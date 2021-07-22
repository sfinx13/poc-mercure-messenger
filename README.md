# Demo Mercure 

Extract large data in asynchronous way using Messenger and Mercure for live notification

![Screenshoot](doc/demo_mercure.png)

## Installation

Install dependencies
```bash
composer install
npm install
```

Compile assets
```bash
npm run build
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

## Configuration 

### env variables
```bash
cp .env.dist .env
```
### JWT(JSON Web Token)
https://symfony.com/doc/current/mercure.html#configuration

## Command

Send a notification with custom message
```bash
bin/console app:send-notification "Add a message"
```

## Debugging tool

http://localhost:9000/.well-known/mercure