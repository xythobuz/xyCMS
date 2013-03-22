<?
include('config.php');
if (basename($_SERVER['PHP_SELF']) == "stats.php") {
    // Script is called on its own, establish connection and print html head
    $db = mysql_connect($sql_host, $sql_username, $sql_password);
    mysql_select_db($sql_database);
    if (mysql_errno()) {
        die ('Konnte keine Verbindung zur Datenbank aufbauen');
    }

    $renderVisitors = 0;
    $renderBots = 0;
    if (isset($_GET['img'])) {
        include("render.php");
        exit; // We are a png, so no text following!
    } else if (isset($_GET['imgB'])) {
        $renderVisitors = 1;
        include("render.php");
        exit;
    } else if (isset($_GET['imgC'])) {
        $renderBots = 1;
        include("render.php");
        exit;
    }

if ($_GET['lang'] == "en") {
    $tmpstring = $xyCMS_lang2;
} else {
    $tmpstring = $xyCMS_lang;
}
?><!DOCTYPE HTML>
<html lang="<? echo $tmpstring ?>">
<head>
<meta charset="utf-8" />
<? if (isset($_GET['css']) && !empty($_GET['css'])) { ?>
<link rel="stylesheet" href="<? echo $xyCMS_root; ?>/<? echo $_GET['css']; ?>.css" media="screen" type="text/css" />
<? } else { ?>
<link rel="stylesheet" href="<? echo $xyCMS_root; ?>/style.css" media="screen" type="text/css" />
<? } ?>
<link rel="author" href="<? echo $xyCMS_authormail; ?>" />
<link rel="shortcut icon" href="<? echo $xyCMS_root; ?>/favicon.ico" />
<meta name="author" content="<? echo $xyCMS_author; ?>">
<title>xyCMS Statistics</title>
<meta name="robots" content="nofollow">
</head>
<body>
<div class="admin">
<h1>xyCMS Statistics</h1>
<p>The left diagram shows the Pageviews, the central diagram shows the Visitors,
the right diagram shows the Bots this month.</p>
<p>Below, you see the referers of the visitors.
Google search terms are listed separately, as are internal and external links.</p>
<hr>
<?
}
?>
<div style="float: left; width: 33%;">
<h3>Pageviews</h3>
<a href="stats.php?img&amp;w=600"><img src="stats.php?img&amp;w=250" alt="Pageviews this Month"></a>
<?
// Clear old stuff, only keep last 100 days
$sql = 'DELETE FROM cms_visit WHERE ('.time().' - UNIX_TIMESTAMP(day)) > (60*60*24*100)';
mysql_query($sql);
$sql = 'DELETE FROM cms_visitors WHERE ('.time().' - UNIX_TIMESTAMP(day)) > (60*60*24*100)';
mysql_query($sql);
$sql = 'DELETE FROM cms_bots WHERE ('.time().' - UNIX_TIMESTAMP(day)) > (60*60*24*100)';
mysql_query($sql);

$sql = 'SELECT day, visitors FROM cms_visitors WHERE MONTH(day) = '.date('m');
$result = mysql_query($sql);
if ($result) {
    $viewsThisMonth = 0;
    while($row = mysql_fetch_array($result)) {
        $viewsThisMonth += $row['visitors'];
    }
} else {
    $viewsThisMonth = 0;
}
if ($viewsThisMonth != 0)
    echo "<p>".$viewsThisMonth." this month</p>";
$viewsThisMonthA = $viewsThisMonth;

$sql = 'SELECT day, visitors FROM cms_visitors WHERE MONTH(day) = '.(intval(date('m')) - 1);
$result = mysql_query($sql);
if ($result) {
    $viewsLastMonth = 0;
    while($row = mysql_fetch_array($result)) {
        $viewsLastMonth += $row['visitors'];
    }
} else {
    $viewsLastMonth = 0;
}
if ($viewsLastMonth != 0)
    echo "<p>".$viewsLastMonth." last month</p>";
$viewsLastMonthA = $viewsLastMonth;

$sql = 'SELECT day, visitors FROM cms_visitors';
$result = mysql_query($sql);
if ($result) {
    $views = 0;
    while($row = mysql_fetch_array($result)) {
        $views += $row['visitors'];
    }
} else {
    $views = 0;
}
if ($views != 0)
    echo "<p>".$views." overall</p>";
