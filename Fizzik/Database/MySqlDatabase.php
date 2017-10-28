<?php

namespace Fizzik\Database;

use \mysqli;

/**
 * Database object that handles mysqli connections
 */
class MySqlDatabase {
    const DEFAULT_PORT = 3306;

    /** @var \mysqli $db */
    private $db = null;
    private $pstate = []; //Map of : names => prepared statements

    /**
     * Attempts to connect to the mysql database, returning the connection on success, FALSE on failure
     * @param $host
     * @param $user
     * @param $password
     * @param $dbname
     * @param int $port
     * @return bool|mysqli
     */
    public function connect($host, $user, $password, $dbname, $port = self::DEFAULT_PORT) {
        if ($port === NULL) $port = self::DEFAULT_PORT;

        if ($this->db != null) {
            $this->close();
        }

        $this->db = new mysqli($host, $user, $password, $dbname, $port);
        if (mysqli_connect_errno()) {
            return FALSE;
        }

        return $this->db;
    }

    /*
     * Prepares a query statement with the given name,
     * for the current connection, if there is one.
     * use ? for params
     */
    public function prepare($name, $query) {
        if ($this->db != null) {
            $e = $this->db->prepare($query);
            if (!$e) {
                die("prepare error (".$name.")");
            }
            $this->pstate[$name] = $e;
            return $e;
        }

        return false;
    }

    /*
     * Binds the given variables with their given type string to the prepared statement of given name.
     * Type string is a string where each character represents the type of the variable at the same index
     * Valid characters are: i=integer, d=double, s=string, b=blob
     */
    public function bind($name, $types, &...$vars) {
        if ($this->db != null) {
            $e = $this->pstate[$name];
            if ($e != null) {
                return $e->bind_param($types, ...$vars);
            }
        }

        return false;
    }

    /**
     * Executes the prepared query with the given variable name,
     * returns a result set if the query creates one, or false otherwise;
     * @param $name string name of the prepared query
     * @return \mysqli_result|bool
     */
    public function execute($name) {
        if ($this->db != null) {
            $e = $this->pstate[$name];
            if ($e != null) {
                $exec = $e->execute();

                if ($exec) {
                    return $e->get_result();
                }
            }
        }

        return false;
    }

    /**
     * Returns the number of affected rows for the given prepared query
     * if it has been executed and it is UPDATE, INSERT, DELETE
     * @param $name string name of the prepared query
     * @return int|NULL
     */
    public function affectedRows($name) {
        if ($this->db != null) {
            $e = $this->pstate[$name];
            if ($e != null) {
                return $e->affected_rows();
            }
        }

        return NULL;
    }

    public function query($query) {
        $result = $this->db->query($query);
        if (!$result) {
            die('Query failed: ' . $this->db->error);
        }
        return $result;
    }

    public function fetchArray(\mysqli_result $result) {
        return $result->fetch_assoc();
    }

    public function freeResult(\mysqli_result $result) {
        $result->free();
    }

    public function setEncoding($encodingstr) {
        $this->db->set_charset($encodingstr);
    }

    /*
     * Begins a transaction
     */
    public function transaction_begin() {
        $this->db->begin_transaction();
    }

    /*
     * Commits a transaction
     */
    public function transaction_commit() {
        $this->db->commit();
    }

    /*
     * Rolls back a transaction
     */
    public function transaction_rollback() {
        $this->db->rollback();
    }

    /*
     * Counts the amount of rows returned in the result
     */
    public function countResultRows($result) {
        return $result->num_rows;
    }

    public function isConnected() {
        return $this->db != null;
    }

    public function connection() {
        return $this->db;
    }

    public function closeStatements() {
        foreach ($this->pstate as $statement) {
            $statement->close();
        }
    }

    public function close() {
        $this->closeStatements();
        $this->pstate = [];
        $this->db->close();
        $this->db = null;
    }
}
?>