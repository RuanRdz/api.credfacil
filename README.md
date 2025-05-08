## Install localhost
docker-compose up -d

docker exec -it credfacil-api /bin/bash
## navegar ao diretorio do projeto

php artisan key:generate
php artisan migrate --force