$viewsA = $views;
?>
</div>

<div style="float: left; width: 33%;">
<h3>Visitors</h3>
<a href="stats.php?imgB&amp;w=600"><img src="stats.php?imgB&amp;w=250" alt="Visitors this Month"></a>
<?
$sql = 'SELECT count(ip) AS count, day
    FROM cms_visit
    GROUP BY day
    HAVING MONTH(day) = '.date('m').'
    ORDER BY day ASC';
$result = mysql_query($sql);
if ($result) {
    $visitsThisMonth = 0;
    while($row = mysql_fetch_array($result)) {
        $visitsThisMonth += $row['count'];
    }
} else {
    $visitsThisMonth = 0;
}
$visitsThisMonthA = $visitsThisMonth;
if ($visitsThisMonth != 0)
    echo "<p>".$visitsThisMonth." this month</p>";

$sql = 'SELECT count(ip) AS count, day
    FROM cms_visit
    GROUP BY day
    HAVING MONTH(day) = '.(intval(date('m')) - 1);
$result = mysql_query($sql);
if ($result) {
    $visitsLastMonth = 0;
    while($row = mysql_fetch_array($result)) {
        $visitsLastMonth += $row['count'];
    }
} else {
    $visitsLastMonth = 0;
}
if ($visitsLastMonth != 0)
    echo "<p>".$visitsLastMonth." last month</p>";
$visitsLastMonthA = $visitsLastMonth;

$sql = 'SELECT count(ip) AS count, day
    FROM cms_visit
    GROUP BY day';
$result = mysql_query($sql);
if ($result) {
    $visits = 0;
    while($row = mysql_fetch_array($result)) {
        $visits += $row['count'];
    }
} else {
    $visits = 0;
}
if ($visits != 0)
    echo "<p>".$visits." overall</p>";
$visitsA = $visits;
?>
</div>

<div style="float: left; width: 33%;">
<h3>Bots</h3>
<a href="stats.php?imgC&amp;w=600"><img src="stats.php?imgC&amp;w=250" alt="Bots this Month"></a>
<?
$sql = 'SELECT bots AS count, day
    FROM cms_bots
    GROUP BY day
    HAVING MONTH(day) = '.date('m').'
    ORDER BY day ASC';
$result = mysql_query($sql);
if ($result) {
    $visitsThisMonth = 0;
    while($row = mysql_fetch_array($result)) {
        $visitsThisMonth += $row['count'];
    }
} else {
    $visitsThisMonth = 0;
}
if ($visitsThisMonth != 0)
    echo "<p>".$visitsThisMonth." this month</p>";

$sql = 'SELECT bots AS count, day
    FROM cms_bots
    GROUP BY day
    HAVING MONTH(day) = '.(intval(date('m')) - 1);
$result = mysql_query($sql);
if ($result) {
    $visitsLastMonth = 0;
    while($row = mysql_fetch_array($result)) {
        $visitsLastMonth += $row['count'];
    }
} else {
    $visitsLastMonth = 0;
}
if ($visitsLastMonth != 0)
    echo "<p>".$visitsLastMonth." last month</p>";

$sql = 'SELECT bots AS count, day
    FROM cms_bots
    GROUP BY day';
$result = mysql_query($sql);
if ($result) {
    $visits = 0;
    while($row = mysql_fetch_array($result)) {
        $visits += $row['count'];
    }
} else {
    $visits = 0;
}
if ($visits != 0)
    echo "<p>".$visits." overall</p>";
?>
</div>
<?
if (($viewsThisMonthA != 0) && ($visitsThisMonthA != 0))
    echo "<p>~".floor($viewsThisMonthA / $visitsThisMonthA)." Views / Visitor this month</p>";
if (($viewsLastMonthA != 0) && ($visitsLastMonthA != 0))
    echo "<p>~".floor($viewsLastMonthA / $visitsLastMonthA)." Views / Visitor last month</p>";
if (($viewsA != 0) && ($visitsA != 0))
    echo "<p>~".floor($viewsA / $visitsA)." Views / Visitor overall</p>";
