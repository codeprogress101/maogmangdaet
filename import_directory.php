<?php
require_once __DIR__ . '/includes/directory_loader.php';

$source = __DIR__ . '/directory.xlsx';
$destination = __DIR__ . '/uploads/directory.json';

echo "Importing directory from {$source}\n";

if (!is_readable($source)) {
    fwrite(STDERR, "Source file not found or unreadable.\n");
    exit(1);
}

$entries = load_directory_entries($source);

if (empty($entries)) {
    fwrite(STDERR, "No entries found in the spreadsheet.\n");
    exit(1);
}

$data = [
    'generated_at' => date('c'),
    'source_file' => basename($source),
    'total' => count($entries),
    'entries' => $entries,
];

if (file_put_contents($destination, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) === false) {
    fwrite(STDERR, "Failed to write directory data to {$destination}.\n");
    exit(1);
}

echo "Directory data exported to {$destination}.\n";