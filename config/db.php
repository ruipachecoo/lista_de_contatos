<?php
$host = getenv('MYSQL_HOST') ?: 'db';
$db = getenv('MYSQL_DATABASE') ?: 'contact_list';
$user = getenv('MYSQL_USER') ?: 'contact_user';
$password = getenv('MYSQL_PASSWORD') ?: 'secure_password';

$conn = new mysqli($host, $user, $password, $db);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>

