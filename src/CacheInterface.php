<?php

namespace Path;

/**
 * Интерфейс кеша.
 */
interface CacheInterface
{
    /**
     * Получить значение из кеша по ключу.
     *
     * @param $key
     * @return mixed
     * @throws \OutOfRangeException Прежде чем получать значение,
     *  необходимо удостовериться в его наличии с помощью метода @see CacheInterface::has()
     */
    public function get($key);

    /**
     * Проверяет наличие ключа в кеше.
     *
     * @param $key
     * @return bool
     */
    public function has($key): bool;

    /**
     * Устанавливает значение ключа в кеше.
     *
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set($key, $value);
}
