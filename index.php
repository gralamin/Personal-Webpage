<?php

session_start();

if (!isset($_SESSION[USR_ID]) ) {
    session_defaults();
}

$curPage = curPageURL();

if (!empty($_GET)) {
    if (isset($_GET["p"])) {
        $page = $_GET["p"];
    }
    else {
        $page = NULL;
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


# From www.webcheatsheet.com/PHP/get_current_page_url.php
function curPageURL() {
    $pageURL = 'http';
    if (isset($_SERVER['HTTPS']) && $_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"];
    }
    return $pageURL;
}


?>