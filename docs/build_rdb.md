# Build RDB

```shell
# Install MySQL8
sudo dnf localinstall https://dev.mysql.com/get/mysql80-community-release-el8-1.noarch.rpm
sudo dnf module disable mysql
sudo dnf install mysql-community-server

sudo systemctl start mysqld
sudo systemctl status mysqld
sudo sudo systemctl enable mysqld

# Secure
sudo grep password /var/log/mysqld.log
sudo mysql_secure_installation

# Create user and database
mysql -u root -p
CREATE USER 'ci'@'%' IDENTIFIED BY 'Cloud_infra03';
GRANT ALL ON *.* TO 'ci'@'%';
FLUSH PRIVILEGES;
CREATE DATABASE ci CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
exit

# Set .env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=ci
DB_USERNAME=ci
DB_PASSWORD=Cloud_infra03

# Set firewall
sudo firewall-cmd --add-service=mysql --zone=public --permanent

```
