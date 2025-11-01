<?php
session_start();

include '../conectarbanco.php';

// Verifique se o usuário está autenticado (ajuste conforme necessário)
// if (!isset($_SESSION['email'])) {
//     die('Acesso negado.');
// }

// Conectar ao banco de dados
$conn = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name']);

// Verifique a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Obter o email do depósito a ser excluído
$email = $_POST['email'];

// Preparar a consulta de exclusão
$sql = "DELETE FROM confirmar_deposito WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);

// Executar a consulta e verificar o resultado
if ($stmt->execute()) {
    echo 'success';
} else {
    echo 'error';
}

// Fechar a conexão
$stmt->close();
$conn->close();
?>
