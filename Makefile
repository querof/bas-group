
start_containers:
	docker-compose up -d

stop_containers:
	docker stop bas-group-nginx-1 bas-group-fpm-1 bas-group-db-1

restart_containers:
	docker stop bas-group-nginx-1 bas-group-fpm-1 bas-group-db-1
	docker-compose up -d

install:
	docker exec -it bas-group-fpm-1 composer install  --optimize-autoloader
	docker exec -it bas-group-fpm-1 php bin/console doctrine:migrations:migrate --no-interaction

tests:
	docker exec -it bas-group-fpm-1  php ./vendor/bin/phpunit
