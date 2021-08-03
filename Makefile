run:
	@bash -l -c 'docker-compose up -d'

rebuild-data: build-db reload-data

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

create-db:
	@docker-compose exec php-fpm sh -c "bin/console --env=dev doctrine:database:create --no-interaction"

build-db:
	@docker-compose exec php-fpm sh -c "bin/console --env=dev doctrine:migrations:migrate --no-interaction"

reload-data:
	@docker-compose exec php-fpm sh -c "bin/console --env=dev doctrine:fixtures:load --no-interaction"

drop-db:
	@docker-compose exec php-fpm sh -c "bin/console --env=dev doctrine:database:drop --force --no-interaction"