<?php

// Inclui a configuração do banco de dados
require '/var/www/html/config/db.php';

// Define o tipo de conteúdo da resposta como JSON
header('Content-Type: application/json');

// Obtém o método HTTP da requisição
$method = $_SERVER['REQUEST_METHOD'];

// **GET: Busca um contato por ID**
if ($method === 'GET' && isset($_GET['pessoa_id'])) {
    // Obtém o ID da pessoa da URL
    $pessoa_id = intval($_GET['pessoa_id']); // Sanitiza o ID
    // Prepara e executa a consulta SQL para buscar os contatos da pessoa
    $stmt = $conn->prepare("SELECT * FROM contatos WHERE pessoa_id = ?");
    $stmt->bind_param("i", $pessoa_id);
    $stmt->execute();
    // Retorna os resultados da consulta em formato JSON
    echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
} 
// **POST: Cria um novo contato**
elseif ($method === 'POST') {
    // Obtém os dados do corpo da requisição em formato JSON
    $data = json_decode(file_get_contents('php://input'), true);
    // Extrai e sanitiza os dados da pessoa e do contato da requisição
    $pessoa_id = intval($data['pessoa_id'] ?? null);
    $tipo = htmlspecialchars($data['tipo'] ?? '', ENT_QUOTES, 'UTF-8');
    $valor = htmlspecialchars($data['valor'] ?? '', ENT_QUOTES, 'UTF-8');
    $notas = htmlspecialchars($data['notas'] ?? null, ENT_QUOTES, 'UTF-8');

    // Verifica se os campos obrigatórios estão presentes
    if ($pessoa_id && $tipo && $valor) {
        // Insere um novo contato no banco de dados
        $stmt = $conn->prepare("INSERT INTO contatos (pessoa_id, tipo, valor, notas) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $pessoa_id, $tipo, $valor, $notas);
        $stmt->execute();
        // Retorna o ID do novo contato e os dados inseridos
        echo json_encode(['id' => $stmt->insert_id, 'pessoa_id' => $pessoa_id, 'tipo' => $tipo, 'valor' => $valor, 'notas' => $notas]);
    } else {
        // Retorna um erro 400 se os campos obrigatórios estiverem faltando
        http_response_code(400);
        echo json_encode(['erro' => 'Os campos pessoa_id, tipo e valor são obrigatórios.']);
    }
} 
// **PUT: Atualiza um contato existente**
elseif ($method === 'PUT' && isset($_GET['id'])) {
    // Obtém o ID do contato a ser atualizado da URL
    $id = intval($_GET['id']); // Sanitiza o ID
    // Obtém os dados da requisição em formato JSON
    $data = json_decode(file_get_contents('php://input'), true);
    // Extrai e sanitiza os dados do contato a serem atualizados
    $tipo = htmlspecialchars($data['tipo'] ?? '', ENT_QUOTES, 'UTF-8');
    $valor = htmlspecialchars($data['valor'] ?? '', ENT_QUOTES, 'UTF-8');
    $notas = htmlspecialchars($data['notas'] ?? null, ENT_QUOTES, 'UTF-8');

    // Verifica se os campos obrigatórios estão presentes
    if ($tipo && $valor) {
        // Atualiza o contato no banco de dados
        $stmt = $conn->prepare("UPDATE contatos SET tipo = ?, valor = ?, notas = ? WHERE id = ?");
        $stmt->bind_param("sssi", $tipo, $valor, $notas, $id);
        $stmt->execute();
        // Retorna os dados do contato atualizado
        echo json_encode(['id' => $id, 'tipo' => $tipo, 'valor' => $valor, 'notas' => $notas]);
    } else {
        // Retorna um erro 400 se os campos obrigatórios estiverem faltando
        http_response_code(400);
        echo json_encode(['erro' => 'Os campos tipo e valor são obrigatórios.']);
    }
} 
// **DELETE: Exclui um contato**
elseif ($method === 'DELETE' && isset($_GET['id'])) {
    // Obtém o ID do contato a ser excluído da URL
    $id = intval($_GET['id']); // Sanitiza o ID
    // Exclui o contato do banco de dados
    $stmt = $conn->prepare("DELETE FROM contatos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    // Retorna uma mensagem de sucesso
    echo json_encode(['id' => $id, 'mensagem' => 'Contato excluído com sucesso.']);
} else {
    // Retorna um erro 405 se o método HTTP não for suportado
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.']);
}
?>

