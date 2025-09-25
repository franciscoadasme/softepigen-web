FROM crystallang/crystal:latest-alpine

WORKDIR /app/

COPY ./shard.yml ./shard.lock /app/
RUN shards install --frozen

COPY ./ /app/
RUN crystal build --no-debug --release --static -o /app/bin/softepigen /app/lib/softepigen/src/main.cr
RUN shards build --no-debug --release --static web
RUN mkdir -p /app/public/output

CMD ["/app/bin/web"]
