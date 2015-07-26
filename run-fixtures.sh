
mysql -u$1 -p$2 -D$3 -e"set foreign_key_checks=0;truncate table speciality;"
mysql -u$1 -p$2 -D$3 -e"truncate table sub_specialities;"

app/console doctrine:fixtures:load --append
