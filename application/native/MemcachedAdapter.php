<?php

/**
 * Class MemcachedAdapter
 */
class MemcachedAdapter
{
    const FATAL_CODES =
        [
            Memcached::RES_ERRNO,
            Memcached::RES_TIMEOUT,
            Memcached::RES_HOST_LOOKUP_FAILURE,
            Memcached::RES_CONNECTION_SOCKET_CREATE_FAILURE,
            Memcached::RES_SERVER_MARKED_DEAD,
            3,// Memcached::RES_CONNECTION_FAILURE,
            Memcached::RES_SERVER_TEMPORARILY_DISABLED,
        ];

    const PERSIST_KEY = 'persist';

    private $memcached;

    private $disconnectedServer = [];

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        // persistent means that connections are stored and reused. so in case of failover, it will be applied to
        // all clients with same persistent_id='persistent'
        $this->memcached = new Memcached('persistent');

        $servers = $this->memcached->getServerList();
        if (!$servers)
        {
            $this->addServers($config);
        }
    }

    /**
     * @return array|false
     */
    public function getAllKeys()
    {
        $keys = $this->memcached->getAllKeys();
        $this->handleFailure();

        return $keys;
    }

    /**
     * @return bool
     */
    public function flush(): bool
    {
        $isFlushed = $this->memcached->flush();
        $this->handleFailure();

        return $isFlushed;
    }

    /**
     * @return array|false
     */
    public function getServerList()
    {
        $serversList = $this->memcached->getServerList();
        $this->handleFailure();

        return $serversList;
    }

    /**
     * @param string $key
     *
     * @return array|false
     */
    public function getServerByKey(string $key)
    {
        $server = $this->memcached->getServerByKey($key);
        $this->handleFailure();

        return $server;
    }

    /**
     * @return array|false
     */
    public function getStats()
    {
        $serversList = $this->memcached->getStats();
        $this->handleFailure();

        return $serversList;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key)
    {
        $data = $this->memcached->get($key);
        $this->handleFailure();

        return $data;
    }

    /**
     * @param string $key
     * @param mixed $data
     * @param int $ttl
     *
     * @return mixed
     */
    public function set(string $key, $data, int $ttl = 0)
    {
        $this->memcached->set($key, $data, $ttl);
        $this->handleFailure();

        return $data;
    }

    /**
     * Doesn't make may sense, just gives possibility to debug and check what's going on
     *
     * @param string
     */
    private function handleFailure()
    {
        $resultCode = $this->memcached->getResultCode();
        if (!in_array($resultCode, self::FATAL_CODES))
        {
            return;
        }

        $disconnectedServer = $this->memcached->getLastDisconnectedServer();
        if (!$disconnectedServer || in_array($disconnectedServer, $this->disconnectedServer))
        {
            return;
        }
        $this->disconnectedServer[] = $disconnectedServer;

        $serverList = $this->memcached->getServerList();
        $newServerList = [];
        foreach ($serverList as $serverKey => $server)
        {
            if ($server['host'] == $disconnectedServer['host'] && $server['port'] == $disconnectedServer['port'])
            {
                continue;
            }

            $newServerList[$serverKey] = [$server['host'], $server['port']];
        }

        // Segmentation fault in case of uncommenting next lines
        // $this->memcached->resetServerList();
        // $this->addServers($newServerList);
    }

    /**
     * @param array $config
     */
    private function addServers(array $config)
    {
        // 0.5 sec should be enough to connect
        $this->memcached->setOption(Memcached::OPT_CONNECT_TIMEOUT, 500);

        // consistent distribution will allow to rebalance only needed keys
        $this->memcached->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
        // recommended to turn on this option in case of consistent distribution
        $this->memcached->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);

        // allows to disabled server which has failures during connection
        $this->memcached->setOption(Memcached::OPT_REMOVE_FAILED_SERVERS, true);
        // remove failured hosts. same as previous line
        $this->memcached->setOption(Memcached::OPT_AUTO_EJECT_HOSTS, true);
        // after 1 failure requests to memcached instance, it will be not used
        // tried with 3, but it didn't worked
        $this->memcached->setOption(Memcached::OPT_SERVER_FAILURE_LIMIT, 1);

        // after 60 seconds reverify dead instance
        $this->memcached->setOption(Memcached::OPT_DEAD_TIMEOUT, 60);

        // add servers from config. in the bottom because it's needed to setup options
        $this->memcached->addServers($config);
    }
}