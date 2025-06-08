<?php
// api_config.php

require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;

// URL do serviço ONLYOFFICE. 
$onlyoffice_server_url = 'http://onlyoffice-server'; // 

// URL do App PHP
$php_server_url = 'http://meu-app-php'; // 

// JWT.
$jwtSecret = 'meu_segredo_super_secreto_e_longo'; 

$fileName = basename($_GET['file']);
$filePath = __DIR__ . '/uploads/' . $fileName;

if (!file_exists($filePath)) {
    http_response_code(404);
    echo json_encode(['error' => 'Arquivo não encontrado']);
    exit;
}

$fileUrl = $php_server_url . '/download.php?file=' . urlencode($fileName);
$callbackUrl = $php_server_url . '/save.php?file=' . urlencode($fileName);
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
$docKey = md5_file($filePath) . time();

$config = [
    'document' => [ 'fileType' => $fileExt, 'key' => $docKey, 'title' => $fileName, 'url' => $fileUrl ],
    'editorConfig' => [
        'mode' => 'edit',
        'callbackUrl' => $callbackUrl,
        'user' => [ 'id' => 'user-1', 'name' => 'Usuário de Teste' ],
        'autosave' => [ 'type' => 'disabled' ],
        'customization' => [
            'forcesave' => true,
            'comments' => false,
            'feedback' => false,
            'modeChange' => false,
            'documentLink' => false,
            'documentTitle' => true,
            'chat' => false,
        ],
        'lang' => 'pt',
    ],
        'width' => '1000px%',
        'height' => '1000px',
];

$token = JWT::encode($config, $jwtSecret, 'HS256');
$config['token'] = $token;

header('Content-Type: application/json');
echo json_encode($config);
exit;