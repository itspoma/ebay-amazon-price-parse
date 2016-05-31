FROM centos:6
MAINTAINER itspoma <itspoma@gmail.com>

ARG ENVIRONMENT
ARG NODEJS_VERSION
ARG PHP_VERSION
ARG MYSQL_USER
ARG MYSQL_PASSWORD

ENV ENVIRONMENT ${ENVIRONMENT}
ENV MYSQL_USER ${MYSQL_USER}
ENV MYSQL_PASSWORD ${MYSQL_PASSWORD}

RUN yum clean all \
 && yum install -y git curl gcc-c++ tar which wget \
 && yum install -y mc

# node & npm & nvm
RUN curl --silent --location https://rpm.nodesource.com/setup | bash - \
 && yum install -y nodejs \
 && npm -g install npm \
 && curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.31.0/install.sh | bash \
 && . ~/.bashrc \
 && nvm install v${NODEJS_VERSION} \
 && nvm use v${NODEJS_VERSION} \
 && node --version \
 && npm --version

# bower
RUN npm install -g bower \
 && bower --version

# php
RUN rpm -Uvh https://mirror.webtatic.com/yum/el6/latest.rpm \
 && yum clean all \
 && yum install -y ${PHP_VERSION} ${PHP_VERSION}-common ${PHP_VERSION}-intl \
 && yum install -y ${PHP_VERSION}-pdo ${PHP_VERSION}-mysqlnd \
 && php --version

# configure the php.ini
RUN echo "" >> /etc/php.ini \
 && sed 's/;date.timezone.*/date.timezone = Europe\/Kiev/' -i /etc/php.ini \
 && sed 's/^display_errors.*/display_errors = On/' -i /etc/php.ini \
 && sed 's/;error_log.*/error_log = \/shared\/logs\/php_errors.log/' -i /etc/php.ini \
 && sed 's/^display_startup_errors.*/display_startup_errors = On/' -i /etc/php.ini \
 && sed 's/^variables_order.*/variables_order = "EGPCS"/' -i /etc/php.ini

ADD ./environment/php/php.env-default.ini /etc/php.d/
ADD ./environment/php/php.env-${ENVIRONMENT}.ini /etc/php.d/

# apache2
RUN yum install -y httpd \
 && rm -rfv /etc/httpd/conf.d/*.conf \
 && httpd -version

# configure the httpd
RUN sed 's/#ServerName.*/ServerName site/' -i /etc/httpd/conf/httpd.conf \
 && sed 's/#EnableSendfile.*/EnableSendfile off/' -i /etc/httpd/conf/httpd.conf

# put vhost config for httpd
ADD ./environment/httpd/*.conf /etc/httpd/conf.d/

# composer
RUN curl -sS https://getcomposer.org/installer | php \
 && mv composer.phar /usr/local/bin/composer \
 && composer clearcache \
 && composer --version

 # phpmyadmin
 RUN rpm -iUvh http://dl.fedoraproject.org/pub/epel/6/x86_64/epel-release-6-8.noarch.rpm \
  && yum -y install phpmyadmin \
  && sed 's/Require ip 127.0.0.1/#/' -i /etc/httpd/conf.d/phpMyAdmin.conf \
  && sed 's/Require ip ::1/#/' -i /etc/httpd/conf.d/phpMyAdmin.conf \
  && sed 's/Allow from ::1/Allow from all/' -i /etc/httpd/conf.d/phpMyAdmin.conf \
  && sed 's/^.cfg..LoginCookieValidity.*$/\$cfg["LoginCookieValidity"] = 3600*10;/' -i /usr/share/phpMyAdmin/libraries/config.default.php

# mysql install
RUN yum install -y mysql mysql-server

# mysql configure
RUN sed 's/^user.*/user=root/' -i /etc/my.cnf \
 && sed 's/^datadir.*/datadir=\/shared\/environment\/mysql\/data/' -i /etc/my.cnf \
 && sed 's/^log-error.*/log-error=\/shared\/logs\/mysqld.log/' -i /etc/my.cnf

ADD ./environment/mysql /shared/environment/mysql
ADD ./logs /shared/logs

RUN true \
 && rm -rf /shared/environment/mysql/data \
 && mysql_install_db \
 && service mysqld restart \
 && mysqladmin -u ${MYSQL_USER} password "${MYSQL_PASSWORD}" \
 && mysql -u ${MYSQL_USER} -p${MYSQL_PASSWORD} -e "SHOW DATABASES;"

WORKDIR /shared

CMD ["/bin/bash", "/shared/environment/init.sh"]
