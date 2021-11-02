# Build Queue

```shell
# Install redis
sudo dnf install redis php-pecl-redis 
sudo systemctl start redis
sudo systemctl status redis
sudo sudo systemctl enable redis

# Install Laravel Horizon
composer require laravel/horizon
php artisan horizon:install

# Set supervisor
sudo dnf install supervisor.noarch
sudo vim /etc/supervisord.d/horizon.ini

[program:horizon]
process_name=%(program_name)s
command=php /var/www/html/web/artisan horizon
autostart=true
autorestart=true
user=chiro
redirect_stderr=true
stdout_logfile=/var/www/html/web/storage/logs/horizon.log
stopwaitsecs=3600

touch /var/www/html/web/storage/logs/horizon.log
sudo chown chiro:apache /var/www/html/web/storage/logs/horizon.log

sudo systemctl start supervisord
sudo systemctl status supervisord
sudo systemctl enable supervisord
```
