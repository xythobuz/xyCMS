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
<title>xyCMS - Delete Comment</title>
</head>
<body>
<div class="admin">
<?

if (!isset($_GET['w']) && !isset($_GET['t'])) {
    $sql = 'SELECT
        id,
        autor,
        inhalt,
        parent,
        frei
        FROM
        cms_comments
        ORDER BY
        parent';
    $result = mysql_query($sql);
    if (!$result) {
        die ('Query-Error!');
    }
?>
<table border="1">
<tr><th>Autor</th>
<th>Inhalt</th>
<th>Parent</th>
<th>Freigegeben</th>
<th>Löschen?</th></tr>
<?
    while ($row = mysql_fetch_array($result)) {
        echo "<tr>";
        echo "<td>".$row['autor']."</td>\n";
        echo "<td>".$row['inhalt']."</td>\n";
        $sql = 'SELECT
            ueberschrift
            FROM cms_news
            WHERE id = '.$row['parent'];
        $result2 = mysql_query($sql);
        if (!$result2) {
            die ('Query Error!');
        }
        $row2 = mysql_fetch_array($result2);
        echo "<td>".$row2['ueberschrift']."</td>\n";
        echo '<td><a href="deletecomment.php?t='.$row['id'].'">'.$row['frei']."</a></td>\n";
        echo '<td><a href="deletecomment.php?w='.$row['id'].'">Löschen</a></td></tr>'."\n";
    }
?>
</table>
<?
} else if (isset($_GET['w'])) {
    $sql = 'DELETE FROM cms_comments
        WHERE id = '.mysql_real_escape_string($_GET['w']).'';
    $result = mysql_query($sql);
    if (!$result) {
        print "Database delete failed! (44)<br>\n";
        exit;
    }
    print "Deleted Database Entry...<br>\n";
} else {
    $sql = 'SELECT
        frei
        FROM
        cms_comments
        WHERE id = '.mysql_real_escape_string($_GET['t']);
    $result = mysql_query($sql);
    if (!$result) {
        die ("Error!");
    }
    $row = mysql_fetch_array($result);
    $sql = 'UPDATE cms_news
        SET
        frei = "';
    if ($row['frei'] == 0) {
        $sql = 'UPDATE cms_comments
            SET
            frei = "1"
            WHERE
            id = '.mysql_real_escape_string($_GET['t']);
        $tmp = "TRUE";
    } else {
        $sql = 'UPDATE cms_comments
            SET
            frei = "0"
            WHERE
            id = '.mysql_real_escape_string($_GET['t']);
        $tmp = "FALSE";
    }
    $result = mysql_query($sql);
    if (!$result) {
        die ("Error");
    } else {
        echo $tmp."!\n";
    }
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
