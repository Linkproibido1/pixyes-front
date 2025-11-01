<?php
session_start();

// Verificar se o e-mail está presente na sessão
if (!isset($_SESSION['email'])) {
    header("Location: ../");
    exit;
}

include '../conectarbanco.php';

// Conectar ao banco de dados
$conn = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name']);

// Verifique a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Obter o ID da solicitação da URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Excluir a solicitação
$sql = "DELETE FROM retiradas WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Solicitação excluída com sucesso.";
} else {
    echo "Erro ao excluir a solicitação.";
}

$stmt->close();
$conn->close();

header("Location: saques_usuarios.php");
exit;
?>
