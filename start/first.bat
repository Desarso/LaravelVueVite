


IF exist  %CD%\.env (
    echo ".env is here"
) ELSE (
    ECHO "not here"
    rename .env.example .env
)

cls

docker rm -vf $(docker ps -aq) 
docker rmi -f $(docker images -aq)

docker compose build --no-cache
docker compose up
