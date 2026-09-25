<?php
declare(strict_types=1);

/**
 * Κάθε εργαλείο ζει στον φάκελο modules/<slug>/ (slug = tools.slug).
 * Δες modules/README.md για τη σύμβαση.
 */

function module_dir(string $slug): string
{
    return APP_ROOT . '/modules/' . $slug;
}

/** Υπάρχει ήδη υλοποίηση για το εργαλείο; */
function module_exists(string $slug): bool
{
    return preg_match('/^[a-z0-9-]+$/', $slug) === 1 && is_file(module_dir($slug) . '/index.php');
}

/** Ενεργή συνδρομή της επιχείρησης στο εργαλείο (ή null) */
function active_subscription(int $businessId, int $toolId): ?array
{
    return q1(
        "SELECT * FROM subscriptions WHERE business_id = ? AND tool_id = ? AND status = 'active' ORDER BY id DESC LIMIT 1",
        [$businessId, $toolId]
    );
}

/** URL μέσα σε ένα εργαλείο, π.χ. module_url('qr-boss', 'codes/5') */
function module_url(string $slug, string $path = ''): string
{
    return url('t/' . $slug . ($path !== '' ? '/' . ltrim($path, '/') : ''));
}

/**
 * Άνοιγμα εργαλείου: /t/<slug>/<υπόλοιπη διαδρομή>
 * Ελέγχει σύνδεση, επιχείρηση και ενεργή συνδρομή, και μετά φορτώνει το module.
 */
function dispatch_module(string $slug, string $subpath): never
{
    $user = require_login();
    $business = require_business();
    $tool = q1('SELECT * FROM tools WHERE slug = ?', [$slug]);
    if (!$tool) {
        not_found();
    }
    $subscription = active_subscription((int) $business['id'], (int) $tool['id']);
    if (!$subscription) {
        flash('Το ' . $tool['name'] . ' δεν είναι ενεργό για την επιχείρησή σου.', 'error');
        redirect('tools/' . $slug);
    }
    if (!module_exists($slug)) {
        render('module_pending', ['tool' => $tool], ['title' => $tool['name'], 'nav' => 'tools']);
    }

    // Διαθέσιμα στο module: $user, $business, $tool, $subscription, $subpath
    $subpath = trim($subpath, '/');
    require module_dir($slug) . '/index.php';
    exit;
}

/**
 * Εμφάνιση σελίδας εργαλείου μέσα στο layout της πύλης.
 * $view σχετικό με modules/<slug>/views/, π.χ. module_render('qr-boss', 'codes', [...])
 */
function module_render(string $slug, string $view, array $data = [], array $opts = []): never
{
    render('../../modules/' . $slug . '/views/' . $view, $data, $opts + ['nav' => 'tools']);
}

/**
 * Δημόσιες σελίδες εργαλείου (χωρίς login), π.χ. το μενού που ανοίγει με το QR.
 * Φορτώνει modules/<slug>/public.php με $subpath. Το module ελέγχει μόνο του
 * σε ποια επιχείρηση ανήκει το αίτημα και αν η συνδρομή είναι ενεργή.
 */
function dispatch_public(string $slug, string $subpath): never
{
    if (!preg_match('/^[a-z0-9-]+$/', $slug) || !is_file(module_dir($slug) . '/public.php')) {
        not_found();
    }
    $subpath = trim($subpath, '/');
    require module_dir($slug) . '/public.php';
    exit;
}

/* ---------- Ανέβασμα εικόνων ---------- */

/**
 * Αποθηκεύει εικόνα από $_FILES[$field] στο uploads/<φάκελος>/ και επιστρέφει
 * τη σχετική διαδρομή (π.χ. "qr-boss/12/ab12cd.jpg"), ή null αν δεν στάλθηκε αρχείο.
 * Δέχεται JPG/PNG/WebP έως 8MB. Αν υπάρχει GD, μικραίνει στα $maxSize px.
 */
function store_image(string $field, string $folder, int $maxSize = 1200): ?string
{
    $f = $_FILES[$field] ?? null;
    if (!$f || ($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($f['error'] !== UPLOAD_ERR_OK || $f['size'] > 8 * 1024 * 1024) {
        throw new RuntimeException('Η φωτογραφία δεν ανέβηκε. Δοκίμασε μικρότερο αρχείο (έως 8MB).');
    }
    $info = @getimagesize($f['tmp_name']);
    $types = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
    if (!$info || !isset($types[$info[2]])) {
        throw new RuntimeException('Δεκτές μόνο φωτογραφίες JPG, PNG ή WebP.');
    }
    $dir = APP_ROOT . '/uploads/' . trim($folder, '/');
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        throw new RuntimeException('Δεν μπορώ να γράψω στον φάκελο uploads.');
    }
    $ext = $types[$info[2]];
    $name = bin2hex(random_bytes(8));

    // Νέα κωδικοποίηση με GD: μικρότερο αρχείο και καθαρό από ό,τι άλλο κρύβεται μέσα
    if (function_exists('imagecreatetruecolor')) {
        $src = match ($info[2]) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($f['tmp_name']),
            IMAGETYPE_PNG  => @imagecreatefrompng($f['tmp_name']),
            IMAGETYPE_WEBP => @imagecreatefromwebp($f['tmp_name']),
        };
        if ($src) {
            if ($info[2] === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
                $o = (int) (@exif_read_data($f['tmp_name'])['Orientation'] ?? 1);
                $src = match ($o) { 3 => imagerotate($src, 180, 0), 6 => imagerotate($src, -90, 0), 8 => imagerotate($src, 90, 0), default => $src };
            }
            [$w, $h] = [imagesx($src), imagesy($src)];
            $scale = min(1, $maxSize / max($w, $h));
            $nw = max(1, (int) round($w * $scale));
            $nh = max(1, (int) round($h * $scale));
            $dst = imagecreatetruecolor($nw, $nh);
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
            $ext = $info[2] === IMAGETYPE_PNG ? 'png' : 'jpg';
            $path = "$dir/$name.$ext";
            $ok = $ext === 'png' ? imagepng($dst, $path, 6) : imagejpeg($dst, $path, 82);
            if ($ok) {
                return trim($folder, '/') . "/$name.$ext";
            }
        }
    }
    if (!move_uploaded_file($f['tmp_name'], "$dir/$name.$ext")) {
        throw new RuntimeException('Η φωτογραφία δεν αποθηκεύτηκε.');
    }
    return trim($folder, '/') . "/$name.$ext";
}

function upload_url(?string $rel): string
{
    return $rel ? url('uploads/' . $rel) : '';
}

function delete_upload(?string $rel): void
{
    if ($rel && !str_contains($rel, '..')) {
        @unlink(APP_ROOT . '/uploads/' . $rel);
    }
}
