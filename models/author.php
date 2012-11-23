<?php
require_once("models".DIRECTORY_SEPARATOR."base.php");
require_once("models".DIRECTORY_SEPARATOR."util.php");

class Author extends Model {
    function __construct() {
        parent::__construct("Author");
    }

    public function getSchema() {
        return "id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,\n" .
            "first_name VARCHAR(30),\n" .
            "last_name VARCHAR(30),\n" .
            "email VARCHAR(50),\n" .
            "PRIMARY KEY(id)," .
            "UNIQUE(email)";
    }

    public function createRow($array) {
        $bindParam = new BindParam();
        $bindParam->add('s', $array['first_name']);
        $bindParam->add('s', $array['last_name']);
        $bindParam->add('s', $array['email']);
        return $this->insertValues($bindParam, "first_name, last_name, email");
    }

    public function getMailToLink($id) {
        $lastName = $this->getField($id, "last_name");
        $firstName = $this->getField($id, "first_name");
        $email = $this->getField($id, "email");
        return "<a href='mailto:$email'>" . $firstName . " " .
                $lastName . "</a>";
    }

    private function getField($id, $field) {
        $db = Database::getInstance();
        $query = "SELECT " . $field . " FROM Author WHERE id = " . $id;
        $ret = "Invalid Author";
        if ($result = $db->query($query)) {
            $row = $result->fetch_array();
            $ret = $row[$field];
        } else {
            print $db->getError();
        }
        $db->close();
        return $ret;
    }
}

?>