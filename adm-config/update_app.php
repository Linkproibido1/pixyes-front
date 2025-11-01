<?php
include '../conectarbanco.php';

// Conectar ao banco de dados
$conn = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name']);

// Verifique a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Obter dados do formulário
$taxa_cash_in_padrao = $_POST['taxa_cash_in_padrao'];
$taxa_cash_out_padrao = $_POST['taxa_cash_out_padrao'];
$reserva_padrao = $_POST['reserva_padrao'];
$taxa_fixa_padrao = $_POST['taxa_fixa_padrao'];
$indicacao_padrao = $_POST['indicacao_padrao'];
$taxa_pix_valor_real_cash_in_padrao = $_POST['taxa_pix_valor_real_cash_in_padrao']; // Novo campo

// Atualizar o único registro
$sql = "UPDATE app SET taxa_cash_in_padrao = ?, taxa_cash_out_padrao = ?, taxa_pix_valor_real_cash_in_padrao = ?, reserva_padrao = ?, taxa_fixa_padrao = ?, indicacao_padrao = ?  WHERE 1"; // Incluindo nova coluna
$stmt = $conn->prepare($sql);
$stmt->bind_param("dddsss", $taxa_cash_in_padrao, $taxa_cash_out_padrao, $taxa_pix_valor_real_cash_in_padrao, $reserva_padrao, $taxa_fixa_padrao, $indicacao_padrao); // Alterado para 'ddd' para incluir o novo parâmetro

if ($stmt->execute()) {
    echo 'success';
} else {
    echo 'error';
}

$stmt->close();
$conn->close();
?>
