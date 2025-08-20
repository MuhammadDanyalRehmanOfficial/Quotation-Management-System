<?php
session_start();
require __DIR__ . '/../config/db.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?? [];

if (!isset($_SESSION['user'])) {
    http_response_code(204);
    exit;
}

if (($data['action'] ?? '') !== 'autosave') {
    http_response_code(204);
    exit;
}

$qid = (int)($data['qid'] ?? 0);
$items = $data['items'] ?? [];

$upd = $mysqli->prepare("UPDATE quotation_items SET qty=?, price=?, gst_percent=?, total=? WHERE id=?");

foreach ($items as $row) {
    $qty = (float)$row['qty'];
    $price = (float)$row['price'];
    $gst = (float)$row['gst'];
    $total = $qty * $price;
    $id = (int)$row['id'];
    $upd->bind_param('dddsi', $qty, $price, $gst, $total, $id);
    $upd->execute();
}

$mysqli->query("UPDATE quotations SET last_autosave=NOW() WHERE id=$qid");

http_response_code(204);
