<?php

namespace Fizzik\Database;

/**
 * Database object that handles mongodb connections
 */
class MongoDBDatabase {
    /** @var \MongoDB\Client $client */
    private $client = null; //last connected client
    /** @var \MongoDB\Database $db */
    private $db = null; //last selected database

    public function connect($uri, $urioptions = [], $driveroptions = []) {
        try {
            $this->client = new \MongoDB\Client($uri, $urioptions, $driveroptions);

            return $this->client;
        }
        catch (\Exception $e) {
            die("Could not connect: " . $e->getMessage());
        }
    }

    public function selectDatabase($database, $options = []) {
        $this->db = $this->client->selectDatabase($database, $options);
        return $this->db;
    }

    public function selectCollection($collection, $options = []) {
        return $this->db->selectCollection($collection, $options);
    }

    public function isConnected() {
        return $this->db != null;
    }

    public function connection() {
        return $this->db;
    }

    public function close() {
        $this->db = null;
        $this->client = null;
    }
}