<?
include('../config.php');
include('func.php');
$db = mysql_connect($sql_host, $sql_username, $sql_password);
mysql_select_db($sql_database);
if (mysql_errno()) {
	die ('Could not connect to database!');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<meta content="yes" name="apple-mobile-web-app-capable" />
<meta content="minimum-scale=1.0, width=device-width, maximum-scale=0.6667, user-scalable=no" name="viewport" />
<link href="style.css" rel="stylesheet" media="screen" type="text/css" />
<link rel="apple-touch-icon" href="images/icon.png" />
<link rel="apple-touch-startup-image" href="images/load.png" />
<title><? echo $xyCMS_title; ?></title>
<?
$sql = 'SELECT
	inhalt
FROM
	cms_codehead
ORDER BY
	id ASC';
$result = mysql_query($sql);
if (!$result) {
	die ("Error");
}
while ($row = mysql_fetch_array($result)) {
	echo stripslashes($row['inhalt'])."\n";
}
?>
<script src="functions.js" type="text/javascript"></script>
<? if (isset($xyCMS_onload)) { ?>
<script type="text/javascript">
window.onDomReady(onReady);
function onReady() {
	<? echo $xyCMS_onload; ?>
}
</script>
<? } ?>
</head>
<body>
<div id="topbar">
<? if (isset($_GET['p']) || isset($_GET['search'])) {
	if ((isset($_SERVER['HTTP_REFERER'])) && (strpos($_SERVER['HTTP_REFERER'], $xyCMS_root."/index.php") === 0)) { ?>
	<div id="leftnav"><a href="index.php"><img src="images/home.png" alt="Home" /></a></div>
	<? } else { ?>
	<div id="leftnav"><a href="javascript:history.back();">Back</a></div>
	<? }
} else if (isset($_GET['news']) && !is_numeric($_GET['news'])) { ?>
	<div id="leftnav"><a href="index.php"><img src="images/home.png" alt="Home" /></a></div>
<? } else if (isset($_GET['news'])) { ?>
	<div id="leftnav"><a href="index.php?news">Back</a></div>
<? } else { ?>
	<div id="leftnav"><a class="noeffect" href="../index.php?desktop"><img alt="Desktop Version" src="images/pc.png" /></a></div>
<? } ?>
	<div id="title"><? echo $xyCMS_title; ?></div>
<? if (isset($_GET['p'])) {
	if (isset($_GET['lang'])) { // Link to lang 1 ?>
	<div id="rightnav"><a href="index.php?p=<? echo $_GET['p']; ?>"><img alt="Change language" src="../img/flags/<? echo $xyCMS_lang; ?>.png"></a></div>
<?	} else { // Link to lang 2 ?>
	<div id="rightnav"><a href="index.php?p=<? echo $_GET['p']; ?>&amp;lang"><img alt="Change language" src="../img/flags/<? echo $xyCMS_lang2; ?>.png"></a></div>
<?	}
} ?>
</div>
<? if (isset($_GET['lang'])) {
	$inhaltLanguage = "inhalt_en";
} else {
	$inhaltLanguage = "inhalt";
} ?>
<? if ((!isset($_GET['search'])) && (!isset($_GET['news']))) { ?>
<div class="searchbox">
	<form action="index.php" method="get">
		<fieldset>
			<input id="search" placeholder="search" type="text" name="search" />
			<input id="submit" type="hidden" />
		</fieldset>
	</form>
</div>
<? } ?>
<div id="content">
<? if (isset($_GET['p'])) { // Page:
	$sql = "SELECT inhalt, inhalt_en, linktext
	FROM cms
	WHERE kuerzel = '".mysql_real_escape_string($_GET['p'])."'";
	$result = mysql_query($sql);
	if (!$result) {
		die("Database Error");
	}
	$row = mysql_fetch_array($result);
?>	<span class="graytitle"><? echo $row['linktext']; ?></span>
	<ul class="pageitem">
		<li class="textbox">
<?
	$content = stripslashes($row[$inhaltLanguage]);
	$content = preg_replace('#(href|src)="([^:"]*)(?:")#','$1="'.$xyCMS_root.'/$2"',$content);
	echo $content;
?>		</li>
	</ul>
<?
} else if (isset($_GET['search'])) {  // Search:
?>	<span class="graytitle">Search</span>
<?	searchInCms($_GET['search']);
} else if (isset($_GET['news'])) {
	if ($_GET['news'] == "") {
		// List articles
		listNews();
	} else {
		// Show article
		$sql = 'SELECT inhalt, ueberschrift, datum
		FROM cms_news
		WHERE id = '.mysql_real_escape_string($_GET['news']);
		$result = mysql_query($sql);
		if (!$result) {
			echo "404 - Page not found";
			exit;
		}
		$row = mysql_fetch_array($result);
?>	<span class="graytitle"><? echo stripslashes(stripslashes($row['ueberschrift']))." (".$row['datum'].")"; ?></span>
	<ul class="pageitem">
		<li class="textbox">
<?		$content = stripslashes($row['inhalt']);
		$content = preg_replace('#(href|src)="([^:"]*)(?:")#','$1="'.$xyCMS_root.'/$2"',$content);
		echo $content;
?>		</li>
	</ul>
<?      if (isset($xyCMS_disqus)) { ?>
    <div id="disqus_thread"></div>
    <script type="text/javascript">
        var disqus_shortname = '<? echo $xyCMS_disqus; ?>';
        (function() {
            var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
            dsq.src = 'http://' + disqus_shortname + '.disqus.com/embed.js';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
        })();
    </script>
    <noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
    <a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>
<?	    }
    }
} else { // Navigation: ?>
	<span class="graytitle">Navigation</span>
	<ul class="pageitem">
		<li class="menu">
			<a href="index.php?news">
				<span class="name">Blog</span>
				<span class="arrow"></span>
			</a>
		</li>
	<? printPage(0, 0); ?>
	</ul>
	<span class="graytitle">Links</span>
	<ul class="pageitem">
<?
		$sql = 'SELECT url, title, nofollow FROM cms_links ORDER BY ord ASC';
		$result = mysql_query($sql);
		if(!$result) {
			die("Database error...");
		}
		while ($row = mysql_fetch_array($result)) {
?>		<li class="menu">
			<a class="noeffect" href="<? echo $row['url']; ?>"<? if ($row['nofollow'] == 1) { echo " rel=\"nofollow\""; } ?>>
				<span class="name"><? echo $row['title']; ?></span>
				<span class="arrow"></span>
			</a>
		</li>
<?		}
?>	</ul>
	<? if (isset($xyCMS_logo)) { ?>
	<span class="graytitle">Logo</span>
	<ul class="pageitem">
		<li class="textbox">
			<img src="../<? echo $xyCMS_logo; ?>" alt="Logo">
		</li>
	</ul>
<?	}
} ?>
</div>
<div id="footer">
	<a class="noeffect" href="../admin.php">Admin Area</a><br>
	<a class="noeffect" href="http://snippetspace.com">iPowered by iWebKit</a><br>
	<a href="#"><? include("../count.php"); ?> visitors / pageviews today.</a>
</div>
<?
$sql = 'SELECT
	inhalt
FROM
	cms_code
ORDER BY
	id ASC';
$result = mysql_query($sql);
if (!$result) {
	die ("Error");
}
while ($row = mysql_fetch_array($result)) {
	echo stripslashes($row['inhalt'])."\n";
}
?>
</body>
<? mysql_close(); ?>
</html>
