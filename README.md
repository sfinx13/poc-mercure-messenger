![example workflow](https://github.com/sfinx13/poc-mercure-messenger/actions/workflows/ci.yaml/badge.svg)

# Demo Mercure

Extract large data in asynchronous way using Messenger and Mercure for live notification

<img style="width:50%;max-width: 50%" src="doc/demo_mercure_messenger.gif" alt="Demo mercure messenger">
<img style="width:50%;max-width: 50%" src="doc/demo_mercure.png" alt="Demo mercure">

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
* [Pushing Data to Clients Using the Mercure Protocol](https://symfony.com/doc/current/mercure.html)
* [Messenger: Sync & Queued Message Handling](https://symfony.com/doc/current/messenger.html)
* [Getting started with mercure](https://mercure.rocks/docs/getting-started)
* [Mercure documentation](https://mercure.rocks/docs)
* [Awesome Mercure Resources](https://mercure.rocks/docs/ecosystem/awesome)
* [Using server-sent events](https://developer.mozilla.org/en-US/docs/Web/API/Server-sent_events/Using_server-sent_events)
