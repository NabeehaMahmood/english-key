<?php
/**
 * Custom line-icon set used in place of emoji throughout the site.
 * Icons inherit color via currentColor by default, or can use the shared
 * navy->orange gradient (#ekaGrad, defined once in includes/header.php)
 * when $gradient is true. Each icon remembers its own viewBox/stroke-width
 * because the approved designs draw icons on different grids (16/20/24)
 * with different stroke weights - icon() reproduces them exactly rather
 * than forcing one shared grid.
 */
function icon(string $name, string $class = 'icon', bool $gradient = false): string
{
    // Most icons share a 20x20 viewBox; a few (added to match specific
    // reference SVGs pixel-for-pixel) use their own viewBox instead.
    $defs = [
        'chat' => ['<path d="M3 4.5A1.5 1.5 0 0 1 4.5 3h11A1.5 1.5 0 0 1 17 4.5v7A1.5 1.5 0 0 1 15.5 13H9l-3.5 3v-3H4.5A1.5 1.5 0 0 1 3 11.5v-7z"/>'],
        'mail' => ['<path d="M3 5.5A1.5 1.5 0 0 1 4.5 4h11A1.5 1.5 0 0 1 17 5.5v9a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 3 14.5v-9z"/><path d="M4 5.5l6 5 6-5"/>'],
        'book' => ['<path d="M4 4.5c1.8-.7 4-.7 6 0 2-.7 4.2-.7 6 0v11c-1.8-.7-4-.7-6 0-2-.7-4.2-.7-6 0v-11z"/><path d="M10 4.5v11"/>'],
        'document' => ['<path d="M6 3h6l3 3v10.5A.5.5 0 0 1 14.5 17h-8a.5.5 0 0 1-.5-.5v-13A.5.5 0 0 1 6 3z"/><path d="M12 3v3h3"/><path d="M7.5 10h5M7.5 12.5h5"/>'],
        'cap' => ['<path d="M2.5 8L10 4.5 17.5 8 10 11.5 2.5 8z"/><path d="M5.5 9.5v3c0 1 2 2 4.5 2s4.5-1 4.5-2v-3"/>'],
        'target' => ['<circle cx="10" cy="10" r="6.5"/><circle cx="10" cy="10" r="3.2"/><circle cx="10" cy="10" r=".6" fill="currentColor" stroke="none"/>'],
        'people' => ['<circle cx="7" cy="7.5" r="2.2"/><path d="M2.8 16c0-2.4 1.9-4 4.2-4s4.2 1.6 4.2 4"/><circle cx="14" cy="8" r="1.8"/><path d="M12.7 12.4c1.9.3 3.3 1.7 3.3 3.6"/>'],
        'calendar' => ['<rect x="3" y="4.2" width="14" height="12" rx="1.3"/><path d="M3 8h14M7 2.5v3M13 2.5v3"/>'],
        'card' => ['<rect x="2.5" y="5" width="15" height="10" rx="1.3"/><path d="M2.5 8.3h15M5.5 12h3"/>'],
        'ticket' => ['<path d="M3 8.2c1 0 1.8-.8 1.8-1.8S4 4.6 3 4.6V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v.6c-1 0-1.8.8-1.8 1.8s.8 1.8 1.8 1.8v6.6a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V8.2z"/><path d="M12 3.5v13"/>'],
        'star' => ['<path d="M10 2.7l2.2 4.5 5 .7-3.6 3.5.9 5-4.5-2.4-4.5 2.4.9-5-3.6-3.5 5-.7 2.2-4.5z" fill="currentColor" stroke="none"/>'],
        'whatsapp' => ['<path d="M16 3C9.4 3 4 8.4 4 15c0 2.6.8 5 2.3 7L4 29l7.2-2.2c1.9 1 4 1.6 6.1 1.6h.7c6.6 0 12-5.4 12-12S22.6 3 16 3zm6.6 17c-.3.8-1.6 1.5-2.3 1.6-.6.1-1.3.2-2.1-.1-.5-.2-1.1-.4-1.9-.7-3.4-1.5-5.6-4.9-5.8-5.1-.2-.2-1.4-1.8-1.4-3.5s.9-2.5 1.2-2.8c.3-.3.7-.4.9-.4h.6c.2 0 .5-.1.7.5.3.7.9 2.3 1 2.5.1.2.1.4 0 .6-.1.2-.2.4-.4.6-.2.2-.4.5-.5.6-.2.2-.4.4-.2.7.2.4.9 1.5 2 2.4 1.3 1.2 2.5 1.6 2.8 1.7.4.2.6.1.8-.1.2-.2.9-1 1.1-1.4.2-.4.5-.3.8-.2.3.1 2 1 2.4 1.1.4.2.6.3.7.4.1.3.1.9-.2 1.6z" fill="currentColor" stroke="none"/>', '0 0 32 32'],
        'check' => ['<path d="M4 10.5l3.7 3.7L16 5.8"/>'],
        'plus' => ['<path d="M10 3.5v13M3.5 10h13"/>'],
        'person' => ['<circle cx="10" cy="6.5" r="3"/><path d="M4 17c0-3.3 2.7-5.5 6-5.5s6 2.2 6 5.5"/>'],
        'chevron-down' => ['<path d="M5 7.5l5 5 5-5"/>'],
        'trophy' => ['<path d="M6 3h8v5a4 4 0 0 1-4 4 4 4 0 0 1-4-4V3z"/><path d="M6 4H3.5A1.5 1.5 0 0 0 2 5.5C2 7.5 3.5 9 6 9M14 4h2.5A1.5 1.5 0 0 1 18 5.5C18 7.5 16.5 9 14 9"/><path d="M10 12v3M7 17.5h6M7.5 17.5c0-1.6.6-2.3 2.5-2.5 1.9.2 2.5.9 2.5 2.5"/>'],
        'medal' => ['<path d="M7 3h6l2 5-5 4-5-4 2-5z"/><circle cx="10" cy="13.5" r="3.5"/><path d="M8.5 13.2l1 1 2.2-2.2"/>'],
        'award' => ['<circle cx="10" cy="8" r="5"/><path d="M10 5.6l.9 1.9 2 .3-1.5 1.4.4 2-1.8-1-1.8 1 .4-2-1.5-1.4 2-.3.9-1.9z" fill="currentColor" stroke="none"/><path d="M7.2 12.3L6 18l4-2 4 2-1.2-5.7"/>'],
        // 24x24 icons matching the "Why EnglishKeys" reference SVGs exactly.
        'grad-cap' => ['<path d="M22 10 12 5 2 10l10 5 10-5z"/><path d="M6 12v5c0 1 3 3 6 3s6-2 6-3v-5"/>', '0 0 24 24'],
        'open-book' => ['<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>', '0 0 24 24'],
        'community' => ['<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>', '0 0 24 24'],
        // 24x24 icons matching the Courses-page programme-category (.cata-ico) reference SVGs exactly.
        'book-spine' => ['<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>', '0 0 24 24'],
        'check-circle' => ['<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>', '0 0 24 24'],
        'star-badge' => ['<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>', '0 0 24 24'],
        'lightning' => ['<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>', '0 0 24 24'],
        'eye' => ['<path d="M1.5 10S4.5 4 10 4s8.5 6 8.5 6-3 6-8.5 6-8.5-6-8.5-6z"/><circle cx="10" cy="10" r="2.6"/>'],
        'folder' => ['<path d="M3.5 15.7A2 2 0 0 0 5.5 17.5H16"/><path d="M5.5 2.5H16v15H5.5A2 2 0 0 1 3.5 15.5V4.5a2 2 0 0 1 2-2z"/>'],
        'bolt' => ['<polygon points="10.8 1.7 2.5 11 9.5 11 8.7 18.3 17 9 10 9 10.8 1.7"/>'],
        'phone' => ['<path d="M17 13.4v2.4a1.6 1.6 0 0 1-1.75 1.6 15.8 15.8 0 0 1-6.9-2.45 15.6 15.6 0 0 1-4.8-4.8A15.8 15.8 0 0 1 1.1 3.25 1.6 1.6 0 0 1 2.7 1.5h2.4a1.6 1.6 0 0 1 1.6 1.38c.1.77.29 1.52.56 2.25a1.6 1.6 0 0 1-.36 1.68L5.87 7.9a12.8 12.8 0 0 0 4.8 4.8l1.09-1.03a1.6 1.6 0 0 1 1.68-.36c.72.27 1.48.46 2.25.56A1.6 1.6 0 0 1 17 13.4z"/>'],
        'list' => ['<path d="M3 3h10M3 8h10M3 13h6"/>', '0 0 16 16'],
        'compass' => ['<circle cx="12" cy="12" r="4.2"/><path d="M12 2.5v3M12 18.5v3M4.6 4.6l2.1 2.1M17.3 17.3l2.1 2.1M2.5 12h3M18.5 12h3M4.6 19.4l2.1-2.1M17.3 6.7l2.1-2.1"/>', '0 0 24 24'],
        'book-open' => ['<path d="M12 6.5C10.4 5.1 8 4.5 5.6 4.8 4.7 4.9 4 5.7 4 6.6v11c0 1 .9 1.8 1.9 1.6 2.1-.4 4.4.1 6.1 1.4 1.7-1.3 4-1.8 6.1-1.4 1-.2 1.9-.6 1.9-1.6v-11c0-.9-.7-1.7-1.6-1.8C16 4.5 13.6 5.1 12 6.5z"/><path d="M12 6.5v14"/>', '0 0 24 24'],
        'bookmark' => ['<path d="M6 21V4"/><path d="M6 4.5c1.6-1.1 3.2-1.1 4.8 0 1.7 1.1 3.5 1.1 5.2 0v9c-1.7 1.1-3.5 1.1-5.2 0-1.6-1.1-3.2-1.1-4.8 0z"/>', '0 0 24 24'],
        'chevron-sm' => ['<path d="M3 6l5 5 5-5"/>', '0 0 16 16'],
        'meta-calendar' => ['<rect x="2" y="3" width="12" height="11" rx="2"/><path d="M2 6h12M5 1v3M11 1v3"/>', '0 0 16 16'],
        'meta-person' => ['<circle cx="8" cy="5" r="3"/><path d="M2 14c1-3 3.5-4 6-4s5 1 6 4"/>', '0 0 16 16'],
        'meta-mode' => ['<path d="M8 3c-2-1.5-5-1.5-6 0v10c1-1.5 4-1.5 6 0 2-1.5 5-1.5 6 0V3c-1-1.5-4-1.5-6 0v10"/>', '0 0 16 16'],
        'meta-price' => ['<rect x="1" y="4" width="14" height="9" rx="2"/><path d="M1 7h14"/>', '0 0 16 16'],
        'meta-seats' => ['<path d="M2 6a2 2 0 0 0 2-2h8a2 2 0 0 0 2 2v4a2 2 0 0 0-2 2H4a2 2 0 0 0-2-2V6z"/><path d="M8 4v8" stroke-dasharray="2 2"/>', '0 0 16 16'],
        // Fill not stroke -- svg-level fill="none" doesn't matter since the
        // path sets its own fill/stroke explicitly.
        'star-sm' => ['<path d="M8 1l2.2 4.5 4.8.7-3.5 3.4.8 4.9L8 12.2 3.7 14.5l.8-4.9L1 6.2l4.8-.7L8 1z" fill="currentColor" stroke="none"/>', '0 0 16 16'],
        'refresh' => ['<path d="M16.5 10a6.5 6.5 0 1 1-2.3-4.95"/><path d="M17 3.5v4h-4"/>'],
    ];

    $def = $defs[$name] ?? [];
    $inner = $def[0] ?? '';
    $viewBox = $def[1] ?? '0 0 20 20';
    $stroke = $gradient ? 'url(#ekaGrad)' : 'currentColor';
    return '<svg class="' . htmlspecialchars($class, ENT_QUOTES) . '" viewBox="' . $viewBox . '" fill="none" stroke="' . $stroke . '" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $inner . '</svg>';
}

function starRow(int $count = 5, string $class = 'icon star-icon'): string
{
    return str_repeat(icon('star', $class), max(0, $count));
}
