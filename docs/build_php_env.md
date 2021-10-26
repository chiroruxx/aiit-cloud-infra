# Build PHP8 Environment

```shell
# Set Remi repo
sudo yum install epel-release
sudo yum update epel-release

sudo rpm -ivh http://rpms.remirepo.net/enterprise/remi-release-8.rpm
sudo rpm --import http://rpms.remirepo.net/RPM-GPG-KEY-remi

sudo yum config-manager --set-enabled remi

# Install PHP8
sudo yum module reset php
sudo yum module install php:remi-8.0

sudo yum install -y php php-mysql php-mysqlnd php-bcmath php-ctype php-json php-mbstring php-openssl php-pdo php-tokenizer php-xml gcc

php -v

# Install Composer
sudo yum install composer

composer -V
```
