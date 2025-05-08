## Install localhost
docker-compose up -d

docker exec -it credfacil-api /bin/bash
## navegar ao diretorio do projeto

php artisan key:generate
php artisan migrate --force


## instalar crontab
sudo apt update
sudo apt install cron
sudo systemctl enable cron
sudo systemctl start cron