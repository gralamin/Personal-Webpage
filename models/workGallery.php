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
            "PRIMARY KEY(id),\n" .
            "FOREIGN KEY(work_id) REFERENCES WorkItem(id)";
        return $my_string;
    }

    public function createRow($array) {
        $bindParam = new BindParam();
        $nullValue = NULL;
        $bindParam->add('i', $array['work_id']);
        $bindParam->add('b', $nullValue);
        $blobs = array(new FileBlob(1, $array['img']));
        return $this->insertValues($bindParam, "work_id, img", $blobs);
    }
}

?>