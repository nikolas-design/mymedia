<?php
$ids = array_map('intval', (array) ($_GET['ids'] ?? []));
$codes = $ids ? qall('SELECT * FROM qr_codes WHERE business_id = ? AND id IN (' . implode(',', array_fill(0, count($ids), '?')) . ')
                      ORDER BY table_label IS NULL, LENGTH(table_label), table_label, id', [$bid, ...$ids]) : [];
if (!$codes) {
    flash('Διάλεξε ποια QR θα τυπώσεις.', 'error');
    redirect('t/qr-boss/codes');
}
// Σελίδα χωρίς το layout της πύλης, έτοιμη για Α4
require __DIR__ . '/../views/print.php';
exit;
