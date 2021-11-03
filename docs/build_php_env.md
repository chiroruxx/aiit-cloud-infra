# Build PHP8 Environment

```shell
# Set Remi repo
sudo dnf install epel-release
sudo dnf update epel-release

sudo rpm -ivh http://rpms.remirepo.net/enterprise/remi-release-8.rpm
sudo rpm --import http://rpms.remirepo.net/RPM-GPG-KEY-remi

sudo dnf config-manager --set-enabled remi

# Install PHP8
sudo dnf module reset php
sudo dnf module install php:remi-8.0

sudo dnf install php php-mysql php-mysqlnd php-bcmath php-ctype php-json php-mbstring php-openssl php-pdo php-tokenizer php-xml gcc

php -v

# Install Composer
sudo dnf install composer

composer -V

# Build dev env
composer require --dev barryvdh/laravel-ide-helper
```
