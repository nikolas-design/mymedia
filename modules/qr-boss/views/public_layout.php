<!doctype html>
<html lang="el">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="theme-color" content="<?= e($color) ?>">
<title><?= e($profile['title'] ?? 'Μενού') ?></title>
<link rel="stylesheet" href="<?= e(url('modules/qr-boss/assets/menu.css')) ?>?v=<?= (int) @filemtime(__DIR__ . '/../assets/menu.css') ?>">
<style>:root{--brand:<?= e($color) ?>}</style>
</head>
<body>
<?php require __DIR__ . '/' . $view . '.php'; ?>
</body>
</html>
