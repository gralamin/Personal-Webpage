<?php
require_once("models".DIRECTORY_SEPARATOR."base.php");
require_once("models".DIRECTORY_SEPARATOR."blob.php");
require_once("settings.php");

$BASE_IMAGE_PATH = Settings::path_from_root . "images.php?id=";
$THUMBNAIL_IMAGE_PATH = Settings::path_from_root . "thumbnail.php?id=";

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

    public function getRow($id) {
        $query = "SELECT img FROM WorkGallery WHERE id = ?";
        $bindParam = new BindParam();
        $bindParam->add('i', $id);
        $rows = $this->getValue($query, $bindParam);
        foreach ($rows as $row) {
            return imagecreatefromstring($row['img']);
        }
    }

    public function getThumbnail($id, $width) {
        $query = "SELECT img FROM WorkGallery WHERE id = ?";
        $bindParam = new BindParam();
        $bindParam->add('i', $id);
        $rows = $this->getValue($query, $bindParam);
        foreach ($rows as $row) {
            return make_thumb($row['img'], $width);
        }
    }

    public function getGallery($workId, $width=100) {
        global $THUMBNAIL_IMAGE_PATH;
        global $BASE_IMAGE_PATH;
        $query = "SELECT id FROM WorkGallery WHERE work_id = ?";
        $bindParam = new BindParam();
        $bindParam->add('i', $workId);
        $rows = $this->getValue($query, $bindParam);
        $galleryUrls = array();
        foreach ($rows as $row) {
            $thumbnailAndPath = array($THUMBNAIL_IMAGE_PATH . $row['id'] .
                                      "&width=" . $width, $BASE_IMAGE_PATH .
                                      $row['id']);
            array_push($galleryUrls, $thumbnailAndPath);
        }
        return $galleryUrls;
    }
}

function make_thumb($src, $desired_width) {
    /* Get the source type */
    $img_res = imagecreatefromstring($src);
    if ($img_res !== false) {
        $width = imagesx($img_res);
        $height = imagesy($img_res);
        /* find the "desired height" of this thumbnail, relative to the desired
           width  */
        $desired_height = floor($height * ($desired_width / $width));

        /* create a new, "virtual" image */
        $virtual_image = imagecreatetruecolor($desired_width, $desired_height);

        /* copy source image at a resized size */
        imagecopyresampled($virtual_image, $img_res, 0, 0, 0, 0,
                           $desired_width, $desired_height, $width,
                           $height);
        imagedestroy($img_res);
        return $virtual_image;
    }
    else {
        echo 'An error occurred.';
    }
}

?>