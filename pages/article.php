<?php
require_once("renderer.php");
require_once("models".DIRECTORY_SEPARATOR."workItem.php");

$id = NULL;
if (!empty($_GET)) {
    if (isset($_GET["id"])) {
        $id = $_GET["id"];
    }
}

$item = new WorkItem();
$title = $item->getTitle($id);
$postedDate = $item->getDate($id);
$author = $item->getAuthorLink($id);
$repo = $item->getRepoUrl($id);

echo('<div class="content">');
echo('<h1>' . $title .'</h1>');
echo('<div class="author">by: ' . $author . '</div>');
echo('<div class="date">Posted on: ' . $postedDate . '</div>');
echo('<div class="repository"><a href="' . $repo . '">Link to Repository.</a>');
echo("    <div class='article'>");
renderArticle($id, false);
echo('    </div>');
echo("    <div class='source'>");
echo("        <input type='button' value='Display Source' /> ");
renderArticle($id, true);
echo("    </div>");
echo('</div>');
?>