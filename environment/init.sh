#!/bin/bash -e

# set application environment
sed "s/SetEnv ENVIRONMENT.*/SetEnv ENVIRONMENT ${ENVIRONMENT}/" -i /etc/httpd/conf.d/site.conf

# restart mysql daemon & init db data
/usr/bin/mysql_install_db

# restart services
/sbin/service httpd restart
/sbin/service mysqld restart

# change permissions for data & logs directories
chmod -R 0777 /shared/logs/

/bin/bash
