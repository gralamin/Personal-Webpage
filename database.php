<?php
require_once("settings.php");

class Database {
    private static $db_instance;

    private function __construct() {
        $this->con = NULL;
    }

    public static function getInstance() {
        if (!self::$db_instance) {
            self::$db_instance = new Database();
        }
        return self::$db_instance;
    }

    private function connect() {
        if ($this->con == NULL) {
            $this->con = new mysqli("localhost", Settings::user_name,
                                    Settings::password, Settings::database);
        }
    }

    public function close() {
        if ($this->con != NULL) {
            $this->con->close();
            $this->con = NULL;
        }
    }

    public function query($query, $resultmode = MYSQLI_STORE_RESULT) {
        $this->connect();
        return $this->con->query($query, $resultmode);
    }

    public function prepare($query) {
        $this->connect();
        return $this->con->prepare($query);
    }

    public function getError() {
        return "( " . $this->con->errno . " ) " . $this->con->error;
    }
}

?>