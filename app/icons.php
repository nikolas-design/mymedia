<?php
declare(strict_types=1);

const ICONS = [
    'logo'     => '<path d="M16 18a2 2 0 0 1 2 2 2 2 0 0 1 2-2 2 2 0 0 1-2-2 2 2 0 0 1-2 2M16 4a2 2 0 0 1 2 2 2 2 0 0 1 2-2 2 2 0 0 1-2-2 2 2 0 0 1-2 2M9 18a6 6 0 0 1 6-6 6 6 0 0 1-6-6 6 6 0 0 1-6 6 6 6 0 0 1 6 6"/>',
    'menu'     => '<path d="M4 6h16M4 12h16M4 18h16"/>',
    'shield'   => '<path d="M12 3a12 12 0 0 0 8.5 3A12 12 0 0 1 12 21 12 12 0 0 1 3.5 6 12 12 0 0 0 12 3"/>',
    'bolt'     => '<path d="M13 3v7h6l-8 11v-7H5z"/>',
    'qr'       => '<rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><path d="M7 7v.01M17 7v.01M7 17v.01M14 14h3M20 14v3M14 20h3v-3M20 20v.01"/>',
    'wallet'   => '<path d="M17 8V5a1 1 0 0 0-1-1H6a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3M19 12h-2a2 2 0 0 0 0 4h2a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1M4 6v12a2 2 0 0 0 2 2h12a1 1 0 0 0 1-1v-2"/>',
    'star'     => '<path d="m12 17.8-6.2 3.3 1.2-6.9-5-4.9 6.9-1L12 2l3.1 6.3 6.9 1-5 4.9 1.2 6.9z"/>',
    'chart'    => '<path d="M4 20V10M10 20V4M16 20v-8M22 20H2"/>',
    'headset'  => '<path d="M4 14v-3a8 8 0 0 1 16 0v3"/><path d="M18 19a1 1 0 0 1-1 1h-3M4 14h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H4zM20 14h-2a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h2z"/>',
    'home'     => '<path d="M5 12H3l9-9 9 9h-2M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7"/><path d="M9 21v-6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v6"/>',
    'grid'     => '<rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><rect x="14" y="14" width="6" height="6" rx="1"/>',
    'users'    => '<circle cx="9" cy="8" r="3.5"/><path d="M3 20v-1a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v1M16 4.5a3.5 3.5 0 0 1 0 7M21 20v-1a4 4 0 0 0-3-3.9"/>',
    'receipt'  => '<path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16l-3-2-2 2-2-2-2 2-2-2zM9 7h6M9 11h6M13 15h2"/>',
    'user'     => '<circle cx="12" cy="8" r="4"/><path d="M4 21v-1a5 5 0 0 1 5-5h6a5 5 0 0 1 5 5v1"/>',
    'truck'    => '<circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/><path d="M5 17H3V6h11v11M9 17h6M14 9h4l3 4v4h-2"/>',
    'calendar' => '<path d="M10.5 21H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3M16 3v4M8 3v4M4 11h16"/><circle cx="18" cy="18" r="4"/><path d="M18 16.5V18l1 1"/>',
    'scissors' => '<circle cx="6" cy="7" r="3"/><circle cx="6" cy="17" r="3"/><path d="M8.6 8.6 20 18M8.6 15.4 20 6"/>',
    'utensils' => '<path d="M19 3v12h-5c-.5 0-1-.5-1-1V6c0-2 1.5-3 3-3zM19 15v6M8 3v18M5 3v5a3 3 0 0 0 6 0V3"/>',
    'building' => '<path d="M3 21h18M9 8h1M9 12h1M9 16h1M14 8h1M14 12h1M14 16h1M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"/>',
    'book'     => '<path d="M3 19a9 9 0 0 1 9 0 9 9 0 0 1 9 0M3 6a9 9 0 0 1 9 0 9 9 0 0 1 9 0M3 6v13M12 6v13M21 6v13"/>',
    'globe'    => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>',
    'chevron'  => '<path d="m9 6 6 6-6 6"/>',
    'back'     => '<path d="m15 6-6 6 6 6"/>',
    'mail'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
    'plus'     => '<path d="M12 5v14M5 12h14"/>',
    'play'     => '<path d="M7 4v16l13-8z"/>',
    'arrow'    => '<path d="M5 12h14M13 6l6 6-6 6"/>',
    'logout'   => '<path d="M14 8V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2v-2M9 12h12l-3-3M18 15l3-3"/>',
    'bell'     => '<path d="M10 5a2 2 0 1 1 4 0 7 7 0 0 1 4 6v3a4 4 0 0 0 2 3H4a4 4 0 0 0 2-3v-3a7 7 0 0 1 4-6M9 17v1a3 3 0 0 0 6 0v-1"/>',
    'settings' => '<path d="M10.3 4.3c.4-1.8 3-1.8 3.4 0a1.7 1.7 0 0 0 2.6 1.1c1.5-.9 3.3.8 2.4 2.4a1.7 1.7 0 0 0 1 2.5c1.8.5 1.8 3 0 3.5a1.7 1.7 0 0 0-1 2.5c.9 1.5-.9 3.3-2.4 2.4a1.7 1.7 0 0 0-2.6 1c-.4 1.9-3 1.9-3.4 0a1.7 1.7 0 0 0-2.6-1c-1.5.9-3.3-.9-2.4-2.4a1.7 1.7 0 0 0-1-2.5c-1.8-.5-1.8-3 0-3.5a1.7 1.7 0 0 0 1-2.5c-.9-1.6.9-3.3 2.4-2.4 1 .6 2.3.1 2.6-1.1"/><circle cx="12" cy="12" r="3"/>',
    'check'    => '<path d="m5 12 5 5L20 7"/>',
    'x'        => '<path d="M18 6 6 18M6 6l12 12"/>',
    'print'    => '<path d="M17 17h2a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h2M17 9V5a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v4"/><rect x="7" y="13" width="10" height="8" rx="2"/>',
    'swap'     => '<path d="M7 10h14l-4-4M17 14H3l4 4"/>',
];

function icon(string $name, int $size = 18): string
{
    $paths = ICONS[$name] ?? ICONS['grid'];
    return '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths . '</svg>';
}

/** Χρωματιστό τετράγωνο με εικονίδιο (όπως στο mockup) */
function tile(string $iconName, string $color, int $box = 40): string
{
    $colors = [
        'purple' => ['#f2edfd', '#793de7'],
        'teal'   => ['#e3f5f2', '#1d9e75'],
        'orange' => ['#fdf3e2', '#e79b23'],
        'indigo' => ['#e9ecfb', '#4c5fd8'],
        'blue'   => ['#e6f3fb', '#2f8fd6'],
        'pink'   => ['#fbe9ef', '#d64b7c'],
        'green'  => ['#e8f6ee', '#22a06b'],
        'grey'   => ['#f0f1f5', '#5e636e'],
    ];
    [$bg, $fg] = $colors[$color] ?? $colors['purple'];
    $inner = $box >= 44 ? 20 : ($box <= 28 ? 14 : 18);
    return '<span class="tile" style="width:' . $box . 'px;height:' . $box . 'px;background:' . $bg . ';color:' . $fg . '">'
        . icon($iconName, $inner) . '</span>';
}
