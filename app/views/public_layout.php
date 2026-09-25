<!doctype html>
<html lang="el">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="theme-color" content="<?= e($color) ?>">
<meta name="robots" content="noindex">
<title><?= e($title) ?></title>
<link rel="stylesheet" href="<?= e(asset('public.css')) ?>">
<style>:root{--brand:<?= e($color) ?>}</style>
</head>
<body>
<main class="pub">
  <?php if ($logo): ?><img class="plogo" src="<?= e(upload_url($logo)) ?>" alt=""><?php endif; ?>
  <?php require $file; ?>
  <p class="pby">Με τη MyMedia</p>
</main>
</body>
</html>
