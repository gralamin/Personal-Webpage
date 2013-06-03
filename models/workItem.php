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

    public function isNew($id) {
        // A post is new, if: It is the last post made
        // or if it was made in the last week.
        if ($id == $this->getLatest())
            return True;
        $date = $this->getDate($id);
        $cur = new DateTime();
        $sub = new DateTime($date);

        $val = $cur.diff($sub);
        return $val->days <= 7;
    }

    public function getLatest() {
        $query = "SELECT id FROM WorkItem ORDER BY submission_date LIMIT ?";
        $lim = 1;
        $bindParam = new BindParam();
        $bindParam->add('i', $lim);
        $rows = $this->getValue($query, $bindParam);
        $ret = "Invalid Article";
        foreach ($rows as $row) {
            $ret = $row["id"];
        }
        return $ret;
    }

    public function getAuthorLink($id) {
        $aid = $this->getField($id, "author_id");
        $auth = new Author();
        return $auth->getMailToLink($id);
    }

    private function getField($id, $field) {
        $query = "SELECT " . $field . " FROM WorkItem WHERE id = ?";
        $bindParam = new BindParam();
        $bindParam->add('i', $id);
        $rows = $this->getValue($query, $bindParam);
        $ret = "Invalid article";
        foreach ($rows as $row) {
            $ret = $row[$field];
        }
        return $ret;
    }
}

?>