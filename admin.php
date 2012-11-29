<? include('auth.php');
if (isset($_GET['info'])) {
    phpinfo();
    exit;
}
include('config.php');
$db = mysql_connect($sql_host, $sql_username, $sql_password);
mysql_select_db($sql_database);
if (mysql_errno()) {
    die ('Konnte keine Verbindung zur Datenbank aufbauen');
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
<title>xyCMS Administration</title>
</head>
<body>
<div class="admin">
<h1>xyCMS Admin-Area</h1>
<h2>Pages</h2>
<a href="admin/addpage.php">Add Page</a><br>
<a href="admin/editpage.php">Edit Page</a><br>
<a href="admin/deletepage.php">Delete Page</a><br>
<h2>Blog</h2>
<a href="admin/addnews.php">Add News</a><br>
<a href="admin/editnews.php">Edit News</a><br>
<a href="admin/deletenews.php">Delete News</a><br>
<a href="admin/deletecomment.php">Manage Comments</a><br>
<h2>Links</h2>
<a href="admin/addlink.php">Add Link</a><br>
<a href="admin/editlink.php">Edit Links</a><br>
<h2>Misc</h2>
<a href="admin/users.php">Manage Users</a><br>
<h2>Code</h2>
<a href="admin/addcodenav.php">Add Code Footer</a><br>
<a href="admin/editcodenav.php">Edit Code Footer</a><br>
<a href="admin/addcodehead.php">Add Code Head</a><br>
<a href="admin/editcodehead.php">Edit Code Head</a><br>
<hr>
<?
// Update sitemap
$sitemap = "<?xml version='1.0' encoding='UTF-8'?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"\nxmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\nxsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9\nhttp://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\">\n<url><loc>".$xyCMS_root."/index.php?p=blog</loc></url>\n";
$sql = 'SELECT
    kuerzel, inhalt_en
    FROM
    cms
    WHERE NOT
    nolink = 1
    ORDER BY
    id';
$result = mysql_query($sql);
if (!$result) {
    die ('Query-Error!');
}
while ($row = mysql_fetch_array($result)) {
    $sitemap = $sitemap."<url><loc>";
    $sitemap = $sitemap.$xyCMS_root."/index.php?p=";
    $sitemap = $sitemap.$row['kuerzel'];
    $sitemap = $sitemap."</loc></url>\n";

    if (!(($row['inhalt_en'] == "<p>No translation available...</p>") || ($row['inhalt_en'] == "No translation available..."))) {
        $sitemap = $sitemap."<url><loc>";
        $sitemap = $sitemap.$xyCMS_root."/index.php?p=";
        $sitemap = $sitemap.$row['kuerzel'];
        $sitemap = $sitemap."&amp;lang=".$xyCMS_lang2;
        $sitemap = $sitemap."</loc></url>\n";
    }
}
$sql = 'SELECT
    id
    FROM
    cms_news
    ORDER BY
    datum DESC';
$result = mysql_query($sql);
if (!$result) {
    die("Query Error!");
}
while ($row = mysql_fetch_array($result)) {
    $sitemap = $sitemap."<url><loc>";
    $sitemap = $sitemap.$xyCMS_root."/index.php?p=blog&amp;blog=";
    $sitemap = $sitemap.$row['id'];
    $sitemap = $sitemap."</loc></url>\n";
}
$sitemap = $sitemap."</urlset>";
$path = 'sitemap.xml';
if(!file_exists($path)) {
    if (touch($path)) {
        print "Created $path...<br>\n";
    } else {
        print "Could not create $path...<br>\n";
        exit;
    }
}
if (!$handle = fopen($path, "w")) {
    print "Kann $path nicht öffnen!<br>\n";
    exit;
}
if (!fwrite($handle, $sitemap)) {
    print "Kann nicht in $path schreiben!<br>\n";
    exit;
}
fclose($handle);
print "Saved $path...<br>\n";


// Recreate RSS Feed
$sql = 'SELECT
    datum,
    ueberschrift,
    inhalt,
    id
    FROM
    cms_news
    ORDER BY
    datum DESC';
$result = mysql_query($sql);
if (!$result) {
    die ("Error!!");
}
$rss = '<?xml version="1.0"?>'."\n";
$rss = $rss.'<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">'."\n";
$rss = $rss."\t<channel>\n\t\t<atom:link href=\"".$xyCMS_root."/rss.xml\" rel=\"self\" type=\"application/rss+xml\" />\n";
$rss = $rss."\t\t<title>".$xyCMS_title." Blog</title>\n";
$rss = $rss."\t\t<link>".$xyCMS_root."/index.php?p=blog</link>\n";
$rss = $rss."\t\t<description>".$xyCMS_title.". - Artikel und Links</description>\n";
$rss = $rss."\t\t<language>de-de</language>\n";
$rss = $rss."\t\t<generator>xyCMS</generator>\n";
$rss = $rss."\t\t<ttl>60</ttl>\n";

$count = 0;
while ($row = mysql_fetch_array($result)) {
    if ($count >= 20) {
        break;
    }
    $rss = $rss."\t\t<item>\n";
    $rss = $rss."\t\t\t<title>".str_replace('>', '&gt;', str_replace('<', '&lt;', stripslashes($row['ueberschrift'])))."</title>\n";

    $rss = $rss."\t\t\t<link>".$xyCMS_root."/index.php?p=blog&amp;blog=".$row['id']."</link>\n";

    $rss = $rss."\t\t\t<description>".str_replace("&lt;a href=\"img", "&lt;a href=\"".$xyCMS_root."/img", str_replace("&lt;a href=\"/", "&lt;a href=\"".$xyCMS_root."/", str_replace("src=\"img", "src=\"".$xyCMS_root."/img", str_replace("src=\"/", "src=\"".$xyCMS_root."/", str_replace('>', '&gt;', str_replace("<", "&lt;", str_replace('&', '&amp;', stripslashes($row['inhalt']))))))))."</description>\n";
    $rss = $rss."\t\t\t<pubDate>".date(DATE_RSS, strtotime($row['datum']))."</pubDate>\n";
    $rss = $rss."\t\t\t<guid>".$xyCMS_root."/index.php?p=blog&amp;blog=".$row['id']."</guid>\n";
    $rss = $rss."\t\t</item>\n";
    $count++;
}
$rss = $rss."\t</channel>\n</rss>";

$path = 'rss.xml';
if(!file_exists($path)) {
    if (touch($path)) {
        print "Created $path...<br>\n";
    } else {
        print "Could not create $path...<br>\n";
        exit;
    }
}
if (!$handle = fopen($path, "w")) {
    print "Could not open $path<br>\n";
    exit;
}
if (!fwrite($handle, $rss)) {
    print "Could not write to $path<br>\n";
    exit;
}
fclose($handle);
print "Saved $path...<br>\n";
?>
<hr>
<a href="stats.php">Stats</a><br>
<a href="logout.php">Logout</a><br>
<a href="index.php">Home</a><br>
<a href="admin.php">Admin</a><br>
</div>
</body>
</html>
<? mysql_close($db); ?>
