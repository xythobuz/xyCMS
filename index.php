<?
include("config.php");
if ((isset($_SERVER['HTTP_USER_AGENT'])) && (stripos($_SERVER['HTTP_USER_AGENT'],"iPod") || stripos($_SERVER['HTTP_USER_AGENT'],"iPhone") || stripos($_SERVER['HTTP_USER_AGENT'],"iPad"))) {
    if (!isset($_GET['desktop'])) {
        if ($_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1') {
            if (php_sapi_name() == 'cgi') {
                header('Status: 303 See Other');
            } else {
                header('HTTP/1.1 303 See Other');
            }
        }
        $loc = $xythobuzCMS_root."/mobile/index.php";
        if (isset($_GET['p'])) {
            if ($_GET['p'] == blog) {
                $loc = $loc."?news";
                if (isset($_GET['blog']) && is_numeric($_GET['blog'])) {
                    $loc = $loc."=".$_GET['blog'];
                }
            } else {
                $loc = $loc."?p=".$_GET['p'];
            }
        }
        header ("Location: ".$loc);
        exit();
    }
}
include("func.php");
$db = mysql_connect($sql_host, $sql_username, $sql_password);
mysql_select_db($sql_database);
if (mysql_errno()) {
    die ("Can't connect to database!");
}

if (!isset($_GET['p'])) {
    $_GET['p'] = "home";
}
if (isset($_GET['lang'])) {
    $language = 2;
    $xyCMS_language = $xyCMS_lang2;
    $xyCMS_selectlang = $xyCMS_selectlang1;
    $xyCMS_selectlangsub = $xyCMS_selectlang1sub;
    $xyCMS_lang_age = $xyCMS_lang2_age;
    $xyCMS_langsql = "inhalt_en";
    $xyCMS_switchedlang = "index.php?p=".$_GET['p'];
    $xyCMS_lang1full = $xyCMS_lang1full1;
    $xyCMS_lang2full = $xyCMS_lang2full1;
    $xyCMS_link = "index.php?lang&amp;p=";
    $xyCMS_news = "index.php?p=blog&amp;lang";
    $xyCMS_searchText = $xyCMS_searchText2;
} else {
    $language = 1;
    $xyCMS_language = $xyCMS_lang;
    $xyCMS_selectlang = $xyCMS_selectlang2;
    $xyCMS_selectlangsub = $xyCMS_selectlang2sub;
    $xyCMS_lang_age = $xyCMS_lang1_age;
    $xyCMS_langsql = "inhalt";
    $xyCMS_switchedlang = "index.php?p=".$_GET['p']."&amp;lang";
    $xyCMS_lang1full = $xyCMS_lang1full2;
    $xyCMS_lang2full = $xyCMS_lang2full2;
    $xyCMS_link = "index.php?p=";
    $xyCMS_news = "index.php?p=blog";
    $xyCMS_searchText = $xyCMS_searchText1;
}
?>
<!DOCTYPE html>
<?
if ($language == 1) {
    echo '<html lang="'.$xyCMS_lang.'">';
} else {
    echo '<html lang="'.$xyCMS_lang2.'">';
}
?>
    <head>
        <meta charset="utf-8" />
        <link href="bootstrap.css" rel="stylesheet" media="screen">
        <link rel="author" href="<? echo $xyCMS_authormail; ?>">
        <link rel="shortcut icon" href="<? echo $xyCMS_root; ?>/favicon.ico">
        <meta name="author" content="<? echo $xyCMS_author; ?>">
        <? if ($_GET['p'] == "blog") {
            if (isset($xyCMS_customFeed)) { ?>
        <link rel="alternate" type="application/rss+xml" title="Blog RSS-Feed" href="<? echo $xyCMS_customFeed; ?>">
            <? } else { ?>
        <link rel="alternate" type="application/rss+xml" title="Blog RSS-Feed" href="rss.xml">
            <? }
            echo '<meta name="description" content="'.$xyCMS_title.' Blog">';
            if (isset($_GET['blog']) && is_numeric($_GET['blog'])) {
                $sql = 'SELECT * FROM cms_news WHERE id = '.mysql_real_escape_string($_GET['blog']);
                $row = mysql_fetch_array(mysql_query($sql));
                echo '<title>'.stripslashes($row['ueberschrift']).'</title>';
            } else {
                echo '<title>'.$xyCMS_title.' Blog</title>';
            }
        } else {
            $sql = 'SELECT * FROM cms WHERE kuerzel = "'.mysql_real_escape_string($_GET['p']).'"';
            $row = mysql_fetch_array(mysql_query($sql));
            if ($row) {
                echo '<meta name="description" content="'.stripslashes($row['beschreibung']).'">';
                echo '<title>'.stripslashes($row['beschreibung']).'</title>';
            } else {
                echo '<meta name="description" content="'.$xyCMS_title.' '.$xyCMS_subtitle.'">';
                echo '<title>'.$xyCMS_title.' '.$xyCMS_subtitle.'</title>';
            }
        }
        $sql = 'SELECT inhalt FROM cms_codehead ORDER BY id ASC';
        $result = mysql_query($sql);
        if (!$result) {
            die ("Error");
        }
        while ($row = mysql_fetch_array($result)) {
            echo stripslashes($row['inhalt'])."\n";
        }
