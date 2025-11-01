<?php
include '../conectarbanco.php';

// Conectar ao banco de dados
$conn = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name']);

// Verificar se houve erro na conexão
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Erro na conexão com o banco de dados: ' . $conn->connect_error]));
}

// Verificar se os dados foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url_cash_in = $_POST['url_cash_in'];
    $url_cash_out = $_POST['url_cash_out'];
    $taxa_pix_cash_in = $_POST['taxa_pix_cash_in'];
    $taxa_pix_cash_out = $_POST['taxa_pix_cash_out'];

    // Preparar a consulta SQL para atualizar os dados da tabela ad_pagpix
    $sql = "UPDATE ad_pagpix SET url_cash_in = ?, url_cash_out = ?, taxa_pix_cash_in = ?, taxa_pix_cash_out = ? LIMIT 1";
    $stmt = $conn->prepare($sql);

    // Verificar se houve erro na preparação da consulta
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Erro ao preparar a consulta: ' . $conn->error]);
        exit;
    }

    // Associar os parâmetros à consulta
    $stmt->bind_param("ssdd", $url_cash_in, $url_cash_out, $taxa_pix_cash_in, $taxa_pix_cash_out);

    // Executar a consulta
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao executar a consulta: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Método de requisição inválido']);
}

$conn->close();
?>
