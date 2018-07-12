FROM nyanpass/php5.5:5.5.38-fpm
#RUN sed -i 's/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/g' /etc/php5/fpm/php.ini
#RUN sed -i 's/allow_url_fopen = Off/allow_url_fopen = On/g' /etc/php5/fpm/php.ini
#RUN sed -i 's/expose_php = On/expose_php = Off/g' /etc/php5/fpm/php.ini
ENV TZ=Europe/Belgrade
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
RUN apt-get update
RUN apt-get install --yes php-pear
RUN apt-get install --yes libicu-dev
RUN apt-get install --yes libpcre3-dev
RUN apt-get install --yes git-core gcc autoconf make
RUN apt-get install --yes php5-dev
RUN apt-get install --yes unzip
RUN apt-get install --yes imagemagick
RUN apt-get install --yes pkg-config libmagickwand-dev
RUN apt-get install --yes vim
RUN pecl install apcu-4.0.4
#RUN pecl install tidy-1.2
RUN pecl install igbinary-1.2.1
RUN pecl install memcache-3.0.8
RUN pecl install xdebug-2.2.3
RUN pecl install redis-2.2.8
RUN pecl install intl-3.0.0
RUN pecl install zip-1.12.3
RUN pecl install imagick-3.3.0
RUN cp /etc/php5/cli/php.ini /usr/local/etc/php/php.ini
RUN echo "date.timezone = Europe/Belgrade" >> /usr/local/etc/php/php.ini

RUN git clone -q --depth=1 https://github.com/phalcon/cphalcon.git -b 1.3.2
RUN export CFLAGS="-g3 -O1 -fno-delete-null-pointer-checks -Wall";
WORKDIR /var/www/html/cphalcon/build
RUN ./install
RUN sed -i '/^;php_admin_value\[error_log\]/cphp_admin_value[error_log] = /var/log/php/fpm-error.log' "/usr/local/etc/php-fpm.d/www.conf"
RUN sed -i '/^;php_admin_flag\[log_errors\]/cphp_admin_flag[log_errors] = on' "/usr/local/etc/php-fpm.d/www.conf"
RUN docker-php-ext-enable phalcon
RUN docker-php-ext-enable igbinary
RUN docker-php-ext-enable apcu
#RUN docker-php-ext-enable tidy
RUN docker-php-ext-enable intl
RUN docker-php-ext-enable memcache
RUN docker-php-ext-enable xdebug
RUN docker-php-ext-enable redis
RUN docker-php-ext-enable imagick
RUN docker-php-ext-enable zip
RUN docker-php-ext-install pdo_mysql
WORKDIR /var/www/robinson