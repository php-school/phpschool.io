FROM php:8.1-fpm AS prod

RUN apt-get -qq update && apt-get install -qqy git zlib1g-dev libzip-dev \
	&& rm -rf /var/lib/apt/lists/* \
	&& docker-php-ext-install pdo pdo_mysql zip sockets

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /var/www/html
WORKDIR /var/www/html

COPY --from=php:8.1-cli /usr/local/bin/php-cgi /usr/local/bin/php-cgi

RUN cd /var/www/html

CMD ["php-fpm"]

FROM prod AS debug

RUN pecl install xdebug && docker-php-ext-enable xdebug
ADD .docker/etc/php-xdebug.ini /usr/local/etc/php/conf.d/php-xdebug.ini