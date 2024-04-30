FROM node:latest as base

ARG UID
ARG GID
ARG USER

ENV UID=${UID}
ENV GID=${GID}
ENV USER=${USER}

RUN addgroup --gid ${GID} --system ${USER}
RUN adduser --system --home /home/${USER} --shell /bin/sh --uid ${UID} --ingroup ${USER} ${USER}

RUN npm install -g npm@10.6.0 && npm install -g npm-check-updates

WORKDIR /var/www/

USER ${USER}

FROM base as npm
ENTRYPOINT [ "npm" ]

FROM base as ncu
ENTRYPOINT [ "ncu", "--interactive", "--format", "group" ]

