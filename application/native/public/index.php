<?php

require_once '../MemcachedAdapter.php';

$config = [
    1 => ['memcache-1', 11211],
    2 => ['memcache-2', 11211],
];

$memcached = new MemcachedAdapter($config);

$content = [];

$action = $_GET['action'] ?? '';
switch ($action):
    case 'flush':
        $memcached->flush();
        $content[] = 'Flushed';
    break;

    case 'keys':
        $keys = $memcached->getAllKeys();
        if ($keys === false):
            $content[] = 'Can\'t get info.';
            break;
        endif;
        $content = $keys;
    break;

    case 'servers':
        $servers = $memcached->getServerList();
        foreach ($servers as $server):
            $content[] = $server['host'];
        endforeach;
    break;

    case 'stats':
        $stats = $memcached->getStats();
        if (!$stats):
            $content[] = 'Can\'t get info.';
            break;
        endif;
        foreach ($stats as $serverName => $serverStats):
            $block = '';
            $block .= $serverName . PHP_EOL;
            foreach ($serverStats as $property => $stat):
                $block .= $property . ' = ' . $stat . PHP_EOL;
            endforeach;
            $content[] = $block;
        endforeach;
    break;

    case 'test':
        for ($i = 1; $i < 100; $i++):
            $block = '';
            $block .= 'itteration ' . $i . PHP_EOL;
            $key = $i . '-key-' . $i;
            $val = 'foobar-' . $i;
            $firstGet = $memcached->get($key);
            $block .= 'get ' . $key . ' = ' . $firstGet . PHP_EOL;
            $memcached->set($key, $val);
            $block .= 'set ' . $key . ' = ' . $val . PHP_EOL;
            $secondGet = $memcached->get($key);
            $block .= 'get ' . $key . ' = ' . $secondGet;
            $content[] = $block;
        endfor;
    break;
endswitch;

include '../template.php';