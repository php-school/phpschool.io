# Sandbox docker host

Student code runs in containers on a Fly.io machine instead of the web server. The web server reaches the
machine's docker API over Fly's private network, through a WireGuard tunnel. The API has no password, so it is never
public.

`start.sh` starts dockerd and creates the `php-school-ports` network for exercises that publish a port. Student code on
it can not reach the internet or this machine, eg. the docker API, it can only answer connections to its port. Other
student code runs with no network at all.

## Setup

```sh
fly apps create phpschool-sandbox
fly ips allocate-v6 --private   # the address through fly's proxy, eg. fdaa:1:2:0:1::2
fly deploy --ha=false --no-public-ips
fly machine list                # the machine's own address, eg. fdaa:1:2:3:4:5:6:2
```

The machine suspends when idle. Only traffic through the proxy address wakes it, so docker talks to that. The ports
student code publishes are not proxied, so checks connect to the machine's own address, which is awake by then.

On the web server, a permanent tunnel into the private network:

```sh
fly wireguard create personal fra web fly.conf   # on a machine logged in to fly, then copy fly.conf over
sudo mv fly.conf /etc/wireguard/fly.conf && sudo systemctl enable --now wg-quick@fly
```

Then in the web server's `.env`:

```
DOCKER_HOST=tcp://[fdaa:1:2:0:1::2]:2375
SANDBOX_PORTS_HOST=[fdaa:1:2:3:4:5:6:2]
SANDBOX_PORTS_NETWORK=php-school-ports
```

and deploy, which builds the workshops' runtime images on the machine.

## Monitoring

Set `SLACK_ALERTS_WEBHOOK` in the web server's `.env` to get errors in Slack, eg. a verify that can't reach the
machine. The same error is posted at most once an hour.

To hear about it before a student does, check the machine hourly from the web server's crontab:

```
0 * * * * cd /var/www/phpschool.io/current && php bin/app check-sandbox
```

Each check wakes the machine for a few minutes, so don't run it more often. Fly's Grafana, at
[fly-metrics.net](https://fly-metrics.net), shows its CPU, memory and how full `/data` is.
