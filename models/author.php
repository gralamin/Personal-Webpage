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
}

?>