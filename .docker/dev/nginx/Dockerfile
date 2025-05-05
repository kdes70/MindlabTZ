FROM nginx:alpine

RUN apk add --no-cache curl

HEALTHCHECK --interval=5s --timeout=3s --start-period=1s CMD curl --fail http://127.0.0.1/health || exit 1