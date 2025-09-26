FROM crystallang/crystal:latest-alpine
RUN apk add --no-cache dcron zip

ENV KEMAL_ENV=production

WORKDIR /app/

COPY ./shard.yml ./shard.lock /app/
RUN shards install --frozen

COPY ./ /app/
RUN crystal build --no-debug --release -o /app/bin/softepigen /app/lib/softepigen/src/main.cr
RUN shards build --no-debug --release web
RUN mkdir -p /app/public/output

COPY ./cron/delete_old_files /etc/cron.d/delete_old_files
RUN chmod 0744 /app/scripts/delete_old_files.sh \
&& chmod 0644 /etc/cron.d/delete_old_files \
&& touch /var/log/cron.log

RUN chmod +x /app/scripts/run.sh
CMD ["/app/scripts/run.sh"]
