<?php
/** Simple PHP function for making thumbnails
 **/
require_once("models".DIRECTORY_SEPARATOR."image.php");

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
    $gallery = new Image();
    $retval = $gallery->getThumbnail($id, $width);
    header('Content-Type: image/png');
    imagepng($retval);
    imagedestroy($retval);
} else {
    print("No image found.<br>");
    print_r($_GET);
}

?>