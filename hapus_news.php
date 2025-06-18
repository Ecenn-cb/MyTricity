<?php
session_start();
include 'koneksiDB.php';

if ($_SESSION['role'] !== 'admin') {
    die("Akses ditolak!");
}

$id = $_GET['id'];
$conn->query("DELETE FROM news WHERE id_news = $id");

header("Location: adminNews.php");
exit;
