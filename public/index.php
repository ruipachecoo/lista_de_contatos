<?php

// Inclui a configuração do banco de dados
require '../config/db.php';

// Define o cabeçalho padrão como JSON para as respostas da API
header('Content-Type: application/json');

// Obtem a URI solicitada
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Verifica e roteia as URIs
switch ($uri) {
    case '/pessoas':
        require '/var/www/html/routes/pessoas.php';
        break;

    case '/contatos':
        require '/var/www/html/routes/contatos.php';
        break;

    case '/':
        // Rota principal, retorna uma mensagem básica ou redireciona para documentação
        echo json_encode(['mensagem' => 'Bem-vindo à API de Lista de Contatos!']);
        break;

    default:
        // Rota não encontrada
        http_response_code(404);
        echo json_encode(['erro' => 'Recurso não encontrado.']);
        break;
}

