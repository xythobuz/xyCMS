<?
session_start();
$hostname = $_SERVER['HTTP_HOST'];
$path = dirname($_SERVER['PHP_SELF']);
include('config.php');
if (!isset($_SESSION['angemeldet']) || !$_SESSION['angemeldet']) {
    header('Location: '.$xyCMS_root.'/pwd.php');
    exit;
}
?>
