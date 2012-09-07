<?php
require_once("base.php");

class WorkItem extends Model {
    function __construct() {
        parent::__construct("WorkItem");
    }

    public function getSchema() {
        $my_string = "id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,\n" .
            "name VARCHAR(30),\n" .
            "repository_url VARCHAR(250),\n" .
            "submission_date DATE,\n" .
            "author_id INT(10) UNSIGNED,\n" .
            "PRIMARY KEY(id),\n" .
            "FOREIGN KEY(author_id) REFERENCES Author(id),\n" .
            "UNIQUE(repository_url),\n" .
            "UNIQUE(name)";
        return $my_string;
    }

    public function createRow($array) {
        $bindParam = new BindParam();
        $bindParam->add('s', $array['name']);
        $bindParam->add('s', $array['repository_url']);
        $bindParam->add('s', $array['submission_date']);
        $bindParam->add('i', $array['author_id']);
        return $this->insertValues($bindParam, "name, repository_url, submission_date, author_id");
    }
}

?>