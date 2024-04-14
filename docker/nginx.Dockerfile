FROM nginx:stable

# Remove the default Nginx configuration file
RUN rm /etc/nginx/conf.d/default.conf

# Copy the configuration file from the current directory
COPY ./docker/nginx.conf /etc/nginx/conf.d/default.conf
