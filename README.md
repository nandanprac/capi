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

app/console consult:question:classification:queue
app/console consult:question:doctorassignment:queue
app/console consult:question:doctorassignment:queue

Run following commands in Fabric to push GCM notifications.

app/console fabric:consult:gcm:queue


To Setup Classification, use data in trainingdata folder

run following commands in order

Command to insert stop words
app/console consult:data:trainer:helper /tmp/stop_words.csv --action=stop

Command to insert trained data. and use last file in array and stem option to include stem words
app/console consult:data:trainer /tmp/trainset.csv /tmp/src/stem_list\(u\).csv --stem


Fixtures

- Install Specialities and Sub-Specialities for assignment and classfication
- 

app/console doctrine:fixtures:load --append ( WARNING!!  DONT RUN WITHOUT --append.)


```
