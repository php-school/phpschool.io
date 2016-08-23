FROM php:7.0-fpm

RUN apt-get update && apt-get install -y git zlib1g-dev \
	&& rm -rf /var/lib/apt/lists/* \
	&& docker-php-ext-install pdo pdo_mysql zip

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Xdebug
RUN pecl install -o -f xdebug-2.4.0

ADD .docker/bin/docker-configure /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-configure

ADD .docker/etc/php-xdebug.ini /usr/local/etc/php/conf.d/php-xdebug.ini

COPY . /var/www/html
WORKDIR /var/www/html

RUN cd /var/www/html && composer install -q --no-dev -o
RUN php bin/app generate-docs

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/docker-configure"]
CMD ["php-fpm"]