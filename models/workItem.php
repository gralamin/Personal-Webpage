<?php
require_once("base.php");
require_once("author.php");

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

    public function getTitle($id) {
        return $this->getField($id, "name");
    }

    public function getRepoUrl($id) {
        return $this->getField($id, "repository_url");
    }

    public function getDate($id) {
        return $this->getField($id, "submission_date");
    }

    public function getAuthorLink($id) {
        $aid = $this->getField($id, "author_id");
        $auth = new Author();
        return $auth->getMailToLink($id);
    }

    private function getField($id, $field) {
        $db = Database::getInstance();
        $query = "SELECT " . $field . " FROM WorkItem WHERE id = " . $id;
        $ret = "Invalid article";
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