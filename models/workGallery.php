<?php
require_once("models".DIRECTORY_SEPARATOR."base.php");
require_once("models".DIRECTORY_SEPARATOR."blob.php");

class WorkGallery extends Model {
    function __construct() {
        parent::__construct("WorkGallery");
    }

    public function getSchema() {
        $my_string = "id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,\n" .
            "work_id INT(10) UNSIGNED,\n" .
            "img BLOB,\n" .
            "sha CHAR(64),\n" .
            "PRIMARY KEY(id),\n" .
            "UNIQUE(sha),\n" .
            "FOREIGN KEY(work_id) REFERENCES WorkItem(id)";
        return $my_string;
    }

    public function createRow($array) {
        $bindParam = new BindParam();
        $nullValue = NULL;
        $hashValue = hash_file("sha256", $array['img']);
        $bindParam->add('i', $array['work_id']);
        $bindParam->add('b', $nullValue);
        $bindParam->add('s', $hashValue);
        $blobs = array(new FileBlob(1, $array['img']));
        return $this->insertValues($bindParam, "work_id, img, sha", $blobs);
    }
}

?>