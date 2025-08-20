<?php
// includes/header.php
if (!isset($pageTitle)) $pageTitle = "QMS";
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?= esc($pageTitle) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/app.css">
</head>

<body class="bg-light">