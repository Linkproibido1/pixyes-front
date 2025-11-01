<?php
session_start();

// Verificar se o usuário está autenticado
if (!isset($_SESSION['email'])) {
    die("Sessão expirada ou e-mail não encontrado.");
}

// Incluir o arquivo de configuração do banco de dados
include '../conectarbanco.php';

// Conectar ao banco de dados
$conn = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name']);

// Verifique a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Verificar se o ID do usuário foi passado via POST
if (isset($_POST['id'])) {
    $userId = (int)$_POST['id'];

    // Preparar a consulta SQL para excluir o usuário
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $userId);

        // Executar a consulta
        if ($stmt->execute()) {
            echo 'success'; // Retorna sucesso se a exclusão for bem-sucedida
        } else {
            echo 'error'; // Retorna erro se a exclusão falhar
        }

        // Fechar a declaração
        $stmt->close();
    } else {
        echo 'error'; // Retorna erro se a preparação da declaração falhar
    }
} else {
    echo 'error'; // Retorna erro se o ID não for fornecido
}

// Fechar a conexão
$conn->close();
?>
