FROM node:18-alpine

ARG UID
ENV UID "${UID}"

WORKDIR /app

COPY ./frontend/package.json /app/package.json

RUN yarn

ENV HOST 0.0.0.0
EXPOSE 3000

CMD [ "yarn", "dev" ]