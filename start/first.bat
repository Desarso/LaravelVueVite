


IF exist  %CD%\.env (
    echo ".env is here"
) ELSE (
    ECHO "not here"
    rename .env.example .env
)

cls

@REM docker rm -vf $(docker ps -aq) 
@REM docker rmi -f $(docker images -aq)

docker compose build --no-cache
docker compose up
