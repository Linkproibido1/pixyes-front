<?php
session_start();

// Verificar se o e-mail está presente na sessão
if (!isset($_SESSION['email'])) {
    header("Location: ../");
    exit;
}

include '../conectarbanco.php';

$conn = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name']);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';


if ($id > 0) {
    $sql = "DELETE FROM solicitacoes WHERE id = ? AND descricao_transacao = 'entrada-criada'";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {

        if ($stmt->affected_rows > 0) {
            header("Location: entrada.php");
        } else {
            header("Location: entrada.php");
        }
    } else {
        // Erro na execução da consulta
        header("Location: entrada.php");
    }

    $stmt->close();
} else {
    // ID inválido
    header("Location: entrada.php");
}

$conn->close();
?>
