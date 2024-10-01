
start_containers:
	docker-compose up -d

stop_containers:
	docker stop bas-group-nginx-1 bas-group-fpm-1bas-group-db-1

restart_containers:
	docker stop bas-group-nginx-1 bas-group-fpm-1bas-group-db-1
	docker-compose up -d   