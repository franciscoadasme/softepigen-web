FROM crystallang/crystal:latest-alpine
RUN apk add --no-cache dcron zip git

ENV KEMAL_ENV=production

WORKDIR /softepigen/
RUN git clone https://github.com/franciscoadasme/softepigen .
RUN shards build --no-debug --release --without-development softepigen

WORKDIR /app/

COPY ./shard.yml ./shard.lock /app/
RUN shards install --frozen

COPY ./ /app/
RUN shards build --no-debug --release web
RUN mkdir -p /app/public/output
RUN cp /softepigen/bin/softepigen /app/bin/softepigen

COPY ./cron/delete_old_files /etc/cron.d/delete_old_files
RUN chmod 0744 /app/scripts/delete_old_files.sh \
&& chmod 0644 /etc/cron.d/delete_old_files \
&& touch /var/log/cron.log

RUN chmod +x /app/scripts/run.sh
CMD ["/app/scripts/run.sh"]
