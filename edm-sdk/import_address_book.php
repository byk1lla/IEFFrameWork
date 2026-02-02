<?php
require_once 'autoload.php';

$currentDir = __DIR__;
$htmlPath = $currentDir . '/import_source.html';
if (!file_exists($htmlPath)) {
    die("HTML file not found at $htmlPath\n");
}
$html = file_get_contents($htmlPath);
$dom = new DOMDocument();
// Suppress warnings for malformed HTML
@$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);

$xpath = new DOMXPath($dom);
// Target rows in the grid
$rows = $xpath->query("//tr[contains(@class, 'rgRow') or contains(@class, 'rgAltRow')]");

$users = [];
$count = 0;

foreach ($rows as $row) {
    // Columns (index based on DOM structure, might need tuning)
    // 0: Actions, 1: Name, 2: City, 3: VKN, 4: Office, 5: Date, 6: Alias1, 7: Alias2

    $cols = $xpath->query(".//td", $row);

    // Safety check
    if ($cols->length > 4) {
        $title = trim($cols->item(1)->textContent);
        $vkn = trim($cols->item(3)->textContent);
        $alias = trim($cols->item(6)->textContent);

        // Clean up VKN (remove spaces if any)
        $vkn = preg_replace('/\s+/', '', $vkn);

        if (!empty($vkn) && is_numeric($vkn)) {
            $users[$vkn] = [
                'IDENTIFIER' => $vkn,
                'TITLE' => $title,
                'TYPE' => strlen($vkn) == 10 ? 'KURUM' : 'SAHIS',
                'ALIAS' => !empty($alias) && $alias !== '&nbsp;' ? $alias : ''
            ];
            $count++;
        }
    }
}

// Load existing
$file = $currentDir . '/address_book.json';
if (file_exists($file)) {
    $existing = json_decode(file_get_contents($file), true);
    if (is_array($existing)) {
        // Convert array of objects/arrays to map keyed by IDENTIFIER for de-duplication
        $existingMap = [];
        foreach ($existing as $u) {
            $u = (array) $u;
            if (isset($u['IDENTIFIER']))
                $existingMap[$u['IDENTIFIER']] = $u;
        }

        $users = array_replace($existingMap, $users);
    }
}

// Save back values only
file_put_contents($file, json_encode(array_values($users), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "Processed $count users from HTML. Total in address book: " . count($users) . "\n";
?>