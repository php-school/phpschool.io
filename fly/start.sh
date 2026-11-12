#!/bin/sh
# Starts dockerd, then fences off the network for exercises that publish a port. Student code on it can not reach
# the internet or this machine, eg. the docker API, only answer connections to its published port.
set -e

# /run is not a tmpfs here, so pid files from a machine that was not stopped cleanly survive and stop dockerd starting
rm -f /run/docker.pid /run/docker/containerd/containerd.pid

# the web server tells a cold start from a resume by this label, see src/Online/Sandbox.php
dockerd-entrypoint.sh dockerd --label "started=$(date +%s)" --host=tcp://[::]:2375 --host=unix:///var/run/docker.sock --tls=false --data-root=/data/docker &
DOCKERD=$!

export DOCKER_HOST=unix:///var/run/docker.sock
until docker info >/dev/null 2>&1; do kill -0 "$DOCKERD" 2>/dev/null || exit 1; sleep 1; done

docker network inspect php-school-ports >/dev/null 2>&1 || docker network create \
    -o com.docker.network.bridge.name=br-ports \
    -o com.docker.network.bridge.enable_ip_masquerade=false \
    php-school-ports >/dev/null

# -C first, so a restart does not add them twice. Replies to connections from outside are established, so they pass
allow() { iptables -C "$@" 2>/dev/null || iptables -I "$@"; }
allow DOCKER-USER -i br-ports ! -o br-ports -j DROP
allow DOCKER-USER -i br-ports -m conntrack --ctstate ESTABLISHED,RELATED -j RETURN
allow INPUT -i br-ports -j DROP
allow INPUT -i br-ports -m conntrack --ctstate ESTABLISHED,RELATED -j ACCEPT

wait
