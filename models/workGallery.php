<?php
require_once("models".DIRECTORY_SEPARATOR."base.php");
require_once("settings.php");

$BASE_IMAGE_PATH = Settings::path_from_root . "images.php?id=";
$THUMBNAIL_IMAGE_PATH = Settings::path_from_root . "thumbnail.php?id=";

class WorkGallery extends Model {
    function __construct() {
        parent::__construct("WorkGallery");
    }

    public function getSchema() {
        $my_string = "image_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,\n" .
            "work_id INT(10) UNSIGNED,\n" .
            "caption VARCHAR(255),\n" .
            "PRIMARY KEY(image_id, work_id),\n" .
            "FOREIGN KEY(work_id) REFERENCES WorkItem(id),\n" .
            "FOREIGN KEY(image_id) REFERENCES Image(id)";
        return $my_string;
    }

    public function createRow($array) {
        $bindParam = new BindParam();
        $bindParam->add('i', $array['image_id']);
        $bindParam->add('i', $array['work_id']);
        $bindParam->add('s', $array['caption']);
        return $this->insertValues($bindParam, "image_id, work_id, caption");
    }

    public function getRow($id) {
        $query = "SELECT image_id FROM " . $this->table_name .
            " WHERE image_id = ?";
        $bindParam = new BindParam();
        $bindParam->add('i', $id);
        $rows = $this->getValue($query, $bindParam);
        foreach ($rows as $row) {
            return $row['image_id'];
        }
        return NULL;
    }

    public function getGallery($workId, $width=100) {
        global $THUMBNAIL_IMAGE_PATH;
        global $BASE_IMAGE_PATH;
        $query = "SELECT image_id, caption FROM " . $this->table_name .
            " WHERE work_id = ?";
        $bindParam = new BindParam();
        $bindParam->add('i', $workId);
        $rows = $this->getValue($query, $bindParam);
        $galleryUrls = array();
        foreach ($rows as $row) {
            $thumbnailAndPath = array($THUMBNAIL_IMAGE_PATH . $row['image_id'] .
                                      "&width=" . $width, $BASE_IMAGE_PATH .
                                      $row['image_id'], $row['caption']);
            array_push($galleryUrls, $thumbnailAndPath);
        }
        return $galleryUrls;
    }
}
?>