FROM php:7.3

RUN apt-get update -y && apt-get install -y \
  build-essential openssl gnupg apt-transport-https git zip unzip libpng-dev libpq-dev

RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" >/etc/apt/sources.list.d/yarn.list

RUN curl -sL https://deb.nodesource.com/setup_10.x | bash -

RUN apt-get update -y && apt-get install -y nodejs yarn

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install pdo pdo_pgsql mbstring

RUN apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libgmp-dev \
    && ln -s /usr/include/x86_64-linux-gnu/gmp.h /usr/include/gmp.h \
    && docker-php-ext-configure gmp \
    && docker-php-ext-install iconv gd gmp opcache

COPY docker/upload_max_filesize.ini $PHP_INI_DIR/conf.d/

RUN mkdir -p /app
WORKDIR /app

EXPOSE 8000
