FROM nginx:1.21-alpine
COPY ./public/ /var/www/html/public/
COPY ./docker/nginx/fastcgi-php.conf /etc/nginx/fastcgi-php.conf
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf
