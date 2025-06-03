FROM crystallang/crystal:latest-alpine

WORKDIR /app/

COPY ./shard.yml ./shard.lock /app/
RUN shards install --frozen

COPY ./ /app/
RUN shards build --no-debug --release --static web

CMD ["/app/bin/web"]
