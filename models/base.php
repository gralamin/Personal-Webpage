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

    protected function getValue($query, $parameters) {
        $con = Database::getInstance();
        $stmt = $con->prepare($query);
        $rows = array();
        $parameters->bind($stmt);
        $files = NULL;
        if (!$stmt->execute()) {
            print("<br>Retriving value failed " . $con->getError());
        } else {
            if ($files == NULL) {
                $metaResults = $stmt->result_metadata();
                $fields = $metaResults->fetch_fields();
                $statementParams = "";
                foreach ($fields as $field){
                    if (empty($statementParams)) {
                        $statementParams .= "\$column['".$field->name."']";
                    } else {
                        $statementParams .= ", \$column['".$field->name."']";
                    }
                }
            }
            $statment="\$stmt->bind_result($statementParams);";
            # TODO: Find a way to avoid the eval.
            eval($statment);
            while ($stmt->fetch()) {
                $rows[] = $column;
            }
        }
        $stmt->close();
        $con->close();
        return $rows;
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