#Build Web server

```shell
# Install apache
sudo yum -y install httpd

sudo systemctl start httpd
sudo systemctl enable httpd
sudo systemctl status httpd

sudo firewall-cmd --add-service=http --zone=public
sudo firewall-cmd --reload
sudo systemctl restart firewalld

curl http://localhost | head

# Set contents
sudo usermod -aG apache chiro
sudo mkdir /var/www/html/web
sudo chown chiro:apache /var/www/html/web
cp -a /path/to/laravel/directory /var/www/html/web
sudo chown -R chiro:apache /var/www/html/web
sudo chmod -R 770 /var/www/html/web/storage /var/www/html/web/bootstrap/cache

# Set apache configuration
sudo vim /etc/httpd/conf/httpd.conf
# Change /var/www/html to /var/www/html/web/public
sudo systemctl restart httpd

# SELinux
sudo vim /etc/selinux/config
SELINUX=disabled
```
