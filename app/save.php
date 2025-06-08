<?php

require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Desativa a exibição de erros, mas os logs continuarão funcionando.
ini_set('display_errors', 0);
error_reporting(E_ALL); // Reporta todos os erros para o log

$jwtSecret = 'meu_segredo_super_secreto_e_longo';
$response = ['error' => 0];
header('Content-Type: application/json');

// Registra que o script foi chamado.
error_log("[SAVE DEBUG] O script save.php foi acionado.");

try {
    $data = json_decode(file_get_contents('php://input'), true);
    if (empty($data) || !isset($data['token'])) {
        throw new Exception("Requisição inválida ou token não encontrado.");
    }

    $decodedPayload = JWT::decode($data['token'], new Key($jwtSecret, 'HS256'));
    $status = $decodedPayload->status ?? null;
    error_log("[SAVE DEBUG] Status recebido do ONLYOFFICE: " . $status);

    if ($status == 2 || $status == 6) {
        
        $downloadUrl = $decodedPayload->url;
        $correctedUrl = str_replace('localhost:8888', 'onlyoffice-server', $downloadUrl);
        error_log("[SAVE DEBUG] Tentando baixar de: " . $correctedUrl);

        $updatedFileContent = file_get_contents($correctedUrl);
        
        // Verifica se o download funcionou e se o conteúdo não está vazio
        if ($updatedFileContent !== false && !empty($updatedFileContent)) {
            $contentLength = strlen($updatedFileContent);
            error_log("[SAVE DEBUG] Download bem-sucedido. Tamanho do arquivo: " . $contentLength . " bytes.");

            $fileName = basename($_GET['file']);
            $filePath = __DIR__ . '/uploads/' . time() . '.docx';
            error_log("[SAVE DEBUG] Tentando salvar em: " . $filePath);

            // Tenta escrever no arquivo e verifica o resultado
            $bytesWritten = file_put_contents($filePath, $updatedFileContent);

            if ($bytesWritten === false) {
                // Se file_put_contents falhar, ele retorna false.
                throw new Exception("FALHA AO ESCREVER O ARQUIVO NO DISCO. Verifique as permissões da pasta 'uploads'.");
            } else {
                error_log("[SAVE DEBUG] Sucesso! " . $bytesWritten . " bytes escritos em " . $filePath);
            }
        } else {
            throw new Exception("Falha no download do arquivo ou arquivo veio vazio. URL: " . $correctedUrl);
        }
    }
} catch (Exception $e) {
    $response['error'] = 1;
    $response['message'] = $e->getMessage();
    error_log("[SAVE DEBUG] ERRO CAPTURADO: " . $e->getMessage());
}

echo json_encode($response);
exit;