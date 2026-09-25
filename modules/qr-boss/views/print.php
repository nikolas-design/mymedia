<!doctype html>
<html lang="el"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Εκτύπωση QR · <?= e($profile['title']) ?></title>
<link rel="stylesheet" href="<?= e(asset('app.css')) ?>">
<style>
  body{background:#e9e9ef}
  .bar{display:flex;gap:10px;align-items:center;justify-content:center;padding:14px;flex-wrap:wrap}
  .sheet{width:210mm;margin:0 auto 20px;background:#fff;padding:10mm;display:grid;grid-template-columns:1fr 1fr;gap:6mm}
  .tent{border:1px dashed #cfd0d8;border-radius:4mm;padding:8mm 6mm;text-align:center;break-inside:avoid;page-break-inside:avoid}
  .tent h2{margin:0 0 1mm;font-size:18pt;letter-spacing:-.01em;color:<?= e($profile['color']) ?>}
  .tent .sub{font-size:10pt;color:#5e636e;margin-bottom:4mm}
  .tent .code{width:55mm;margin:0 auto;line-height:0}
  .tent .code svg{width:100%;height:auto}
  .tent .cta{font-size:12pt;font-weight:600;margin-top:4mm}
  .tent .tlabel{display:inline-block;margin-top:3mm;font-size:11pt;font-weight:600;padding:1.5mm 4mm;border-radius:99px;background:<?= e($profile['color']) ?>;color:#fff}
  .tent img{max-height:14mm;max-width:40mm;margin-bottom:2mm}
  @media print{body{background:#fff}.bar{display:none}.sheet{margin:0;padding:0;width:auto}@page{size:A4;margin:10mm}}
  @media screen and (max-width:820px){.sheet{width:auto;grid-template-columns:1fr}}
</style></head>
<body>
<div class="bar"><button class="btn dark auto" onclick="window.print()"><?= icon('print', 16) ?> Εκτύπωση</button><span class="small muted"><?= count($codes) ?> QR · Χαρτί Α4, κόψε στις διακεκομμένες γραμμές</span></div>
<div class="sheet">
<?php foreach ($codes as $c): ?>
  <div class="tent">
    <?php if ($profile['logo']): ?><img src="<?= e(upload_url($profile['logo'])) ?>" alt=""><br><?php endif; ?>
    <h2><?= e($profile['title']) ?></h2>
    <div class="sub"><?= e($profile['subtitle'] ?: '') ?></div>
    <div class="code" data-qr="<?= e(qr_public_url($c['code'])) ?>" data-qr-size="300"></div>
    <div class="cta"><?= e(match ($c['type']) {
        'wifi' => 'Σκανάρετε για Wi-Fi',
        'review' => 'Πείτε μας τη γνώμη σας',
        'link' => 'Σκανάρετε με την κάμερα',
        default => 'Σκανάρετε για το μενού',
    }) ?></div>
    <?php if ($c['table_label']): ?><div class="tlabel"><?= e($c['table_label']) ?></div><?php endif; ?>
  </div>
<?php endforeach; ?>
</div>
<script src="<?= e(asset('vendor/qrcode.js')) ?>"></script>
<script src="<?= e(asset('qr.js')) ?>"></script>
</body></html>
