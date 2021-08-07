build: destroy setup run composer npm-install npm-build sleep build-db reload-data restart

run:
	@bash -l -c 'docker-compose up -d'

shutdown:
	@bash -l -c 'docker-compose down'

restart:
	@bash -l -c 'docker-compose restart'

php-fpm:
	@bash -l -c 'docker-compose exec php-fpm bash'

php-worker:
	@bash -l -c 'docker-compose exec php-worker bash'

mysql:
	@docker-compose exec mysql_db sh -c "mysql -u poc -ppoc mercure_poc"

mercure:
	@bash -l -c 'docker-compose exec caddy sh'

rebuild-database: drop-db sleep create-db build-db reload-data

create-db:
	@docker-compose exec php-fpm sh -c "bin/console --env=dev doctrine:database:create --no-interaction"

build-db:
	@docker-compose exec php-fpm sh -c "bin/console --env=dev doctrine:migrations:migrate --no-interaction"

reload-data:
	@docker-compose exec php-fpm sh -c "bin/console --env=dev doctrine:fixtures:load --no-interaction"

drop-db:
	@docker-compose exec php-fpm sh -c "bin/console --env=dev doctrine:database:drop --force --no-interaction"

sleep:
	@bash -l -c 'sleep 15'

setup:
	@docker-compose build

destroy:
	@docker-compose rm -v --force --stop || true

composer:
	@docker-compose exec php-fpm sh -c "composer install --dev --no-interaction -o"

npm-install:
	@docker-compose exec php-fpm sh -c "npm install"

npm-build:
	@docker-compose exec php-fpm sh -c "npm run build"