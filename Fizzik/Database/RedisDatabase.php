<?php

namespace Fizzik\Database;

/**
 * Database object that handles redis connections, also has functions
 * styled to use Redis as a cache
 */
class RedisDatabase {
    /** @var \Predis\Client $client */
    private $client = null; //last connected client

    /**
     * Attempts to connect to the redis server, returning a Predis client on success, FALSE on failure.
     * @param $uri
     * @param null $database
     * @param array $clientOptions
     * @return bool|\Predis\Client
     */
    public function connect($uri, $database = NULL, $clientOptions = []) {
        try {
            $this->client = new \Predis\Client($uri, $clientOptions);

            if ($database !== NULL && is_int($database)) {
                $this->selectDatabase($database);
            }

            return $this->client;
        }
        catch (\Exception $e) {
            return FALSE;
        }
    }

    public function selectDatabase($database_index) {
        return $this->client->select($database_index);
    }

    /*
     * Caches the value at the specified key, with the optional time-to-live in seconds
     * Returns a redis response str
     */
    public function cacheString($key, $value, $seconds = NULL) {
        if (is_int($seconds)) {
            return $this->client->setex($key, $seconds, $value);
        }
        else {
            return $this->client->set($key, $value);
        }
    }

    /*
     * Returns the value of the string of the given key, or null if the key doesn't exist
     */
    public function getCachedString($key) {
        return $this->client->get($key);
    }

    /*
     * Caches the value at the specified key and field, with the optional time-to-live in seconds
     * Returns a redis response int
     */
    public function cacheHash($key, $field, $value, $seconds = NULL) {
        if (is_int($seconds)) {
            $ret = $this->client->hset($key, $field, $value);
            $this->client->expire($key, $seconds);
            return $ret;
        }
        else {
            $ret = $this->client->hset($key, $field, $value);
            $this->client->persist($key);
            return $ret;
        }
    }

    /*
     * Returns the value of the field of the given key, or null if the key or field doesn't exist
     */
    public function getCachedHash($key, $field) {
        return $this->client->hget($key, $field);
    }

    /*
     * Sets the time-to-live in seconds for a given key. If seconds isn't specified, isn't an integer, or is <= 0, then the key is instead deleted.
     * If seconds is set, returns integer 1 if the time-to-live was set, or 0 if the key doesnt exist
     * If seconds isn't set or is <= 0, returns integer 1 if key was removed, 0 if the key doesnt exist
     */
    public function expire($key, $seconds = 0) {
        if (is_int($seconds) && $seconds > 0) {
            return $this->client->expire($key, $seconds);
        }
        else {
            return $this->client->del($key);
        }
    }

    public function isConnected() {
        return $this->client != null;
    }

    public function connection() {
        return $this->client;
    }

    public function close() {
        if ($this->client !== NULL) {
            $this->client->disconnect();
            $this->client = null;
        }
    }
}