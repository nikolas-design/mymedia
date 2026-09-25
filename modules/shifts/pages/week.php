<?php
$monday = sh_monday(preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) ($_GET['w'] ?? '')) ? $_GET['w'] : date('Y-m-d'));
$here = 't/shifts?w=' . $monday;

if (is_post()) {
    module_require_edit();
    $action = input('action');
    if ($action === 'add') {
        $person = q1('SELECT * FROM sh_people WHERE id = ? AND business_id = ?', [input_int('person_id'), $bid]);
        $days = array_filter((array) ($_POST['days'] ?? []), fn($d) => preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $d));
        $start = preg_match('/^\d{2}:\d{2}$/', input('start')) ? input('start') : null;
        $end = preg_match('/^\d{2}:\d{2}$/', input('end')) ? input('end') : null;
        if (!$person || !$days || !$start || !$end) {
            flash('Διάλεξε άτομο, ημέρες και ώρες.', 'error');
            redirect($here);
        }
        foreach ($days as $d) {
            q('INSERT INTO sh_shifts (business_id, person_id, day, start_time, end_time, position) VALUES (?, ?, ?, ?, ?, ?)',
                [$bid, $person['id'], $d, $start, $end, mb_substr(input('position'), 0, 60) ?: $person['position']]);
        }
        flash('Προστέθηκαν ' . count($days) . ' βάρδιες για ' . $person['name'] . '.');
    } elseif ($action === 'delete') {
        q('DELETE FROM sh_shifts WHERE id = ? AND business_id = ?', [input_int('id'), $bid]);
    } elseif ($action === 'copy') {
        // Αντιγραφή της προηγούμενης εβδομάδας (μόνο αν η τρέχουσα είναι άδεια)
        if (qval('SELECT 1 FROM sh_shifts WHERE business_id = ? AND day >= ? AND day <= ?', [$bid, $monday, date('Y-m-d', strtotime("$monday +6 days"))])) {
            flash('Η εβδομάδα έχει ήδη βάρδιες. Σβήσε τες πρώτα αν θέλεις αντιγραφή.', 'error');
            redirect($here);
        }
        $prev = date('Y-m-d', strtotime("$monday -7 days"));
        $n = 0;
        foreach (qall('SELECT s.* FROM sh_shifts s JOIN sh_people p ON p.id = s.person_id WHERE s.business_id = ? AND s.day >= ? AND s.day < ? AND p.active = 1', [$bid, $prev, $monday]) as $s) {
            q('INSERT INTO sh_shifts (business_id, person_id, day, start_time, end_time, position, note) VALUES (?, ?, ?, ?, ?, ?, ?)',
                [$bid, $s['person_id'], date('Y-m-d', strtotime($s['day'] . ' +7 days')), $s['start_time'], $s['end_time'], $s['position'], $s['note']]);
            $n++;
        }
        flash($n ? "Αντιγράφηκαν $n βάρδιες από την προηγούμενη εβδομάδα." : 'Η προηγούμενη εβδομάδα δεν είχε βάρδιες.', $n ? 'ok' : 'info');
    } elseif ($action === 'clear') {
        q('DELETE FROM sh_shifts WHERE business_id = ? AND day >= ? AND day <= ?', [$bid, $monday, date('Y-m-d', strtotime("$monday +6 days"))]);
        flash('Η εβδομάδα άδειασε.', 'info');
    } elseif ($action === 'publish') {
        $sunday = date('Y-m-d', strtotime("$monday +6 days"));
        $s = sh_settings($bid);
        if (!$s['published_until'] || $s['published_until'] < $sunday) {
            q('UPDATE sh_settings SET published_until = ? WHERE business_id = ?', [$sunday, $bid]);
        }
        notify($bid, 'Δημοσιεύτηκε το πρόγραμμα ' . date_gr($monday, false) . ' – ' . date_gr($sunday, false), 't/shifts/mine?w=' . $monday);
        // Email σε όσους έχουν email και βάρδιες την εβδομάδα
        $link = full_url('p/shifts/' . $s['share_token'] . '?w=' . $monday);
        $sent = 0;
        foreach (qall('SELECT DISTINCT p.* FROM sh_people p JOIN sh_shifts s ON s.person_id = p.id WHERE p.business_id = ? AND s.day >= ? AND s.day <= ? AND p.email IS NOT NULL',
            [$bid, $monday, $sunday]) as $p) {
            $lines = '';
            foreach (qall('SELECT * FROM sh_shifts WHERE person_id = ? AND day >= ? AND day <= ? ORDER BY day, start_time', [$p['id'], $monday, $sunday]) as $sh) {
                $lines .= SH_DAYS[(int) date('N', strtotime($sh['day'])) - 1] . ' ' . date_gr($sh['day'], false) . ': ' . sh_time($sh['start_time']) . '–' . sh_time($sh['end_time']) . "\n";
            }
            $sent += (int) send_mail($p['email'], 'Οι βάρδιες σου ' . date_gr($monday, false) . ' – ' . date_gr($sunday, false),
                "Γεια σου {$p['name']},\n\nΟι βάρδιες σου στο {$business['name']}:\n\n$lines\nΌλο το πρόγραμμα: $link\n");
        }
        flash('Το πρόγραμμα δημοσιεύτηκε' . ($sent ? " και στάλθηκε σε $sent άτομα." : '.'));
    }
    redirect($here);
}

$people = qall('SELECT * FROM sh_people WHERE business_id = ? AND active = 1 ORDER BY name', [$bid]);
$grid = sh_week($bid, $monday);
$abs = sh_absences($bid, $monday);
$settings = sh_settings($bid);
$published = $settings['published_until'] && $settings['published_until'] >= date('Y-m-d', strtotime("$monday +6 days"));
module_page('week', compact('monday', 'people', 'grid', 'abs', 'settings', 'published'), 'Πρόγραμμα', 'week');
