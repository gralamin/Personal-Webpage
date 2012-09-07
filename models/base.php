<?php
require_once("models".DIRECTORY_SEPARATOR."util.php");
require_once("database.php");

abstract class Model {
    function __construct($table_name) {
        $this->table_name = $table_name;
    }

    public function getSchema() {
        return "Abstract models do not have schema";
    }

    public function getName() {
        return $this->table_name;
    }

    public function create() {
        $con = Database::getInstance();
        if (!$con->query("CREATE TABLE " . $this->table_name . "(" . $this->getSchema() . ")")) {
            die("Failed to create table " . $con->getError());
        }
        $con->close();
        return True;
    }

    public function exists() {
        $con = Database::getInstance();
        if($con->query("DESCRIBE " . $this->table_name)) {
            $con->close();
            return True;
        }
        print $con->getError();
        $con->close();
        return False;
    }

    protected function insertValues($bindParam, $columns, $blobs = NULL) {
        $con = Database::getInstance();
        $status = True;
        $query = "INSERT INTO " . $this->table_name . " (" . $columns .
            ") VALUES (" . $bindParam->getInsertValuesMarks() . ")";
        $stmt = $con->prepare($query);
        $stmt = $bindParam->bind($stmt);

        if ($blobs != NULL) {
            foreach ($blobs as $blob) {
                $stmt = $blob->sendData($stmt);
            }
        }

        if (!$stmt->execute()) {
            print("<br>Inserting values failed " . $con->getError());
            $status = False;
        };
        $stmt->close();
        $con->close();
        return $status;
    }

    public function createRow($array) {
        print "Create a BindParam based on array, and pass to insertValues";
    }
}
?>