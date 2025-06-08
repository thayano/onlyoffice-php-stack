<?php
$uploadsDir = __DIR__ . '/uploads/';
$fileName = isset($_GET['file']) ? basename($_GET['file']) : '';
$filePath = $uploadsDir . $fileName;

if (empty($fileName) || !file_exists($filePath)) {
    http_response_code(404);
    die('Arquivo não encontrado.');
}

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Content-Length: ' . filesize($filePath));
readfile($filePath);
exit;