?>
<div style="clear: left;">
<p>
</div>
<hr>
<h2>Referers</h2>
<?
if (!isset($_GET['clean'])) {
    // Get referers
    $sql = 'SELECT
        datum, referer, ziel
        FROM
        cms_referer
        ORDER BY
        datum DESC';
    $result = mysql_query($sql);
    if ($result) {
        $i = 0; // Count to see if there are any referes
        $g = 0; // Index for googleTerm
        $googleTerm = array(); // Store google search terms
        $googleLink = array(); // Store full google link
        $o = 0; // Index for otherLinks
        $otherLinks = array(); // Store other links (index: o)

        $regex = str_ireplace('www.', '', parse_url($xyCMS_root, PHP_URL_HOST));
        $regex = "~".$regex."~"; // Delimiter

        while ($row = mysql_fetch_array($result)) {
            $ref = $row['referer'];
            if (preg_match('~google\.~', $ref)) {
                // Google visitor
                $googleLink[$g] = $ref;
                preg_match('/q=(.*)&/UiS', $ref.'&', $googleTerm[$g]); // Store search term
                if (isset($googleTerm[$g][1])) {
                    $googleTerm[$g] = $googleTerm[$g][1]; // Thats the search term
                    $g++;
                }
            } else if (!preg_match($regex, $ref)) {
                // Other link. Save it
                $otherLinks[$o++] = $ref;
            }
            $i++;
        }
        if ($i == 0) {
            echo "There are no recorded referers. Maybe the list was cleared not long ago?";
        } else {
            // Show stats
            echo "<div style=\"float: left; width: 49%;\">";
            if (count($googleTerm) > 0) {
                echo "<table style=\"width: 100%;\" border=\"1\"><tr><th>Google search terms</th></tr>";
                foreach ($googleTerm as $key => $term) {
                    $link = $googleLink[$key];
                    if (preg_match('~url?~', $link)) {
                        $link = str_replace("url?", "#", $link);
                    }
                    if ((urldecode($term) != "") && (!(filter_var($link, FILTER_VALIDATE_URL) === FALSE))) {
                        echo "<tr><td><a href=\"".$link."\">".urldecode($term)."</a></td></tr>";
                    }
                }
                echo "</table>";
            } else {
                echo "<p>No google search terms detected.</p>";
            }
            echo "</div>\n";

            // External links
            echo "<div style=\"float: right; width: 49%;\">";
            if (count($otherLinks) > 0) {
                echo "<table style=\"width: 100%;\" border=\"1\"><tr><th>External link</th></tr>";
                foreach ($otherLinks as $link) {
                    if (!(filter_var($link, FILTER_VALIDATE_URL) === FALSE)) {
                        echo "<tr><td>";
                        echo '<a href="'.$link.'">'.getPageTitle($link).'</a>';
                        echo "</td></tr>";
                    }
                }
                echo "</table>";
            } else {
                echo "<p>No external links to this website detected.</p>";
            }
            echo "</div>\n";

            // Clear Buttons
            echo '<div style="clear: left;">';
            if (basename($_SERVER['PHP_SELF']) == "admin.php") {
                echo '<form action="admin.php" method="get">';
                echo '<input type="submit" name="clean" value="Clear All" />';
                echo '</form>';
            }
            echo "</div>\n";
        }
    } else {
        echo "Could not get referers (".mysql_error($result).")!";
    }
} else { // $_GET['clean'] is set!
    include("auth.php"); // Requires authentication!
    if ($_GET['clean'] == "Clear All") {
        $sql = 'DELETE FROM cms_referer';
        $result = mysql_query($sql);
        if (!$result) {
            echo "Could not delete referers (".mysql_error($result).")!";
        } else {
            echo "Cleared referers!";
            echo "<br><a href=\"admin.php\">OK!</a>";
        }
    } else {
        echo "Invalid clean argument!";
    }
}

if (basename($_SERVER['PHP_SELF']) == "stats.php") {
?>
<hr>
<a href="index.php">Home</a>
</div>
</body>
</html>
<?
    mysql_close($db);
}

// From http://www.cafewebmaster.com/php-get-page-title-function
function getPageTitle($url){
    $parse = parse_url($url);
    return $parse['host'];
    // This works, but is of course slow, as it loads every referer page, so we are simpler...
    /* if (!($data = file_get_contents($url)))
        return false;
    if (preg_match("#<title>(.+)<\/title>#iU", $data, $t)) {
        return trim($t[1]);
    } else {
        return false;
    } */
}
