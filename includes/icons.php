<?php
/**
 * Minimal custom line-icon set (20x20, stroke-based) used in place of emoji
 * throughout the site. Icons inherit color via currentColor.
 */
function icon(string $name, string $class = 'icon'): string
{
    $paths = [
        'chat' => '<path d="M3 4.5A1.5 1.5 0 0 1 4.5 3h11A1.5 1.5 0 0 1 17 4.5v7A1.5 1.5 0 0 1 15.5 13H9l-3.5 3v-3H4.5A1.5 1.5 0 0 1 3 11.5v-7z"/>',
        'mail' => '<path d="M3 5.5A1.5 1.5 0 0 1 4.5 4h11A1.5 1.5 0 0 1 17 5.5v9a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 3 14.5v-9z"/><path d="M4 5.5l6 5 6-5"/>',
        'book' => '<path d="M4 4.5c1.8-.7 4-.7 6 0 2-.7 4.2-.7 6 0v11c-1.8-.7-4-.7-6 0-2-.7-4.2-.7-6 0v-11z"/><path d="M10 4.5v11"/>',
        'document' => '<path d="M6 3h6l3 3v10.5A.5.5 0 0 1 14.5 17h-8a.5.5 0 0 1-.5-.5v-13A.5.5 0 0 1 6 3z"/><path d="M12 3v3h3"/><path d="M7.5 10h5M7.5 12.5h5"/>',
        'cap' => '<path d="M2.5 8L10 4.5 17.5 8 10 11.5 2.5 8z"/><path d="M5.5 9.5v3c0 1 2 2 4.5 2s4.5-1 4.5-2v-3"/>',
        'target' => '<circle cx="10" cy="10" r="6.5"/><circle cx="10" cy="10" r="3.2"/><circle cx="10" cy="10" r=".6" fill="currentColor" stroke="none"/>',
        'people' => '<circle cx="7" cy="7.5" r="2.2"/><path d="M2.8 16c0-2.4 1.9-4 4.2-4s4.2 1.6 4.2 4"/><circle cx="14" cy="8" r="1.8"/><path d="M12.7 12.4c1.9.3 3.3 1.7 3.3 3.6"/>',
        'calendar' => '<rect x="3" y="4.2" width="14" height="12" rx="1.3"/><path d="M3 8h14M7 2.5v3M13 2.5v3"/>',
        'card' => '<rect x="2.5" y="5" width="15" height="10" rx="1.3"/><path d="M2.5 8.3h15M5.5 12h3"/>',
        'ticket' => '<path d="M3 8.2c1 0 1.8-.8 1.8-1.8S4 4.6 3 4.6V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v.6c-1 0-1.8.8-1.8 1.8s.8 1.8 1.8 1.8v6.6a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V8.2z"/><path d="M12 3.5v13"/>',
        'star' => '<path d="M10 2.7l2.2 4.5 5 .7-3.6 3.5.9 5-4.5-2.4-4.5 2.4.9-5-3.6-3.5 5-.7 2.2-4.5z" fill="currentColor" stroke="none"/>',
        'check' => '<path d="M4 10.5l3.7 3.7L16 5.8"/>',
        'plus' => '<path d="M10 3.5v13M3.5 10h13"/>',
        'person' => '<circle cx="10" cy="6.5" r="3"/><path d="M4 17c0-3.3 2.7-5.5 6-5.5s6 2.2 6 5.5"/>',
    ];

    $inner = $paths[$name] ?? '';
    return '<svg class="' . htmlspecialchars($class, ENT_QUOTES) . '" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $inner . '</svg>';
}

function starRow(int $count = 5, string $class = 'icon star-icon'): string
{
    return str_repeat(icon('star', $class), max(0, $count));
}
