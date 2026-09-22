<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok' => false, 'error' => 'method_not_allowed']);
  exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$resumo = isset($input['resumo']) ? trim($input['resumo']) : '';
$nome = isset($input['nome']) ? trim($input['nome']) : '';
$cidade = isset($input['cidade']) ? trim($input['cidade']) : '';

if ($resumo === '') {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'missing_resumo']);
  exit;
}

$to = 'cunhapinturasp@gmail.com,deklima@gmail.com';
$subjectText = 'Orçamento — ' . ($nome !== '' ? $nome : 'novo cliente') . ($cidade !== '' ? ' (' . $cidade . ')' : '');
$subject = '=?UTF-8?B?' . base64_encode($subjectText) . '?=';
$message = str_replace('*', '', $resumo);
$headers = "From: Cunha Pintura <site@cunhapinturas.com.br>\r\n" .
           "Reply-To: cunhapinturasp@gmail.com\r\n" .
           "Content-Type: text/plain; charset=UTF-8";

$sent = @mail($to, $subject, $message, $headers);

echo json_encode(['ok' => $sent]);
