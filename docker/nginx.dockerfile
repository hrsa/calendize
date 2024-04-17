FROM nginx:stable as base

ARG UID
ARG GID
ARG USER

ENV UID=${UID}
ENV GID=${GID}
ENV USER=${USER}

RUN addgroup --gid ${GID} --system ${USER}
RUN adduser --system --home /home/${USER} --shell /bin/sh --uid ${UID} --ingroup ${USER} ${USER}

RUN sed -i "s/user nginx/user '${USER}'/g" /etc/nginx/nginx.conf

RUN rm /etc/nginx/conf.d/default.conf


FROM base as dev
COPY ./docker/nginx-dev.conf /etc/nginx/conf.d/default.conf
