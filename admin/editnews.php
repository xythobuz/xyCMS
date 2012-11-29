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
<title>xyCMS - Edit News</title>
</head>
<body>
<div class="admin">
<?

if (!isset($_GET['w'])) {
    $sql = 'SELECT
        id,
        ueberschrift
        FROM
        cms_news
        ORDER BY
        id DESC';
    $result = mysql_query($sql);
    if (!$result) {
        die ('Query-Error!');
    }
?>
<table border="1">
<tr><th>ID</th>
<th>Heading</th>
<th>Edit?</th></tr>
<?
    while ($row = mysql_fetch_array($result)) {
        echo "<tr>";
        echo "<td>".stripslashes($row['id'])."</td>\n";
        echo "<td>".stripslashes($row['ueberschrift'])."</td>\n";
        echo '<td><a href="editnews.php?w='.$row['id'].'">Edit</a></td></tr>'."\n";
    }
?>
</table>
<?
} else {
    if (!isset($_POST['inhalt'])) {
        // Show edit form
        $sql = 'SELECT
            ueberschrift,
            inhalt
            FROM cms_news
            WHERE id = '.stripslashes($_GET['w']);
        $result = mysql_query($sql);
        if (!$result) {
            die ('Could not read table cms_news');
        }
        $row = mysql_fetch_array($result);
?>
<form action="editnews.php?w=<? echo $_GET['w']; ?>" method="post">
    <fieldset>
    <label>Heading: <input type="text" name="head" value="<?
        echo $row['ueberschrift'];
?>"></label><br>
    <textarea name="inhalt" rows="20" cols="68"><?
        echo stripslashes($row['inhalt']);
?></textarea><br>
        <input type="submit" name="formaction" value="Save" />
    </fieldset>
</form>
<?
    } else {
        $sql = 'UPDATE
            cms_news
            SET
            ueberschrift = "'.mysql_real_escape_string($_POST['head']).'",
                inhalt = "'.mysql_real_escape_string($_POST['inhalt']).'"
                WHERE id = '.mysql_real_escape_string($_GET['w']);
        $result = mysql_query($sql);
        if (!$result) {
            echo mysql_error();
            die ("Could not update cms_news");
        }
        echo "Updated successfully!";
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
