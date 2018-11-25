# Memcache cluster (sharding) test

## How does it works

1. Run docker via `docker-compose up --build -d`.
2. Check test few times, then check stats.
3. Turn off one of memcache servers.
4. Run test again - first 3 balanced keys will be empty after set-get, but then it'll reballance to new instance. Because of OPT_SERVER_FAILURE_LIMIT + OPT_REMOVE_FAILED_SERVERS + OPT_AUTO_EJECT_HOSTS.
5. Check stats and keys tabs. Somehow don't works.
6. Check test again - all keys are avaialble (taken from 1 node)
7. Wait 1 minute (better little bit more)
8. Run test again - same as in 4. Because of OPT_DEAD_TIMEOUT.
