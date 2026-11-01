# The docker host student code runs on, see README.md
FROM docker:28-dind

# no TLS, the API is only reachable over the private network. TINI_SUBREAPER quietens dockerd's init not being pid 1
ENV DOCKER_TLS_CERTDIR="" TINI_SUBREAPER=1

COPY start.sh /usr/local/bin/start.sh

ENTRYPOINT ["start.sh"]
