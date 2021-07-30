# Demo Mercure

Extract large data in asynchronous way using Messenger and Mercure for live notification

![Screenshoot](doc/demo_mercure.png)

## Configuration

### env variables
```bash
cp .env.dist .env
```

## Installation

### Run the app
```bash
make run
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

### Load data
```bash
make data
```
## Command

Send a notification with random message with option if you want to push more messages

Send 50 notifications
```bash
docker exec -it poc-php-fpm bin/console app:send-notif -i 50
```

## Debugging

Debugging tool: http://localhost:9000/.well-known/mercure/ui/

Upload postman collection [postman/](postman/)