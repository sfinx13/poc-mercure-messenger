# Demo Mercure

Extract large data in asynchronous way using Messenger and Mercure for live notification

![Screenshoot](doc/demo_mercure.png)

![Screenshoot](doc/demo_mercure_messenger.gif)

## Installation

```bash
cp .env.dist .env
make build
```

## Usage

* Login page: http://localhost:8080
    * Login: `demo-1`, Password: `demo-1`
    * Login: `demo-2`, Password: `demo-2`
    * Login: `admin`, Password: `admin`
* Notification page: http://localhost:8080/app/notifications
* Command: Send a notification with random message with option if you want to push more messages
    ```bash
    docker exec -it poc-php-fpm bin/console app:send-notif -i 50
    ```

## Debugging

* Debugging tool: http://localhost:9000/.well-known/mercure/ui/
* Upload postman collection [postman/](postman/)