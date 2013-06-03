<?php
require_once("renderer.php");
require_once("models".DIRECTORY_SEPARATOR."workItem.php");
require_once("models".DIRECTORY_SEPARATOR."workGallery.php");

$id = NULL;
if (!empty($_GET)) {
    if (isset($_GET["id"])) {
        $id = $_GET["id"];
    }
}

$item = new WorkItem();
$galleryModel = new WorkGallery();
$title = $item->getTitle($id);
$postedDate = $item->getDate($id);
$author = $item->getAuthorLink($id);
$repo = $item->getRepoUrl($id);
$gallery = $galleryModel->getGallery($id);

echo('<div class="content">');
echo('    <div class="inner-content">');
echo('        <h1 class="title">' . $title .'</h1>');
echo('        <div class="author">by: ' . $author . '</div>');
echo('        <div class="date">Posted on: ' . $postedDate . '</div>');
echo('        <div class="repository"><a href="' . $repo .
     '">Repository</a></div>');
echo('        <h2>Related Images</h2>');
echo('        <div class="gallery">');
foreach ($gallery as $img) {
    echo('            <div class="outerImg">');
    echo('                <a href="' . $img[1] . '" rel="lightbox[gallery]">');
    echo('                <img src="' . $img[0] . '">');
    echo('                </a>');
    echo('            </div>');
}
echo('        </div>');
echo("        <div class='article'>");
renderArticle($id, false);
echo('        </div>');
echo("        <div class='source'>");
echo("            <input type='button' value='Display Source' /> ");
renderArticle($id, true);
echo("        </div>");
echo('    </div>');
echo('</div>');
?>