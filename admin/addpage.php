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
<title>xyCMS - Add Page</title>
</head>
<body>
<div class="admin">
<?

if(!isset($_POST['content'])) {
?>
<h1>Add Page</h1>
<p>Enter content in text area below. Use html tags! * = mandatory</p>
<form action="addpage.php" method="post">
    <fieldset>
        <legend>Meta</legend>
        <label>Short-Name: *<input type="text" name="short" /></label>
        <label>Parent: <select name="parent">
            <option value="0">0 -&gt; Root</option>
<?
    $sql = 'SELECT
        id, linktext
        FROM
        cms';
    $result = mysql_query($sql);
    if (!$result) {
        echo "Query Error!";
        exit;
    }
    while ($row = mysql_fetch_array($result)) {
        if ($row['id'] != $_POST['id']) {
            echo '<option value="';
            echo $row['id'];
            echo '">';
            echo $row['linktext'];
            echo "</option>\n";
        }
    }
?>
        </select></label><br>
        <label>Description: *<input type="text" name="desc" /></label>
        <label>Link Text: *<input type="text" name="link" /></label>
        <label>Order: <input type="text" name="order" /></label>
        <label>Unclickable: <input type="checkbox" name="click" value="true"></label>
    </fieldset>
    <textarea name="content" rows="20" cols="68">Hier Inhalt eingeben</textarea>
    <textarea name="content_en" rows="20" cols="68">No translation available...</textarea>
    <input type="submit" name="formaction" value="Add Page!" />
</form>
<?
} else {
    if ($_POST['parent'] == "") {
        $Pparent = 0;
    } else {
        if (!is_numeric($_POST['parent'])) {
            echo "Parent NaN!";
            exit;
        }
        if ($_POST['parent'] != 0) {
            $sql = 'SELECT id FROM cms WHERE id = "'.$_POST['parent'].'"';
            $result = mysql_query($sql);
            if (!$result) {
                echo "Parent doesn't exist!";
                exit;
            }
            if (!($row = mysql_fetch_array($result))) {
                echo "Parent doesn't exist.";
                exit;
            }
        }
        $Pparent = mysql_real_escape_string($_POST['parent']);
    }
    if ($_POST['order'] != "") {
        if (!is_numeric($_POST['order'])) {
            echo "Order NaN!";
            exit;
        }
        $Porder = $_POST['order'];
    } else {
        $sql = 'SELECT order FROM cms ORDER BY DESC ord';
        $result = mysql_query($sql);
        if (!$result) {
            die ('Query-Error!');
        }
        $row = mysql_fetch_array($result);
        $Porder = $row['ord'] + 1;
    }
    if ( (!isset($_POST['short'])) || (!isset($_POST['desc'])) || (!isset($_POST['link'])) ) {
        print "Left mandatory fields blank!";
        exit;
    }
    $sql = 'SELECT kuerzel FROM cms WHERE kuerzel = "'.mysql_real_escape_string($_POST['short']).'"';
    $result = mysql_query($sql);
    if (!$result) {
        echo "Query error!";
        exit;
    }

    if (isset($_REQUEST['content'])) {
        $conte = str_replace("\r\n", "\n", $_REQUEST['content']);
        $conte = mysql_real_escape_string($conte);
    } else {
        print "No Content!";
        exit;
    }
    if (isset($_REQUEST['content_en'])) {
        $conte2 = str_replace("\r\n", "\n", $_REQUEST['content_en']);
        $conte2 = mysql_real_escape_string($conte2);
    } else {
        print "No Content!";
        exit;
    }

    if ($_POST['click'] == "true") {
        $click = "1";
    } else {
        $click = "0";
    }

    $sql = 'INSERT INTO
        cms(kuerzel, beschreibung, linktext, kategorie, ord, inhalt, inhalt_en, nolink)
        VALUES
        ("'.mysql_real_escape_string($_POST['short']).'",
            "'.mysql_real_escape_string($_POST['desc']).'",
            "'.mysql_real_escape_string($_POST['link']).'",
            "'.$Pparent.'",
            "'.$Porder.'",
            "'.$conte.'",
            "'.$conte2.'",
            '.$click.' )';
    $result = mysql_query($sql);
    if (!$result) {
        echo "Query Error(6)!";
        exit;
    }
    echo "Entry added successfully...!<br>\n";
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
