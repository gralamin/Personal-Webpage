<?php
/** Simple PHP function for making thumbnails
 **/
require_once("database.php");

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
        header('Content-Type: image/png');
        imagepng($virtual_image);
        imagedestroy($virtual_image);
        imagedestroy($img_res);
    }
    else {
        echo 'An error occurred.';
    }
}

$id = NULL;
$width = 100;
if (!empty($_GET)) {
    if (isset($_GET["id"])) {
        $id = $_GET["id"];
    }
    if (isset($_GET["width"])) {
        $width = $_GET["width"];
    }
}

if ($id) {
    /* Until I get an object to represent this, query directly */
    $img = NULL;
    $db = Database::getInstance();
    if ($result = $db->query("SELECT img FROM WorkGallery WHERE id = " . $id)) {
        $row = $result->fetch_array();
        $img = $row['img'];
    } else {
        print $db->getError();
    }
    $db->close();
    make_thumb($img, $width);
} else {
    print("No image found.<br>");
    print_r($_GET);
}

?>