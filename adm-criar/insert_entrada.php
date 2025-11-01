<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../conectarbanco.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $valor = $_POST['valor'];
    $client_email = '-'; 
    $client_name = 'TKIPAY'; 
    $client_document = '-'; 
    $client_telefone = '-'; 
    $descricao_transacao = 'entrada-criada'; 
    $real_data = date('Y-m-d'); 
    $status = 'PAID_OUT'; 
    $externalreference = uniqid(); 
    $qrcode_pix = '0'; 
    $paymentcode = '0'; 

    $idtransaction = sprintf('%s-%s-%s-%s-%s', 
        bin2hex(random_bytes(4)), 
        bin2hex(random_bytes(2)), 
        bin2hex(random_bytes(2)), 
        bin2hex(random_bytes(2)), 
        bin2hex(random_bytes(6))
    );

    $paymentCodeBase64 = null;
    $adquirente_ref = null; 
    $taxa_cash_in = 0.00; 
    $deposito_liquido = $_POST['valor']; 
    $taxa_pix_cash_in_adquirente = 0.00;
    $taxa_pix_cash_in_valor_fixo = 0.00; 
    $executor_ordem = 'ADMIN-NETO';


    $conn = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name']);
    if ($conn->connect_error) {
        die("Erro na conexão com o banco de dados: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO solicitacoes 
        (user_id, externalreference, amount, client_name, client_document, client_email, real_data, 
        status, qrcode_pix, paymentcode, idtransaction, paymentCodeBase64, adquirente_ref, 
        taxa_cash_in, deposito_liquido, taxa_pix_cash_in_adquirente, taxa_pix_cash_in_valor_fixo, 
        client_telefone, executor_ordem, descricao_transacao) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");


    $stmt->bind_param("ssssssssssssssssssss", 
        $user_id, 
        $externalreference, 
        $valor, 
        $client_name, 
        $client_document, 
        $client_email, 
        $real_data, 
        $status, 
        $qrcode_pix, 
        $paymentcode, 
        $idtransaction, 
        $paymentCodeBase64, 
        $adquirente_ref, 
        $taxa_cash_in, 
        $deposito_liquido, 
        $taxa_pix_cash_in_adquirente, 
        $taxa_pix_cash_in_valor_fixo, 
        $client_telefone, 
        $executor_ordem, 
        $descricao_transacao
    );

    if ($stmt->execute()) {
        header("Location: entrada.php");
        exit(); 
    } else {
        echo "Erro ao criar solicitação: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
