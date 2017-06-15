FROM php:7.0-apache

RUN apt-get update && \
    apt-get install -y curl nano git npm && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    npm install -g bower
