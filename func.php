<?
$newsPerPage = 8;
$pageItems = 4;

$countPages = 0;
$minPage = 0;
$maxPage = 0;
$prevOn = 0;
$nextOn = 0;

function getNewsPerPage() {
    global $newsPerPage;
    return $newsPerPage;
}

function newsPages() {
    global $newsPerPage;
    global $countPages;
    $sql = 'SELECT COUNT(*) AS result FROM cms_news';
    $row = mysql_fetch_array(mysql_query($sql));
    $countPages = ceil($row['result'] / $newsPerPage);
    return $countPages;
}

function getPageData($page) {
    global $minPage;
    global $maxPage;
    global $prevOn;
    global $nextOn;
    global $countPages;
    global $newsPerPage;
    global $pageItems;

    newsPages();

    if ($countPages <= $newsPerPage) {
        $minPage = 0;
        $maxPage = 1;
        $prevOn = 0;
        $nextOn = 0;
    } else {
        // We have pages
        if ($page > 0) {
            $prevOn = 1;
        } else {
            $prevOn = 0;
        }
        if ($page < ($countPages - 1)) {
            $nextOn = 1;
        } else {
            $nextOn = 0;
        }
        if ($page < ceil($pageItems / 2)) {
            // Current page < 3
            $minPage = 0;
            $maxPage = $pageItems;
        } else {
            if ($page < ($countPages - ceil($pageItems / 2))) {
                $minPage = $page - floor($pageItems / 2);
                $maxPage = $page + floor($pageItems / 2);
            } else {
                $maxPage = $countPages;
                $minPage = ($countPages - $pageItems);
            }
        }
    }
}

function getMinPager() {
    global $minPage;
    return $minPage;
}

function getMaxPager() {
    global $maxPage;
    return $maxPage;
}

function prevDisabled() {
    global $prevOn;
    return !$prevOn;

}

function nextDisabled() {
    global $nextOn;
    return !$nextOn;
}

function printBread($id, $before1, $between1, $after1, $before2, $after2, $link, $curr) {
    $sql = 'SELECT id FROM cms WHERE kuerzel = "'.$id.'"';
    $res = mysql_query($sql);
    $row = mysql_fetch_array($res);
    printBreadcrumbs($row['id'], $before1, $between1, $after1, $before2, $after2, $link, $row['id']);
}

function printBreadcrumbs($id, $before1, $between1, $after1, $before2, $after2, $link, $curr) {
    $sql = 'SELECT kuerzel, linktext, kategorie FROM cms WHERE id = '.$id;
    $res = mysql_query($sql);
    $row = mysql_fetch_array($res);
    if ($row['kategorie'] != 0) {
        printBreadcrumbs($row['kategorie'], $before1, $between1, $after1, $before2, $after2, $link, $curr);
    }
    if ($id == $curr) {
        echo $before2.$row['linktext'].$after2;
    } else {
        echo $before1.$link.$row['kuerzel'].$between1.$row['linktext'].$after1;
    }
}

function greenAlert($text) {
?>
<div class="alert alert-success">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>Success</strong> <? echo $text; ?>
</div>
<?
}

function redAlert($text) {
?>
<div class="alert alert-error">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>Error</strong> <? echo $text; ?>
</div>
<?
}

function checkCaptcha() {
    global $xyCMS_captcha_pub;
    global $xyCMS_captcha_priv;
    if (isset($xyCMS_captcha_priv)) {
        require_once('recaptchalib.php');
        $resp = recaptcha_check_answer($xyCMS_captcha_priv, $_SERVER['REMOTE_ADDR'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']);
        if (!$resp->is_valid) {
            return TRUE;
        }
    }
    return FALSE;
}

$navCount = 0;
$navLinks = array();
$navNames = array();
$navNest = array();
$navNoLink = array();

function readNavData() {
    global $navCount;
    global $navLinks;
    global $navNames;
    global $navNest;
    global $navNoLink;

    $sql = 'SELECT COUNT(*) AS result FROM cms';
    $res = mysql_query($sql);
    if (!$res) {
        die("Database Error");
    }
    $row = mysql_fetch_array($res);
    $navCount = $row['result'];

    readDataLevel(0, 0, 0);
}

function readDataLevel($i, $level, $parent) {
    global $navCount;
    global $navLinks;
    global $navNames;
    global $navNest;
    global $navNoLink;

    $sql = 'SELECT * FROM cms WHERE kategorie = '.$parent.' ORDER BY ord ASC';
    $res = mysql_query($sql);
    if (!$res) {
        die("Database Error!");
    }
    while (($row = mysql_fetch_array($res)) != NULL) {
        $navLinks[$i] = stripslashes($row['kuerzel']);
        $navNames[$i] = stripslashes($row['linktext']);
        $navNest[$i] = $level;
        if ($row['nolink']) {
            $navNoLink[$i] = TRUE;
        } else {
            $navNoLink[$i] = FALSE;
        }
        $i++;
        $i = readDataLevel($i, $level + 1, $row['id']);
    }
    return $i;
}

function maxNavItems() {
    global $navCount;
    return $navCount;
}

function nestLevel($i) {
    global $navNest;
    return $navNest[$i];
}

function getKuerzel($i) {
    global $navLinks;
    return $navLinks[$i];
}

function getName($i) {
    global $navNames;
    return $navNames[$i];
}

function isUnclickable($i) {
    global $navNoLink;
    return $navNoLink[$i];
}

function shouldChangeLanguage($l, $a, $b) {
    $langs = array($a, $b);
    $bestlang = preferred_language($langs);
    if ($l == $bestlang) {
        return FALSE;
    } else {
        return TRUE;
    }
}

function preferred_language ($available_languages, $http_accept_language="auto") {
    // if $http_accept_language was left out, read it from the HTTP-Header
    if ($http_accept_language == "auto") $http_accept_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    // standard  for HTTP_ACCEPT_LANGUAGE is defined under
    // http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4
    // pattern to find is therefore something like this:
    //    1#( language-range [ ";" "q" "=" qvalue ] )
    // where:
    //    language-range  = ( ( 1*8ALPHA *( "-" 1*8ALPHA ) ) | "*" )
    //    qvalue         = ( "0" [ "." 0*3DIGIT ] )
    //            | ( "1" [ "." 0*3("0") ] )
    preg_match_all("/([[:alpha:]]{1,8})(-([[:alpha:]|-]{1,8}))?" .
        "(\s*;\s*q\s*=\s*(1\.0{0,3}|0\.\d{0,3}))?\s*(,|$)/i",
        $http_accept_language, $hits, PREG_SET_ORDER);

    // default language (in case of no hits) is the first in the array
    $bestlang = $available_languages[0];
    $bestqval = 0;

    foreach ($hits as $arr) {
        // read data from the array of this hit
        $langprefix = strtolower ($arr[1]);
        if (!empty($arr[3])) {
            $langrange = strtolower ($arr[3]);
            $language = $langprefix . "-" . $langrange;
        }
        else $language = $langprefix;
        $qvalue = 1.0;
        if (!empty($arr[5])) $qvalue = floatval($arr[5]);

        // find q-maximal language
        if (in_array($language,$available_languages) && ($qvalue > $bestqval)) {
            $bestlang = $language;
            $bestqval = $qvalue;
        }
        // if no direct hit, try the prefix only but decrease q-value by 10% (as http_negotiate_language does)
        else if (in_array($langprefix,$available_languages) && (($qvalue*0.9) > $bestqval)) {
            $bestlang = $langprefix;
            $bestqval = $qvalue*0.9;
        }
    }
    return $bestlang;
}
?>
