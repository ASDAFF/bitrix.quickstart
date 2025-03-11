Переезжаем на percona-server
Итак, уважаемые коллеги, в распоряжении у нас сервер, на который успешно установлен Centos 6.5, bitrixEnv 4.3 и выполнен yum update. 

Также у нас есть дикое желание избавиться от mysql-server в пользу percona-server. 

Приступаем. 

Выбираем версию репозитория Percona в зависимости от архитектуры железки, с которой работаем. 

yum install http://www.percona.com/downloads/percona-release/percona-release-0.0-1.i386.rpm
yum install http://www.percona.com/downloads/percona-release/percona-release-0.0-1.x86_64.rpm 

Останавливаем mysql-server 

service mysqld stop 

Удаляем пакеты mysql, которые несовместимы с Percona (--nodeps обеспечивает отсутствие проверки зависимости между пакетами, что сохраняет нам bitrixEnv в добром здравии).

rpm --nodeps -e mysql-server mysql 

Теперь можем установить все, что нам нужно от Рercona 

yum install Percona-Server-server-55 Percona-Server-client-55 Percona-Server-shared-55 Percona-Server-shared-compat percona-toolkit xtrabackup perl-DBD-MySQL php-mysql MySQL-python 

Меняем в /etc/my.cnf сокет c /var/lib/mysqld/mysqld.sock на /var/lib/mysql/mysql.sock. Необходимо заменить все записи. 
Также меняем pid-file = /var/run/mysql/mysql.pid на pid_file = /var/run/mysql/mysql.pid 

Переходим в базовую папку mysql 
cd /var/lib/mysql/ 
Удаляем мусорные директории (ex. logs.ori) 

Стартуем percona-server 
service mysql start 

На всякий случай проверяем/обеспечиваем совместимость 
mysql_upgrade 

Запускаем percona в консоли и радуемся 
mysql 

Радуемся полученному результату. 

Для функционирования самого bitrixEnv меняем в /etc/php.d/bitrixenv.ini сокет c /var/lib/mysqld/mysqld.sock на /var/lib/mysql/mysql.sock 



+++++
Нужно выполнить еще
ln -s /var/lib/mysql/mysql.sock /var/lib/mysqld/mysqld.sock 






