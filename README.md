![example workflow](https://github.com/sfinx13/poc-mercure-messenger/actions/workflows/ci.yaml/badge.svg)

# Demo Mercure

Extract large data in asynchronous way using Messenger and Mercure for live notification

![Screenshoot](doc/demo_mercure_messenger.gif)

![Screenshoot](doc/demo_mercure.png)

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

## Links
* <a href="https://symfony.com/doc/current/mercure.html" target="_blank">Pushing Data to Clients Using the Mercure Protocol</a>
* <a href="https://symfony.com/doc/current/messenger.html" target="_blank">Messenger: Sync & Queued Message Handling</a>
* <a href="https://mercure.rocks/docs/getting-started" target="_blank">Getting started with mercure]</a>
* <a href="https://mercure.rocks/docs" target="_blank">Mercure documentation</a>
* <a href="https://mercure.rocks/docs/ecosystem/awesome" target="_blank">Awesome Mercure Resources</a>
* <a href="https://developer.mozilla.org/en-US/docs/Web/API/Server-sent_events/Using_server-sent_events" target="_blank">Using server-sent events</a>
