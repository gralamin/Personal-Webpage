<?php
require_once("models".DIRECTORY_SEPARATOR."workItem.php");

$BOLD_TAG = "<b>";
$BOLD_TAG_END = "</b>";
$ITALIC_TAG = "<i>";
$ITALIC_TAG_END = "</i>";
$UNDERLINE_TAG = "<u>";
$UNDERLINE_TAG_END = "</u>";

function renderArticle($id, $src) {
    if ($id) {
        /* Until I get an object to represent this, query directly */
        $model = new WorkText();
        $text = $model->retrieveText($id);

        if (!$src) {
            render($text);
        } else {
            printSource($text);
        }
    } else {
        print("No article found.<br>");
        print_r($_GET);
    }
}

function printSource($text) {
    $text = htmlspecialchars($text);
    print("<pre>" . $text . "</pre>");
}

function render($text) {
    /* Split into <div> blocks */
    $divs = explode("\n\n", $text);

    foreach ($divs as $div) {
        $divArgs = "";
        $div = parseItem($div, $divArgs);
        print("<div" . $divArgs . ">" . trim($div) . "</div>");
    }
}

function parseItem($div, &$divArgs) {
    $subArgs = "";
    if (preg_match('/^{(.*)}/', $div, $match) == 1) {
        $matched = htmlspecialchars($match[1]);
        $divArgs = " " . $matched;
        $div = trim(explode('{' . $match[1] . '}', $div)[1]);
    }
    if (preg_match('/^\*/', $div) == 1) {
        $div = parseList($div, "ul", '*');
        return $div;
    }
    if (preg_match('/^#/', $div) == 1) {
        $div = parseList($div, "ol", '#');
        return $div;
    }
    while (preg_match('/(\/.*\/)/sU', $div, $match) == 1) {
        # TODO, change regex so this can be moved down safely.
        $div = parseItalize($div, $match);
    }
    while (preg_match('/(_.*_)/sU', $div, $match) == 1) {
        $div = parseUnderline($div, $match);
    }
    while (preg_match('/(\*.*\*)/sU', $div, $match) == 1) {
        $div = parseBold($div, $match);
    }
    while (preg_match('/(\[.*\])/sU', $div, $match) == 1) {
        $div = parseLink($div, $match);
    }
    return $div;
}

function parseList($listDiv, $tag, $symbol) {
    $arr = explode($symbol, $listDiv);
    $div = "<" . $tag . ">";
    for ($i = 1; $i < count($arr); $i++) {
        $liArgs = "";
        $value = parseItem($arr[$i], $liArgs);
        $div = $div . "<li " . $liArgs . ">" . $value  . "</li>";
    }
    $div = $div  . "</" . $tag . ">";
    return $div;
}

function parseLink($div, $match) {
    // Check for case 1 (article id)
    // Check for case 2 (page name with optional description.)
    $arr = explode($match[0], $div);
    $mth = substr($match[0], 1, strlen($match[0])-2);
    $url = "";
    $text = "";
    if (preg_match('/article=(\d+)/', $mth, $articleMatch) == 1) {
        $url = "work.php?id=" . $articleMatch[1];
        $article = new WorkItem();
        $articleNum = 0 + $articleMatch[1]; // Converts to int
        $title = $article->getTitle($articleNum);
        $text = "&#91;" . $title . "&#93;";
    }
    else {
        preg_match('/(.*)[|$]/', $mth, $urlMatch);
        $url = $urlMatch[1] . ".php";
    }
    if (preg_match('/\|(.+)/', $mth, $textMatch) == 1) {
        $text = substr($textMatch[0], 1);
    }
    $div = $arr[0] . "<a href='" . $url . "'>" . $text . "</a>" . $arr[1];
    return $div;
}

function parseUnderline($div, $match) {
    global $UNDERLINE_TAG, $UNDERLINE_TAG_END;
    $arr = explode($match[0], $div);
    $mth = explode("_", $match[0])[1];
    $div = $arr[0] . $UNDERLINE_TAG . $mth . $UNDERLINE_TAG_END . $arr[1];
    return $div;
}

function parseItalize($div, $match) {;
    global $ITALIC_TAG, $ITALIC_TAG_END;
    $arr = explode($match[0], $div);
    $mth = explode("/", $match[0])[1];
    $div = $arr[0] . $ITALIC_TAG . $mth . $ITALIC_TAG_END . $arr[1];
    return $div;
}

function parseBold($div, $match) {
    global $BOLD_TAG, $BOLD_TAG_END;
    $arr = explode($match[0], $div);
    $mth = explode("*", $match[0])[1];
    $div = $arr[0] . $BOLD_TAG . $mth . $BOLD_TAG_END . $arr[1];
    return $div;
}

?>