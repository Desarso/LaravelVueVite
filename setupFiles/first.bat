


IF exist C:\Users\malek.DESKTOP-A921SJN\Desktop\projects\Laravel9-fix\.env (
    echo ".env is here"
) ELSE (
    rename env.example .env
)

cls

docker compose build --no-cache
