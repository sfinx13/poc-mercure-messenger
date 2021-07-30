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

### Create a JWT

Any app MUST bear a JSON Web Token (JWT) to be authorized to **PUBLISH** updates and, sometimes, to **SUBSCRIBE** to the Mercure Hub

#### How to generate one in static way ?

1. Generate one on https://jwt.io/
2. Copy, paste this payload on the *payload section* to be able to publish and subscribe to any topic

```json
{
  "mercure": {
    "publish": [
      "*"
    ], 
    "subscribe" : [
      "*"
    ]
  }
}
```

3. Sign the JWT with the secrey key on *verify signature section* (Ex: !ChangeMe!)
4. Finally edit MERCURE_JWT_TOKEN and MERCURE_JWT_SECRET on .env file

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