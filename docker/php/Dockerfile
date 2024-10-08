FROM php:8.3-fpm-alpine

# fswatch, php83-readline
RUN apk update \
    && apk add --no-cache htop gnupg curl ca-certificates zip unzip git supervisor sqlite libcap libpng-dev python3 \
    bind-tools librsvg ffmpeg nano htop php83-cli php83-dev php83-xdebug \
	php83-bcmath php83-curl php83-dom php83-fileinfo php83-gd php83-iconv php83-imap php83-intl php83-ldap php83-mbstring \
	php83-openssl php83-pdo php83-session php83-soap php83-sockets php83-tokenizer php83-xml php83-xmlwriter php83-zip \
	php83-msgpack php83-pecl-memcached php83-pecl-redis php83-pecl-swoole\
    php83-pecl-mongodb php83-pecl-imagick php83-pecl-pcov php83-pecl-igbinary \
    composer

RUN apk add linux-headers && docker-php-ext-install sockets
RUN docker-php-ext-install pdo_mysql

RUN apk update && apk add --no-cache autoconf g++ make openssl-dev gcc libc-dev pkgconf
RUN pecl install redis && docker-php-ext-enable redis
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Gereksiz dosyaları temizle
RUN apk del autoconf g++ make gcc libc-dev pkgconf

#RUN apk add php83-pgsql php83-sqlite3 php83-pdo_sqlite

# Uygulamanızın dosyalarını çalışma dizinine kopyalayın (örneğin, /var/www/html)
#RUN chown -R www-data:www-data /var/www/html
#RUN chmod -R 755 /var/www/html

COPY php.ini /etc/php/8.3/cli/conf.d/99-deployer.ini

CMD ["php-fpm"]
