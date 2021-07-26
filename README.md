# Demo Mercure 

Extract large data in asynchronous way using Messenger and Mercure for live notification

![Screenshoot](doc/demo_mercure.png)

## Configuration

### env variables
```bash
cp .env.dist .env
```

### JWT(JSON Web Token)
https://symfony.com/doc/current/mercure.html#configuration

## Installation

### Run the app
```bash
docker-compose up -d
```

### Install dependencies
```bash
composer install
npm install
```

### Compile assets for JS/CSS
```bash
npm run build
```

## Command

Send a notification with random message with option if you want to push more messages

Send 10 notifications
```bash
docker exec -it poc-php-fpm bin/console app:send-notif -i 50
```

## Debugging tool

http://localhost:9000/.well-known/mercure/ui/