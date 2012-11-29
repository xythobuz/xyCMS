<? include('../auth.php');
include('../config.php');
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
<title>xyCMS - Delete News</title>
</head>
<body>
<div class="admin">
<h1>Delete News</h1>
<?

if (!isset($_GET['w'])) {
    $sql = 'SELECT
        id,
        ueberschrift
        FROM
        cms_news
        ORDER BY
        id';
    $result = mysql_query($sql);
    if (!$result) {
        die ('Query-Error!');
    }
?>
<table border="1">
<tr><th>ID</th>
<th>Überschrift</th>
<th>Löschen?</th></tr>
<?
    while ($row = mysql_fetch_array($result)) {
        echo "<tr>";
        echo "<td>".$row['id']."</td>\n";
        echo "<td>".$row['ueberschrift']."</td>\n";
        echo '<td><a href="deletenews.php?w='.$row['id'].'">Löschen</a></td></tr>'."\n";
    }
?>
</table>
<?
} else {
    $sql = 'DELETE FROM cms_news
        WHERE id = '.mysql_real_escape_string($_GET['w']).'';
    $result = mysql_query($sql);
    if (!$result) {
        print "Database delete failed! (42)<br>\n";
        exit;
    }
    print "Deleted Database Entry...<br>\n";
}

mysql_close($db);
?>
<hr>
<a href="<? echo $xyCMS_root; ?>/logout.php">Logout</a><br>
<a href="<? echo $xyCMS_root; ?>/index.php">Home</a><br>
<a href="<? echo $xyCMS_root; ?>/admin.php">Admin</a><br>
</div>
</body>
</html>
