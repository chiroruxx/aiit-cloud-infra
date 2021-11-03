# Build Queue

```shell
# Install redis
sudo dnf install redis php-pecl-redis php-process
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

# Allow remote connect
sudo firewall-cmd --add-port=6379/tcp --permanent
sudo firewall-cmd --reload
sudo systemctl restart firewalld

sudo vim /etc/redis.conf

# bind 127.0.0.1
bind 0.0.0.0

sudo systemctl restart redis
sudo systemctl status redis

redis-cli -h 10.10.10.1
```
