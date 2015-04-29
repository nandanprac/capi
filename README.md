consult-api
===========

Consult API

Nginx configuration

server {
  server_name consult.practo.local;
  listen 80;
  access_log /var/log/nginx/consult.access.log;
  error_log  /var/log/nginx/consult.error.log;


  root /home/vagrant/www/consult-api/web;

  location @assetic {
    fastcgi_pass unix:/var/run/php5-fpm.sock;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root/app_dev.php;
    fastcgi_hide_header Content-Type;
  }

  location /css/ {
    try_files $uri @assetic;
    add_header Content-Type 'text/css';

  }

  location /js/ {
    try_files $uri @assetic;
    add_header Content-Type 'application/javascript';

  }

  location / {
    try_files $uri @app;
  }

  location @app {
    fastcgi_pass unix:/var/run/php5-fpm.sock;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root/app_dev.php;
  }
}
