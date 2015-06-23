consult-api
===========
```
Consult API

Nginx configuration

server {
  server_name consult.practo.local;
  listen 80;
  access_log /var/log/nginx/consult.access.log;
  error_log  /var/log/nginx/consult.error.log;


  root /home/vagrant/www/consult-api/web;

  location / {
    try_files $uri @app;
  }

  location @app {
    fastcgi_pass unix:/var/run/php5-fpm.sock;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root/app_dev.php;
  }
}

Run following commands in Consult.

composer install

Set up following parameters in parameters.yml
- Provide DB details
- Provide Elastic search host
- Provide accounts_host (URL to accounts)
- Provide S3 details
- Provide accounts signing key (same as used in ray)

app/console doctrine:migrations:migrate


app/console consult:question:doctorassignment:queue
app/console consult:assignmentpersist:doctorassignment:queue

Run following commands in Fabric to push GCM notifications.

app/console fabric:consult:gcm:queue

```
