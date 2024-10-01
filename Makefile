
start_containers:
	docker-compose up -d

stop_containers:
	docker stop bas-group-nginx-1 bas-group-fpm-1 bas-group-db-1

restart_containers:
	docker stop bas-group-nginx-1 bas-group-fpm-1 bas-group-db-1
	docker-compose up -d

install-dependencies:
	docker exec -it bas-group-fpm-1 composer install --no-dev --optimize-autoloader

