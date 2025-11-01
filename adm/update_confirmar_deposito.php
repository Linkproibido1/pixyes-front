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

// Obter os dados do formulário
$email = $_POST['email'];
$externalreference = $_POST['externalreference'];
$valor = $_POST['valor'];
$status = $_POST['status'];
$data = $_POST['data'];

// Preparar a consulta de atualização
$sql = "UPDATE confirmar_deposito SET externalreference = ?, valor = ?, status = ?, data = ? WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $externalreference, $valor, $status, $data, $email);

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
