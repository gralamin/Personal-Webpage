<?php
require_once("models".DIRECTORY_SEPARATOR."base.php");
require_once("models".DIRECTORY_SEPARATOR."blob.php");

class WorkText extends Model {
    function __construct() {
        parent::__construct("WorkText");
    }

    public function getSchema() {
        $my_string = "work_id INT(10) UNSIGNED,\n" .
            "body TEXT,\n" .
            "PRIMARY KEY(work_id),\n" .
            "FOREIGN KEY(work_id) REFERENCES WorkItem(id)";
        return $my_string;
    }

    public function createRow($array) {
        $bindParam = new BindParam();
        $nullValue = NULL;
        $bindParam->add('i', $array['work_id']);
        $bindParam->add('b', $nullValue);
        $blobs = array(new FileBlob(1, $array['body']));
        return $this->insertValues($bindParam, "work_id, body", $blobs);
    }

    public function retrieveText($id) {
        /* This function is not fit for production */
        $text = "";
        $db = Database::getInstance();
        if ($result = $db->query("SELECT * FROM WorkText WHERE work_id = " . $id)) {
            $row = $result->fetch_array();
            $text = $row['body'];
        } else {
            print $db->getError();
        }
        $db->close();
        return $text;
    }
}

?>