<?php

/**
 * 
 * This file is part of mvc-rest-api for PHP.
 * 
 */
namespace Database\DB;

/**
 * Global Class MySQLi
 */
final class MySQLi {

    /**
     * @var \mysqli|null
     */
    private $mysqli = null;

    /**
     * @var \mysqli_result|null
     */
    private $statement = null;

    /**
     * Construct, create object of MySQLi class
     */
    public function __construct($hostname, $username, $password, $database, $port) {
        // Establish connection
        $this->mysqli = new \mysqli($hostname, $username, $password, $database, $port);

        // Check for connection error
        if ($this->mysqli->connect_error) {
            trigger_error('Error: Could not make a database link (' . $this->mysqli->connect_error . '). Error Code: ' . $this->mysqli->connect_errno, E_USER_ERROR);
            exit();
        }

        // Set default settings
        $this->mysqli->set_charset("utf8");
    }

    /**
     * Execute query statement
     */
    public function query($sql) {
        $this->statement = $this->mysqli->query($sql);
        $result = false;

        try {
            if ($this->statement) {
                $data = array();

                while ($row = $this->statement->fetch_assoc()) {
                    $data[] = $row;
                }

                // Create std class
                $result = new \stdClass();
                $result->row = isset($data[0]) ? $data[0] : array();
                $result->rows = $data;
                $result->num_rows = $this->statement->num_rows;
            }
        } catch (\Exception $e) {
            trigger_error('Error: ' . $e->getMessage() . ' Error Code: ' . $this->mysqli->errno . ' <br />' . $sql, E_USER_ERROR);
            exit();
        }

        if ($result) {
            return $result;
        } else {
            $result = new \stdClass();
            $result->row = array();
            $result->rows = array();
            $result->num_rows = 0;
            return $result;
        }
    }

    /**
     * Escape data to prevent SQL injection
     */
    public function escape($value) {
        return $this->mysqli->real_escape_string($value);
    }

    /**
     * Return last inserted ID
     */
    public function getLastId() {
        return $this->mysqli->insert_id;
    }

    /**
     * Destructor to close database connection
     */
    public function __destruct() {
        if ($this->mysqli) {
            $this->mysqli->close();
        }
    }
}
