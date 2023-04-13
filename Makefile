fileName=

install:

ifeq ("$(wildcard .env)","")
	cp .env.example .env
endif
	@docker compose build --no-cache && docker compose up -d
	@docker exec -it test3-devContainer-1 npm install
	@docker exec -it test3-devContainer-1 composer install
	@docker exec -it test3-devContainer-1 php artisan key:generate



clean:
	#need to remove the specific container and image
	@docker compose down
	@docker rmi $(docker images -f "dangling=true" -q)
	@docker rmi -f $(docker images containerimage -aq)
	@docker rmi -f $(docker images mysql/mysql-server -aq)
	@docker rm -vf $(docker ps -aq --filter "name=laravelvuevite")
