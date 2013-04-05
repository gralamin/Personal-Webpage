<?php
/* Simple php site that reads the url and opens an image based on the
 * url. */
require_once("models".DIRECTORY_SEPARATOR."workGallery.php");

$id = NULL;
if (!empty($_GET)) {
    if (isset($_GET["id"])) {
        $id = $_GET["id"];
    }
}

if ($id) {
    $gallery = new WorkGallery();
    $retval = $gallery->getRow($id);
    header('Content-Type: image/png');
    imagepng($retval);
    imagedestroy($retval);
} else {
    print("No image found.<br>");
    print_r($_GET);
}

?>