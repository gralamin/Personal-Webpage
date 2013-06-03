<?php
require_once("models".DIRECTORY_SEPARATOR."base.php");
require_once("models".DIRECTORY_SEPARATOR."blob.php");
require_once("settings.php");

class Image extends Model {
    function __construct() {
        parent::__construct("Image");
    }

    public function getSchema() {
        $my_string = "id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,\n" .
            "img MEDIUMBLOB,\n" .
            "sha CHAR(64),\n" .
            "PRIMARY KEY(id),\n" .
            "UNIQUE(sha)";
        return $my_string;
    }

    public function createRow($array) {
        $bindParam = new BindParam();
        $nullValue = NULL;
        if (filesize($array['img']) > Settings::max_file_size) {
            return false;
        }
        $hashValue = hash_file("sha256", $array['img']);
        $bindParam->add('b', $nullValue);
        $bindParam->add('s', $hashValue);
        $blobs = array(new FileBlob(0, $array['img']));
        return $this->insertValues($bindParam, "img, sha",
                                   $blobs);
    }

    public function getRow($id) {
        $query = "SELECT img FROM " . $this->table_name . " WHERE id = ?";
        $bindParam = new BindParam();
        $bindParam->add('i', $id);
        $rows = $this->getValue($query, $bindParam);
        foreach ($rows as $row) {
            return imagecreatefromstring($row['img']);
        }
        return NULL;
    }

    public function getThumbnail($id, $width) {
        $query = "SELECT img FROM " . $this->table_name . " WHERE id = ?";
        $bindParam = new BindParam();
        $bindParam->add('i', $id);
        $rows = $this->getValue($query, $bindParam);
        foreach ($rows as $row) {
            return make_thumb($row['img'], $width);
        }
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