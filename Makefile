.DEFAULT_GOAL := help
.PHONY: help

help:
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
	| sed -n 's/^\(.*\): \(.*\)##\(.*\)/\1\3/p' \
	| column -t  -s ':' \

build: destroy setup run composer npm-install npm-build sleep build-db reload-data restart ## : Project setup

run: ## : Start containers
	@bash -l -c 'docker-compose up -d'

shutdown: ## : Shutdown containers
	@bash -l -c 'docker-compose down'

restart: ## : Restart containers
	@bash -l -c 'docker-compose restart'

php-fpm: ## : Connect php-fpm container
	@bash -l -c 'docker-compose exec php-fpm bash'

php-worker: ## : Connect php-worker container
	@bash -l -c 'docker-compose exec php-worker bash'

mysql: ## : Connect mysql container
	@docker-compose exec mysql_db sh -c "mysql -u poc -ppoc mercure_poc"

mercure: ## : Connect container mercure
	@bash -l -c 'docker-compose exec caddy sh'

rebuild-database: drop-db sleep create-db build-db reload-data ## : Rebuild database

create-db: ## : Create database
	@docker-compose exec php-fpm sh -c "bin/console --env=dev doctrine:database:create --no-interaction"

build-db: ## : Execute migrations database
	@docker-compose exec php-fpm sh -c "bin/console --env=dev doctrine:migrations:migrate --no-interaction"

reload-data: ## : Reload fixtures database
	@docker-compose exec php-fpm sh -c "bin/console --env=dev doctrine:fixtures:load --no-interaction"

drop-db: ## : Drop database
	@docker-compose exec php-fpm sh -c "bin/console --env=dev doctrine:database:drop --force --no-interaction"

sleep: ## : Sleep 15 seconds
	@bash -l -c 'sleep 15'

setup: ## : Setup build
	@docker-compose build

destroy: ## : Removes stopped service containers.
	@docker-compose rm -v --force --stop || true

composer: ## : Install composer dependencies
	@docker-compose exec php-fpm sh -c "composer install --dev --no-interaction -o"

npm-install: ## : Install npm dependencies
	@docker-compose exec php-fpm sh -c "npm install"

npm-build: ## : Build npm dependencies
	@docker-compose exec php-fpm sh -c "npm run build"