FROM nginx:1.19.0-alpine
ARG quasar_version
ARG quasar_cli_version

RUN apk update && apk upgrade

RUN apk add --update --no-cache \
        nodejs \
        npm \
        yarn \
        tzdata
        
RUN apk add --update nodejs npm

RUN npm install -g vue-cli

RUN npm install -g @quasar/cli