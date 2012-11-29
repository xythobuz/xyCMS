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
<title>xyCMS - Edit Code</title>
</head>
<body>
<div class="admin">
<h1>Edit Code in &lt;head&gt;</h1>
<?

if ($_GET['d'] == "") {
    $sql = 'SELECT
        inhalt,
        id
        FROM
        cms_codehead';
    $result = mysql_query($sql);
    if (!$result) {
        die ("Query Error");
    }
?>
<table border="1">
<tr><th>Content</th>
<th>Delete</th></tr>
<?
    while ($row = mysql_fetch_array($result)) {
        echo "<tr>";
        echo "<td>".htmlspecialchars(stripslashes($row['inhalt']))."</td>";
        echo "<td><a href=\"editcodehead.php?d=".$row['id']."\">Delete</a></td>";
        echo "</tr>";
    }
} else {
    $sql = 'DELETE FROM cms_codehead
        WHERE id = '.mysql_real_escape_string($_GET['d']);
    $result = mysql_query($sql);
    if (!$result) {
        die ("Query Error.");
    }
    echo "Deleted...";
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
