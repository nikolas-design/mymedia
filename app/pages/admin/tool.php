<?php
require_admin();
$tool = q1('SELECT * FROM tools WHERE id = ?', [(int) $params[0]]);
if (!$tool) {
    not_found();
}
$here = 'admin/tools/' . $tool['id'];

if (is_post()) {
    $action = input('action');
    if ($action === 'tool') {
        $status = in_array(input('status'), ['available', 'soon', 'hidden'], true) ? input('status') : $tool['status'];
        $color = array_key_exists(input('color'), ['purple'=>1,'teal'=>1,'orange'=>1,'indigo'=>1,'blue'=>1,'pink'=>1,'green'=>1,'grey'=>1]) ? input('color') : $tool['color'];
        $iconName = array_key_exists(input('icon'), ICONS) ? input('icon') : $tool['icon'];
        q('UPDATE tools SET name = ?, short = ?, tagline = ?, description = ?, audience = ?, features = ?, status = ?, sort = ?, color = ?, icon = ? WHERE id = ?', [
            input('name') ?: $tool['name'], input('short'), input('tagline'), input('description') ?: null,
            input('audience') ?: null, input('features') ?: null, $status, input_int('sort'), $color, $iconName, $tool['id'],
        ]);
        flash('Το εργαλείο αποθηκεύτηκε.');
    } elseif ($action === 'plan') {
        $id = input_int('id');
        $data = [
            input('name') ?: 'Standard', input('summary'), parse_money(input('price')),
            input('period') === 'year' ? 'year' : 'month', input('popular') === '1' ? 1 : 0,
            input('active') === '1' ? 1 : 0, input_int('sort'),
        ];
        if ($id) {
            q('UPDATE plans SET name = ?, summary = ?, price_cents = ?, period = ?, popular = ?, active = ?, sort = ? WHERE id = ? AND tool_id = ?',
                [...$data, $id, $tool['id']]);
        } else {
            q('INSERT INTO plans (name, summary, price_cents, period, popular, active, sort, tool_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                [...$data, $tool['id']]);
        }
        flash('Το πλάνο αποθηκεύτηκε. Οι υπάρχουσες συνδρομές κρατούν την τιμή τους.');
    }
    redirect($here);
}
$plans = qall('SELECT * FROM plans WHERE tool_id = ? ORDER BY sort, id', [$tool['id']]);
render('admin/tool', compact('tool', 'plans'), ['title' => $tool['name'], 'nav' => 'admin-tools', 'area' => 'admin']);
