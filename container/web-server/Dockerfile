FROM nginx:alpine

RUN rm /etc/nginx/conf.d/default.conf
COPY ./conf.d /etc/nginx/conf.d/
RUN mkdir -p /run/nginx

EXPOSE 80
