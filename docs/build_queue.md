# Build Queue

```shell
#Install redis
sudo dnf install redis php-pecl-redis 
sudo systemctl start redis
sudo systemctl status redis
sudo sudo systemctl enable redis

# Install Laravel Horizon
composer require laravel/horizon
php artisan horizon:install
```