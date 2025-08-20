<?php
// config/db.php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'qms';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
  die("DB connection failed: " . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');

function esc($str) {
  return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

session_start();