?>
    </head>
<?
if (isset($xyCMS_onload)) {
    echo '<body onload="'.$xyCMS_onload.'">';
} else {
    echo '<body>';
}
?>
        <div class="container-fluid">
            <? if (($_GET['p'] != "blog") && shouldChangeLanguage($xyCMS_language, $xyCMS_lang, $xyCMS_lang2)) { ?>
            <div class="alert">
                <button type="button" class="close" data-dismiss="alert">
                    &times;
                </button>
                <strong><? echo $xyCMS_selectlang; ?></strong>
                <a href="<? echo $xyCMS_switchedlang; ?>">
                    <? echo $xyCMS_selectlangsub; ?>
                </a>
            </div>
            <? }
if (isset($_POST['inputText']) && isset($_POST['inputName'])) {
    $res = checkComment($_POST['inputText']);
    if ($res != -1) {
        redAlert("Bad Word: ".$res);
    } else if (checkCaptcha()) {
        redAlert("Captcha wrong!");
    } else {
        $sql = 'INSERT INTO
            cms_comments(datum, autor, inhalt, parent, frei)
        VALUES
            (FROM_UNIXTIME('.time().'),
            "'.mysql_real_escape_string($_POST['inputName']).'",
            "'.mysql_real_escape_string($_POST['inputText']).'",
            '.mysql_real_escape_string($_GET['blog']).',
            '.$xyCMS_com.')';
        $result = mysql_query($sql);
        if (!$result) {
            die("Database Error!");
        }
        if ($xyCMS_com == "FALSE") {
            $subject = "New Comment!";
            $body = $_POST['inputName']." posted the following comment on ".$xyCMS_title.":\n\n".$_POST['inputText']."\n";
            if (!mail($xyCMS_authormail, $subject, $body)) {
                redAlert("Could not notify moderator!");
            } else {
                greenAlert("Comment added. Validation by a moderator is pending!");
            }
        } else {
            greenAlert("Comment added!");
        }
    }
}
?>
            <div class="page-header">
            <h1><? echo $xyCMS_title; ?> <small><? echo $xyCMS_subtitle; ?></small></h1>
            </div>
            <div class="row-fluid">
                <div class="span3">
                    <div class="well" style="padding: 8px 0;">
                    <ul class="nav nav-list">
                        <li<? if ($_GET['p'] == "blog") echo ' class="active"'; ?>>
                            <a href="<? echo $xyCMS_link."blog"; ?>">Blog</a>
                        </li>
                        <li class="divider"></li>
<?
readNavData();
for ($i = 0; $i < maxNavItems(); $i++) {
    for ($n = 0; $n < nestLevel($i); $n++) {
        echo '<ul class="nav nav-list">';
    }
    echo "<li";
    if (getKuerzel($i) == $_GET['p']) {
        echo ' class="active"';
    }
    if (isUnclickable($i)) {
        echo ' class="nav-header">'.getName($i);
    } else {
        echo '><a href="';
        echo $xyCMS_link.getKuerzel($i);
        echo '">';
        echo getName($i);
        echo "</a>";
    }
    echo '</li>';
    for ($n = 0; $n < nestLevel($i); $n++) {
        echo '</ul>';
    }
    echo "\n";
}
?>
                    </ul>
                    </div>
                    <ul class="thumbnails">
                    <a href="<? echo $xyCMS_logoLink; ?>" class="thumbnail">
                            <img src="<? echo $xyCMS_logo; ?>" alt="Logo">
                        </a>
                    </ul>
                    <div class="well">
                        <? echo $xyCMS_author; ?><br>
                        <? echo floor((time() - $xyCMS_birth) / 31536000); ?>
                        <? echo $xyCMS_lang_age; ?><br>
                        <a href="mailto:<? echo $xyCMS_authormail; ?>">
                            <? echo $xyCMS_authormail; ?>
                        </a>
                        <? if (isset($xyCMS_twitterNick)) { ?>
                        <br>Twitter:
                        <a href="http://twitter.com/<? echo $xyCMS_twitterNick; ?>">
                            @<? echo $xyCMS_twitterNick; ?>
                        </a>
                        <? } ?>
                        <? if (isset($xyCMS_flattrusername)) { ?>
                        <br><a href="https://flattr.com/submit/auto?user_id=<? echo $xyCMS_flattrusername; ?>&amp;url=<? echo htmlentities($xyCMS_root); ?>">
                            <img src="https://api.flattr.com/button/flattr-badge-large.png" alt="Flattr Button">
                        </a>
                        <? } ?>
                    </div>
