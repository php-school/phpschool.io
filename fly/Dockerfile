# The docker host student code runs on, see README.md
FROM docker:28-dind

# no TLS, the API is only reachable over the private network. start.sh is pid 1, so dockerd's init (tini) would not
# clean up exited processes under dockerd without TINI_SUBREAPER
ENV DOCKER_TLS_CERTDIR="" TINI_SUBREAPER=1

COPY start.sh /usr/local/bin/start.sh

ENTRYPOINT ["start.sh"]
