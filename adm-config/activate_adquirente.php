<?php
session_start();
include '../conectarbanco.php';

// Obter o ID do adquirente
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

// Conectar ao banco de dados
$conn = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name']);

// Verifique a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Atualizar o status do adquirente para ativo
$sql = "UPDATE adquirentes SET status = 1 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo 'success';
} else {
    echo 'error';
}

$stmt->close();
$conn->close();
?>
