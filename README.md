# Demo Mercure 

Extract large data in asynchronous way using Messenger and Mercure for live notification

## Installation

Install mercure hub 
```bash
docker-compose up -d
```

Start the server in the background
```bash
symfony server:start -d
```

For the use case, to get many message, running the messenger worker
```bash
bin/console messenger:consume
```
