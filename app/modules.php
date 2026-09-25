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
