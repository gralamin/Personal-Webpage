<?php
/* Grabs an article from the given id, and renders it.
 */
require_once("models/workText.php");
require_once("renderer.php");

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

renderArticle($id, $src);

?>