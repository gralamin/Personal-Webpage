<?php
require_once("models".DIRECTORY_SEPARATOR."workItem.php");
require_once("models".DIRECTORY_SEPARATOR."workText.php");

class ConstTags {
    const BOLD_TAG = "<b>";
    const BOLD_TAG_END = "</b>";
    const ITALIC_TAG = "<i>";
    const ITALIC_TAG_END = "</i>";
    const UNDERLINE_TAG = "<u>";
    const UNDERLINE_TAG_END = "</u>";
    const LINK_TO_URL = "<a href=\"";
    const LINK_AFTER_URL = "\">";
    const LINK_END = "</a>";
    const DIV_START = "<div ";
    const DIV_START_END = ">";
    const DIV_END = "</div>";
}

class ConstEntities {
    const LEFT_SQUARE_BRACKET = "&#91;";
    const RIGHT_SQUARE_BRACKET = "&#93;";
}

class Link {
    public function __construct($baseUrl) {
        $this->baseUrl = $baseUrl;
        $this->termList = array();
    }

    public function addTerm($term, $value) {
        $this->termList[$term] = $value;
    }

    public function produceLink() {
        $link = "";
        $first = false;
        foreach ($this->termList as $term => $value) {
            $full_term = urlencode($term) . "=" . urlencode($value);
            if ($first) {
                $first = True;
                $link .= "?" . $full_term;
            } else {
                $link .= "&" . $full_term;
            }
        }
        return $link;
    }
}

function renderArticle($id, $src) {
    if ($id) {
        $model = new WorkText();
        $text = $model->getRow($id)["body"];

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
        print(ConstTags::DIV_START . $divArgs . ConstTags::DIV_START_END .
              trim($div) . ConstTags::DIV_END);
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
    while (preg_match('/(\[[^]]*\])/sU', $div, $match) == 1) {
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
    //print("TESTESTSTSTS<br>");
    //print_r($match);
    $arr = explode($match[0], $div);
    $mth = substr($match[0], 1, strlen($match[0])-2);
    $url = "";
    $text = "";
    if (preg_match('/article=(\d+)/', $mth, $articleMatch) == 1) {
        //print("IN_C1<br>");
        $link = new Link("");
        $link->addTerm("p", "article");
        $link->addTerm("id", $articleMatch[1]);
        $url = $link->produceLink();
        $article = new WorkItem();
        $articleNum = 0 + $articleMatch[1]; // Converts to int
        $title = $article->getTitle($articleNum);
        $text = ConstEntities::LEFT_SQUARE_BRACKET . $title .
            ConstEntities::RIGHT_SQUARE_BRACKET;
    }
    else {
        //print("IN_C2<br>");
        preg_match('/(.*)[|$]/', $mth, $urlMatch);
        $url = $urlMatch[1] . ".php";
    }
    if (preg_match('/\|(.+)/', $mth, $textMatch) == 1) {
        //print("IN_C3<br>");
        $text = substr($textMatch[0], 1);
    }
    $div = $arr[0] . ConstTags::LINK_TO_URL . $url . ConstTags::LINK_AFTER_URL .
        $text . ConstTags::LINK_END . $arr[1];
    return $div;
}

function parseUnderline($div, $match) {
    $arr = explode($match[0], $div);
    $mth = explode("_", $match[0])[1];
    $div = $arr[0] . ConstTags::UNDERLINE_TAG . $mth .
        ConstTags::UNDERLINE_TAG_END . $arr[1];
    return $div;
}

function parseItalize($div, $match) {;
    $arr = explode($match[0], $div);
    $mth = explode("/", $match[0])[1];
    $div = $arr[0] . ConstTags::ITALIC_TAG . $mth . ConstTags::ITALIC_TAG_END .
        $arr[1];
    return $div;
}

function parseBold($div, $match) {
    $arr = explode($match[0], $div);
    $mth = explode("*", $match[0])[1];
    $div = $arr[0] . ConstTags::BOLD_TAG . $mth . ConstTags::BOLD_TAG_END .
        $arr[1];
    return $div;
}

?>