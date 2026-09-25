<?php
qr_require_edit();
$isNew = $params[0] === 'new';
$item = $isNew ? null : q1('SELECT * FROM qr_items WHERE id = ? AND business_id = ?', [(int) $params[0], $bid]);
if (!$isNew && !$item) {
    not_found();
}
$categories = qall('SELECT * FROM qr_categories WHERE business_id = ? ORDER BY sort, id', [$bid]);
if (!$categories) {
    flash('Φτιάξε πρώτα μια κατηγορία.', 'info');
    redirect('t/qr-boss/menu');
}

if (is_post()) {
    if (!$isNew && input('action') === 'delete') {
        delete_upload($item['photo']);
        q('DELETE FROM qr_items WHERE id = ?', [$item['id']]);
        flash('Το πιάτο διαγράφηκε.', 'info');
        redirect('t/qr-boss/menu');
    }
    $catId = input_int('category_id');
    if (!in_array($catId, array_map('intval', array_column($categories, 'id')), true)) {
        $catId = (int) $categories[0]['id'];
    }
    $name = mb_substr(input('name'), 0, 120);
    if ($name === '') {
        flash('Γράψε όνομα.', 'error');
        redirect('t/qr-boss/menu/item/' . ($isNew ? 'new?cat=' . $catId : $item['id']));
    }
    $price = input('price') === '' ? null : parse_money(input('price'));
    $tags = implode(',', array_intersect(array_keys(QR_TAGS), (array) ($_POST['tags'] ?? [])));

    $photo = $item['photo'] ?? null;
    try {
        $new = store_image('photo', 'qr-boss/' . $bid, 1000);
    } catch (RuntimeException $e) {
        flash($e->getMessage(), 'error');
        redirect('t/qr-boss/menu/item/' . ($isNew ? 'new?cat=' . $catId : $item['id']));
    }
    if ($new || input('remove_photo') === '1') {
        delete_upload($photo);
        $photo = $new;
    }

    $data = [$catId, $name, mb_substr(input('description'), 0, 400) ?: null, $price, $photo, $tags ?: null, input('available') === '1' ? 1 : 0];
    if ($isNew) {
        $sort = (int) qval('SELECT COALESCE(MAX(sort), 0) + 10 FROM qr_items WHERE category_id = ?', [$catId]);
        q('INSERT INTO qr_items (category_id, name, description, price_cents, photo, tags, available, business_id, sort) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [...$data, $bid, $sort]);
        flash('Το «' . $name . '» προστέθηκε.');
        redirect(input('again') === '1' ? 't/qr-boss/menu/item/new?cat=' . $catId : 't/qr-boss/menu');
    }
    q('UPDATE qr_items SET category_id = ?, name = ?, description = ?, price_cents = ?, photo = ?, tags = ?, available = ? WHERE id = ?',
        [...$data, $item['id']]);
    flash('Αποθηκεύτηκε.');
    redirect('t/qr-boss/menu#i' . $item['id']);
}

$selectedCat = (int) ($item['category_id'] ?? ($_GET['cat'] ?? 0));
qr_page('item', compact('item', 'categories', 'selectedCat', 'isNew'), $isNew ? 'Νέο πιάτο' : $item['name'], 'menu');
