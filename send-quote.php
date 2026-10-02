<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok' => false, 'error' => 'method_not_allowed']);
  exit;
}

require __DIR__ . '/config.php';

$input = json_decode(file_get_contents('php://input'), true);
$resumo = isset($input['resumo']) ? trim($input['resumo']) : '';

if ($resumo === '') {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'missing_resumo']);
  exit;
}

$subjectText = 'Orçamento Site Cunha Pintura';
$message = str_replace('*', '', $resumo);

$payload = json_encode([
  'from' => 'Cunha Pintura <orcamento@cunhapintura.com.br>',
  'to' => ['orcamento@cunhapintura.com.br'],
  'cc' => ['cunhapinturasp@gmail.com', 'deklima@gmail.com'],
  'reply_to' => 'cunhapinturasp@gmail.com',
  'subject' => $subjectText,
  'text' => $message,
]);

$ch = curl_init('https://api.resend.com/emails');
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_POSTFIELDS => $payload,
  CURLOPT_HTTPHEADER => [
    'Authorization: Bearer ' . RESEND_API_KEY,
    'Content-Type: application/json',
  ],
  CURLOPT_TIMEOUT => 15,
]);
$response = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$sent = $status >= 200 && $status < 300;

echo json_encode(['ok' => $sent]);