<?
$sql = 'SELECT title, url, nofollow FROM cms_links ORDER BY ord ASC';
$result = mysql_query($sql);
if ($result) {
?>
                    <ul class="nav nav-tabs nav-stacked">
<?
    while (($row = mysql_fetch_array($result)) != NULL) {
        echo '<li><a href="'.htmlspecialchars($row['url']).'"';
        if ($row['nofollow']) {
            echo ' rel="nofollow"';
        }
        echo '>'.$row['title']."</a></li>\n";
    }
?>
                    </ul>
<?
}
?>
                </div>
                <div class="span9">
<?
if ($_GET['p'] != "blog") {
    // Page
    echo '<ul class="breadcrumb">';
    printBread($_GET['p'], "<li><a href=\"", "\">", "</a> <span class=\"divider\">/</span></li>\n",
        "<li class=\"active\">", "</li>\n", $xyCMS_link, $_GET['p']);
    echo '</ul><ul class="nav nav-tabs">';
    echo "<li";
    if ($language == 1) {
        echo ' class="active"><a href="#">';
    } else {
        echo '><a href="'.$xyCMS_switchedlang.'">';
    }
    echo $xyCMS_lang1full."</a></li><li";
    if ($language == 2) {
        echo ' class="active"><a href="#">';
    } else {
        echo '><a href="'.$xyCMS_switchedlang.'">';
    }
    echo $xyCMS_lang2full."</a></li></ul>";
    $sql = 'SELECT * FROM cms WHERE kuerzel = "'.mysql_real_escape_string($_GET['p']).'"';
    $row = mysql_fetch_array(mysql_query($sql));
    if (!$row) {
        echo "<h1>404 <small>Page '".$_GET['p']."' not found!</small></h1>";
    } else {
        echo stripslashes($row[$xyCMS_langsql]);
        if (isset($xyCMS_flattrusername)) {
?>
            <br><a href="https://flattr.com/submit/auto?user_id=<? echo $xyCMS_flattrusername; ?>&amp;url=<? echo htmlentities($xyCMS_link.$_GET['p']); ?>">
                <img src="https://api.flattr.com/button/flattr-badge-large.png" alt="Flattr Button">
            </a>
<?      }
    }
} else {
    // Blog
    if (isset($_GET['blog']) && is_numeric($_GET['blog'])) {
        $sql = 'SELECT * FROM cms_news WHERE id = '.mysql_real_escape_string($_GET['blog']);
        $row = mysql_fetch_array(mysql_query($sql));
        if (!$row) {
            echo "<h1>404 <small>Post No. ".$_GET['blog']." not found!</small></h1>";
        } else {
            echo "<h1>".stripslashes($row['ueberschrift']);
            echo " <small>".stripslashes($row['datum'])."</small></h1>";
            echo "<p>".stripslashes($row['inhalt'])."</p>";
            if (isset($xyCMS_flattrusername)) {
?>
            <br><a href="https://flattr.com/submit/auto?user_id=<? echo $xyCMS_flattrusername; ?>&amp;url=<? echo htmlentities($xyCMS_news."&amp;blog=".$_GET['blog']); ?>">
                <img src="https://api.flattr.com/button/flattr-badge-large.png" alt="Flattr Button">
            </a>
<?      }
        }
        echo "<hr>";
        $sql = 'SELECT inhalt, autor, datum FROM cms_comments
            WHERE parent = '.mysql_real_escape_string($_GET['blog']).'
            AND frei = 1';
        $res = mysql_query($sql);
        if (!$res) {
            die("DB-Error!");
        }
        if (($row = mysql_fetch_array($res)) == NULL) {
            echo "<h2>No comments!</h2>";
        } else {
            echo "<br><h2>Comments:</h2>";
            do {
                echo "<h3>".stripslashes($row['autor'])." <small>".stripslashes($row['datum'])."</small></h2>";
                echo "<p>".htmlspecialchars(stripslashes($row['inhalt']))."</p>";
            } while (($row = mysql_fetch_array($res)) != NULL);
        }
?>
                    <hr><h3>Add comment</h3>
                    <form class="form-horizontal" method="post">
                        <div class="control-group">
                            <label class="control-label">
                                Name
                            </label>
                            <div class="controls">
                                <input type="text" name="inputName" placeholder="Name">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">
                                Comment
                            </label>
                            <div class="controls">
                                <textarea rows="5" name="inputText" placeholder="Comment"></textarea>
                            </div>
                        </div>
                        <? if (isset($xyCMS_captcha_pub)) { ?>
                        <div class="control-group">
                            <label class="control-label">
                                reCAPTCHA
                            </label>
                            <div class="controls">
<?
        require_once("recaptchalib.php");
        echo recaptcha_get_html($xyCMS_captcha_pub);
?>
                            </div>
                        </div>
                        <? } ?>
                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" class="btn">Send comment</button>
                            </div>
                        </div>
                    </form>
<?
    } else {
        $page = 0;
        if (isset($_GET['page'])) {
            if (is_numeric($_GET['page'])) {
                if ($_GET['page'] < newsPages()) {
                    $page = $_GET['page'];
                }
            }
        }
        $items = getNewsPerPage();
        $sql = 'SELECT id, datum, ueberschrift, inhalt FROM cms_news
            ORDER BY datum DESC';
        $result = mysql_query($sql);
        if (!$result) {
            echo "<h1>DB-Error!</h1>";
        } else {
            $skip = $page * $items;
            while ($skip-- > 0) {
                $row = mysql_fetch_array($result);
            }
            while (($items-- > 0) && (($row = mysql_fetch_array($result)) != NULL)) {
                echo "<h1><a href=\"".$xyCMS_news."&amp;blog=".$row['id']."\">";
                echo stripslashes($row['ueberschrift']);
                echo "</a> <small>".stripslashes($row['datum'])."</small></h1>";
                echo "<p>".stripslashes($row['inhalt'])."</p>";
                if ($items > 1)
                    echo "<hr>";
            }
        }


        getPageData($page);
        echo '<div class="pagination"><ul>';
        echo "\n";
        $min = getMinPager($page);
        $max = getMaxPager($page);
        if (prevDisabled()) {
            echo '<li class="disabled"><a href="#">&laquo;</a></li>';
        } else {
            echo '<li><a href="'.$xyCMS_news.'&amp;page='.($page - 1).'">&laquo;</a></li>';
        }
        echo "\n";
        for ($i = $min; $i < $max; $i++) {
            echo "<li";
            if ($i == $page) {
                echo ' class="active"><a href="#">';
            } else {
                echo '><a href="'.$xyCMS_news."&amp;page=".$i.'">';
            }
            echo $i.'</a></li>';
            echo "\n";
        }
        if (nextDisabled()) {
            echo '<li class="disabled"><a href="#">&raquo;</a></li>';
        } else {
            echo '<li><a href="'.$xyCMS_news.'&amp;page='.($page + 1).'">&raquo;</a></li>';
        }
        echo "\n";
        echo '</ul></div>';
        echo "\n";
    }
}
?>
                </div>
            </div>
            <div class="navbar">
                <div class="navbar-inner">
                    <ul class="nav">

                        <li><a class="brand" href="http://www.xythobuz.org">xyCMS</a></li>

                        <form class="navbar-search">
                            <input type="text" class="search-query" placeholder="<? echo $xyCMS_searchText; ?>">
                        </form>
<?
        $sql = 'SELECT inhalt FROM cms_codenav ORDER BY id ASC';
        $result = mysql_query($sql);
        if (!$result) {
            die ("Error");
        }
        while ($row = mysql_fetch_array($result)) {
            echo "<li>".stripslashes($row['inhalt'])."</li>\n";
        }
?>
                        <li><p class="navbar-text">
                            <? include("count.php") ?>
                        </p></li>
                    </ul>
                </div>
            </div>
        </div>
        <script src="http://code.jquery.com/jquery-latest.js"></script>
        <script src="bootstrap.js"></script>
    </body>
</html>
