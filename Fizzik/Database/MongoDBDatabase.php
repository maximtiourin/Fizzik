<?php

namespace Fizzik\Database;

/**
 * Database object that handles mongodb connections
 */
class MongoDBDatabase {
    private $db = null;

    public function connect($uri, $database, $urioptions = [], $driveroptions = []) {
        try {
            $client = new \MongoDB\Client($uri, $urioptions, $driveroptions);

            $this->db = $client . selectDatabase($database);

            return $this->db;
        }
        catch (\Exception $e) {
            die("Could not connect: " . $e->getMessage());
        }
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
    }
}