<?php


require_once("util.php");

session_start();

if (!isset($_SESSION["USR_ID"]) ) {
    session_defaults();
}

require_once("docBegin.php");
if (!isset($_SESSION["firsttime"]) ) {
    $_SESSION["firsttime"] = 1;
    echo('<div id="top-warning">' .
         'This page uses CSS3 that is not implemented in most browsers yet.' .
         'This page may not display properly.' .
         '<input type="button" value="X" />' .
         '</div>');
}

$curPage = curPageURL();

$page = NULL;
if (!empty($_GET)) {
    if (isset($_GET["p"])) {
        $page = $_GET["p"];
    }
}

include("header.php");
if ($page == "about") {
    include("pages/about.php");
}
else if ($page == "contact") {
    include("pages/contact.php");
}
else if ($page == "work") {
    include("pages/work.php");
}
else if ($page == "article") {
    include("pages/article.php");
}
else {
    include("pages/index.php");
}
include("footer.php");

function session_defaults() {
    $cookie = 0;
    /*
      if (isset($_COOKIE[CKE_NAME]))
      {
      $cookie = $_COOKIE[CKE_NAME];
      }
    */
    $_SESSION[USR_LOGGED] = false;
    $_SESSION[USR_ID] = 0;
    $_SESSION[USR_NAME] = '';
    $_SESSION[USR_COOKIE] = $cookie;
    $_SESSION[USR_REMEMBER] = false;
}

?>