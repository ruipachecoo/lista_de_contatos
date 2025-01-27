<?php

// Inclui a configuração do banco de dados
require '../config/db.php';

// Define o tipo de conteúdo da resposta como JSON
header('Content-Type: application/json');

// Obtém o método HTTP da requisição
$method = $_SERVER['REQUEST_METHOD'];

// **GET: Busca todas as pessoas**
if ($method === 'GET') {
    // Executa a consulta SQL para buscar todas as pessoas
    $result = $conn->query("SELECT * FROM pessoas");
    // Retorna os resultados da consulta em formato JSON
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
} 
// **POST: Cria uma nova pessoa**
elseif ($method === 'POST') {
    // Obtém os dados do corpo da requisição em formato JSON
    $data = json_decode(file_get_contents('php://input'), true);
    // Extrai e sanitiza o nome da pessoa da requisição
    $nome = htmlspecialchars($data['nome'] ?? '', ENT_QUOTES, 'UTF-8');

    // Verifica se o nome está presente
    if ($nome) {
        // Insere uma nova pessoa no banco de dados
        $stmt = $conn->prepare("INSERT INTO pessoas (nome) VALUES (?)");
        $stmt->bind_param("s", $nome);
        $stmt->execute();
        // Retorna o ID da nova pessoa e o nome inserido
        echo json_encode(['id' => $stmt->insert_id, 'nome' => $nome]);
    } else {
        // Retorna um erro 400 se o nome estiver faltando
        http_response_code(400);
        echo json_encode(['erro' => 'O nome é obrigatório.']);
    }
} 
// **PUT: Atualiza uma pessoa existente**
elseif ($method === 'PUT') {
    // Obtém os dados do corpo da requisição em formato JSON
    $data = json_decode(file_get_contents('php://input'), true);
    // Extrai e sanitiza o ID e o nome da pessoa da requisição
    $id = intval($data['id'] ?? null);
    $nome = htmlspecialchars($data['nome'] ?? '', ENT_QUOTES, 'UTF-8');

    // Verifica se o ID e o nome estão presentes
    if ($id && $nome) {
        // Atualiza a pessoa no banco de dados
        $stmt = $conn->prepare("UPDATE pessoas SET nome = ? WHERE id = ?");
        $stmt->bind_param("si", $nome, $id);
        $stmt->execute();
        // Retorna o ID e o nome da pessoa atualizada
        echo json_encode(['id' => $id, 'nome' => $nome]);
    } else {
        // Retorna um erro 400 se o ID ou o nome estiverem faltando
        http_response_code(400);
        echo json_encode(['erro' => 'ID e nome são obrigatórios.']);
    }
} 
// **DELETE: Exclui uma pessoa**
elseif ($method === 'DELETE') {
    // Obtém os dados do corpo da requisição em formato JSON
    $data = json_decode(file_get_contents('php://input'), true);
    // Extrai e sanitiza o ID da pessoa da requisição
    $id = intval($data['id'] ?? null);

    // Verifica se o ID está presente
    if ($id) {
        // Exclui a pessoa do banco de dados
        $stmt = $conn->prepare("DELETE FROM pessoas WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        // Retorna uma mensagem de sucesso
        echo json_encode(['id' => $id, 'mensagem' => 'Pessoa excluída com sucesso.']);
    } else {
        // Retorna um erro 400 se o ID estiver faltando
        http_response_code(400);
        echo json_encode(['erro' => 'O ID é obrigatório.']);
    }
} else {
    // Retorna um erro 405 se o método HTTP não for suportado
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.']);
}
?>

