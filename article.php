<?php
/* Grabs an article from the given id, and renders it.
 */
require_once("models".DIRECTORY_SEPARATOR."workItem.php");
require_once("models".DIRECTORY_SEPARATOR."workText.php");
require_once("renderer.php");

function create_item($witem) {
    $div = "<div class=\"work-item\">";
}

$id = NULL;
$src = FALSE;
if (!empty($_GET)) {
    if (isset($_GET["id"])) {
        $id = $_GET["id"];
    }
    if (isset($_GET["src"])) {
        $src = (bool) $_GET["src"];
    }
}

$item = new WorkItem();
renderArticle($id, $src);

?>