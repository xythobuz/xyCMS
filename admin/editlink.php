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
<title>xyCMS - Edit Link</title>
</head>
<body>
<div class="admin">
<h1>Edit Link</h1>
<?

if (($_GET['w'] == "") && ($_GET['d'] == "")) {
    $sql = 'SELECT
        ord,
        title,
        url,
        id,
        nofollow
        FROM
        cms_links
        ORDER BY
        ord ASC';
    $result = mysql_query($sql);
    if (!$result) {
        die ("Query Error");
    }
?>
<table border="1">
<tr><th>Order</th>
<th>Title</th>
<th>URL</th>
<th>Nofollow</th>
<th>Save</th>
<th>Delete</th></tr>
<?
    while ($row = mysql_fetch_array($result)) {
        echo "<form action=\"editlink.php?w=";
        echo $row['id']."\" method=\"post\">";
        echo "<tr>";
        echo "<td><input type=\"text\" name=\"order\" value=\"".stripslashes($row['ord'])."\"></td>";
        echo "<td><input type=\"text\" name=\"titel\" value=\"".stripslashes($row['title'])."\"></td>";
        echo "<td><input type\"text\" name=\"link\" value=\"".stripslashes($row['url'])."\"></td>";
        echo "<td>";
        if ($row['nofollow'] == 0) {
?><input type="checkbox" name="click" value="true">
<?
        } else {
?><input type="checkbox" name="click" value="true" checked="checked">
<?
        }
        echo "</td>\n";
        echo "<td><input type=\"submit\" name=\"formaction\" value=\"Save\"></td>\n";
        echo "<td><a href=\"editlink.php?d=".$row['id']."\">Delete</a></td>";
        echo "</tr></form>";
    }
} else if ($_GET['w'] != "") {
    if (isset($_POST['click']) && ($_POST['click'] == "true")) {
        $click = "1";
    } else {
        $click = "0";
    }
    $sql = 'UPDATE
        cms_links
        SET
        url = "'.mysql_real_escape_string($_POST['link']).'",
            title = "'.mysql_real_escape_string($_POST['titel']).'",
            ord = '.mysql_real_escape_string($_POST['order']).',
            nofollow = '.$click.'
            WHERE
            id = '.mysql_real_escape_string($_GET['w']);
    $result = mysql_query($sql);
    if (!$result) {
        die ("Query Error!");
    }
    echo "Edited!";
} else if ($_GET['d'] != ""){
    $sql = 'DELETE FROM cms_links
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
