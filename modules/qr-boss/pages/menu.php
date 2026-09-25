<?php
if (is_post()) {
    $action = input('action');

    // Διαθεσιμότητα πιάτου: μπορούν όλα τα μέλη (π.χ. «τελείωσε το μουσακά»)
    if ($action === 'toggle') {
        q('UPDATE qr_items SET available = 1 - available WHERE id = ? AND business_id = ?', [input_int('id'), $bid]);
        redirect('t/qr-boss/menu#i' . input_int('id'));
    }

    qr_require_edit();
    if ($action === 'sample' && !qval('SELECT 1 FROM qr_categories WHERE business_id = ?', [$bid])) {
        // Δείγμα μενού καφέ, για να δει κανείς αμέσως πώς φαίνεται
        $sample = [
            'Καφέδες' => [['Freddo Espresso', 'Διπλός espresso, χτυπημένος με πάγο', 350, 'top'], ['Freddo Cappuccino', 'Με αφρόγαλα', 400, ''], ['Espresso', 'Μονός ή διπλός', 250, ''], ['Cappuccino', 'Ζεστός, με αφρόγαλα', 380, '']],
            'Brunch'  => [['Pancakes', 'Με μέλι, φρούτα εποχής και γιαούρτι', 750, 'new,veg'], ['Αυγά Benedict', 'Με μπέικον, σάλτσα hollandaise σε brioche', 900, ''], ['Avocado toast', 'Προζυμένιο ψωμί, αβοκάντο, ντοματίνια', 800, 'vegan']],
            'Ποτά'    => [['Μπύρα draft 400ml', null, 550, ''], ['Aperol Spritz', 'Aperol, prosecco, σόδα', 900, 'top'], ['Λεμονάδα σπιτική', null, 400, 'vegan,gf']],
        ];
        $s = 0;
        foreach ($sample as $cat => $list) {
            q('INSERT INTO qr_categories (business_id, name, sort) VALUES (?, ?, ?)', [$bid, $cat, $s += 10]);
            $cid = (int) db()->lastInsertId();
            $i = 0;
            foreach ($list as [$n, $d, $p, $t]) {
                q('INSERT INTO qr_items (business_id, category_id, name, description, price_cents, tags, sort) VALUES (?, ?, ?, ?, ?, ?, ?)',
                    [$bid, $cid, $n, $d, $p, $t ?: null, $i += 10]);
            }
        }
        flash('Φορτώθηκε δείγμα μενού. Άλλαξε ή σβήσε ό,τι θέλεις.');
    } elseif ($action === 'category') {
        $name = mb_substr(input('name'), 0, 80);
        if ($name !== '') {
            $sort = (int) qval('SELECT COALESCE(MAX(sort), 0) + 10 FROM qr_categories WHERE business_id = ?', [$bid]);
            q('INSERT INTO qr_categories (business_id, name, sort) VALUES (?, ?, ?)', [$bid, $name, $sort]);
            flash('Η κατηγορία «' . $name . '» προστέθηκε.');
        }
    } elseif ($action === 'move') {
        // Μετακίνηση κατηγορίας ή πιάτου πάνω/κάτω
        $table = input('what') === 'item' ? 'qr_items' : 'qr_categories';
        $row = q1("SELECT * FROM $table WHERE id = ? AND business_id = ?", [input_int('id'), $bid]);
        if ($row) {
            $scope = $table === 'qr_items' ? ' AND category_id = ' . (int) $row['category_id'] : '';
            $up = input('dir') === 'up';
            $other = q1("SELECT * FROM $table WHERE business_id = ?$scope AND (sort, id) " . ($up ? '<' : '>') . ' (?, ?)
                         ORDER BY sort ' . ($up ? 'DESC' : 'ASC') . ', id ' . ($up ? 'DESC' : 'ASC') . ' LIMIT 1',
                [$bid, $row['sort'], $row['id']]);
            if ($other) {
                // Αν έχουν ίδια σειρά, ξαναμοιράζουμε αριθμούς πρώτα
                if ((int) $other['sort'] === (int) $row['sort']) {
                    $i = 0;
                    foreach (qall("SELECT id FROM $table WHERE business_id = ?$scope ORDER BY sort, id", [$bid]) as $r) {
                        q("UPDATE $table SET sort = ? WHERE id = ?", [$i += 10, $r['id']]);
                    }
                    $row = q1("SELECT * FROM $table WHERE id = ?", [$row['id']]);
                    $other = q1("SELECT * FROM $table WHERE id = ?", [$other['id']]);
                }
                q("UPDATE $table SET sort = ? WHERE id = ?", [$other['sort'], $row['id']]);
                q("UPDATE $table SET sort = ? WHERE id = ?", [$row['sort'], $other['id']]);
            }
        }
    }
    redirect('t/qr-boss/menu');
}

$categories = qall('SELECT * FROM qr_categories WHERE business_id = ? ORDER BY sort, id', [$bid]);
$items = [];
foreach (qall('SELECT * FROM qr_items WHERE business_id = ? ORDER BY sort, id', [$bid]) as $i) {
    $items[(int) $i['category_id']][] = $i;
}
qr_page('menu', compact('categories', 'items'), 'Μενού', 'menu');
