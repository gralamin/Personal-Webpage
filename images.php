<?php
/* Simple php site that reads the url and opens an image based on the
 * url. */
require_once("database.php");

$id = NULL;
if (!empty($_GET)) {
    if (isset($_GET["id"])) {
        $id = $_GET["id"];
    }
}

if ($id) {
    /* Until I get an object to represent this, query directly */
    $db = Database::getInstance();
    if ($result = $db->query("SELECT img FROM WorkGallery WHERE id = " . $id)) {
        $row = $result->fetch_array();
        header('Content-Type: image/png');
        $virtual_image = imagecreatefromstring($row['img']);
        imagepng($virtual_image);
        imagedestroy($virtual_image);
    } else {
        print $db->getError();
    }
    $db->close();
} else {
    print("No image found.<br>");
    print_r($_GET);
}

?>