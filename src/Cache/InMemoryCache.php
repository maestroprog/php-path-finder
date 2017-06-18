<?php

namespace Path\Cache;

use Path\CacheInterface;

final class InMemoryCache implements CacheInterface
{
    private $cache;

    public function __construct()
    {
        $this->cache = new \ArrayObject();
    }

    public function get($key)
    {
        return $this->cache->offsetGet($key);
    }

    public function has($key): bool
    {
        return $this->cache->offsetExists($key);
    }

    public function set($key, $value)
    {
        $this->cache->offsetSet($key, $value);
    }
}
