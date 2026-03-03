FROM php:8.4-fpm

# Устанавливаем системные зависимости
RUN apt update && apt install -y zlib1g-dev g++ git libicu-dev zip libzip-dev librdkafka-dev libmemcached-dev

# Устанавливаем PHP-расширения через универсальный установщик
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions decimal opcache pdo_pgsql pdo_mysql pcntl mysqli bcmath intl zip redis
RUN pecl install memcached && docker-php-ext-enable memcached

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Пользовательские аргументы
ARG UID=1000
ARG GID=1000
ARG USER=appuser

# Создаём пользователя
RUN groupadd -g ${GID} ${USER} \
    && useradd -m -u ${UID} -g ${GID} -s /bin/bash ${USER}

WORKDIR /app
RUN chown -R ${USER}:${USER} /app
RUN git config --global --add safe.directory /app

# xdebug
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
#RUN install-php-extensions xdebug
#сервер, который создали в настройках phpStorm
#ENV PHP_IDE_CONFIG 'serverName=debug'
#RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
#RUN echo "xdebug.start_with_request = yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
#RUN echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
#RUN echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
#RUN echo "xdebug.log=/var/log/xdebug.log" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
#RUN echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
#RUN echo "xdebug.start_with_request = yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

USER ${USER}

CMD ["php-fpm"]
