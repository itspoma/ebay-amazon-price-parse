#!/bin/bash -e

# set application environment
sed "s/SetEnv ENVIRONMENT.*/SetEnv ENVIRONMENT ${ENVIRONMENT}/" -i /etc/httpd/conf.d/site.conf

# restart services
/sbin/service httpd restart

# change permissions for data & logs directories
chmod -R 0777 /shared/logs/
chmod -R 0777 /shared/site/data

/bin/bash
