<?php
session_start();
require __DIR__ . '/../config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['ok' => false, 'error' => 'auth']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?? [];

$action = $data['action'] ?? '';

if ($action === 'add_by_barcode') {
    $qid = (int)$data['qid'];
    $barcode = $data['barcode'] ?? '';

    $stmt = $mysqli->prepare("SELECT id,name,sale_price,gst_percent,barcode FROM items WHERE barcode=? LIMIT 1");
    $stmt->bind_param('s', $barcode);
    $stmt->execute();
    $it = $stmt->get_result()->fetch_assoc();
    if (!$it) {
        echo json_encode(['ok' => false, 'error' => 'Item not found']);
        exit;
    }

    $price = (float)$it['sale_price'];
    $gst = (float)$it['gst_percent'];
    $name = $it['name'];
    $qty = 1;
    $total = $price * $qty;

    $ins = $mysqli->prepare("INSERT INTO quotation_items(quotation_id,item_id,name_snapshot,price,qty,gst_percent,total) VALUES(?,?,?,?,?,?,?)");
    $ins->bind_param('iissddd', $qid, $it['id'], $name, $price, $qty, $gst, $total);
    $ins->execute();
    $newId = $ins->insert_id;

    echo json_encode([
        'ok' => true,
        'item' => [
            'id' => $newId,
            'name' => $name,
            'barcode' => $it['barcode'],
            'qty' => $qty,
            'price' => $price,
            'gst' => $gst,
            'total' => $total
        ]
    ]);
    exit;
}


if ($action === 'delete_item') {
    $id = (int)($data['id'] ?? 0);
    if ($id > 0) {
        $del = $mysqli->prepare("DELETE FROM quotation_items WHERE id=?");
        $del->bind_param('i', $id);
        $del->execute();
        echo json_encode(['ok' => true]);
        exit;
    }
    echo json_encode(['ok' => false, 'error' => 'Invalid ID']);
    exit;
}

if ($action === 'autosave') {
    $qid = (int)($data['qid'] ?? 0);
    $items = $data['items'] ?? [];

    if ($qid <= 0 || !is_array($items)) {
        echo json_encode(['ok' => false, 'error' => 'Invalid input']);
        exit;
    }

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

    echo json_encode(['ok' => true]);
    exit;
}

echo json_encode(['ok' => false, 'error' => 'unknown_action']);
