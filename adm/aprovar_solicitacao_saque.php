<?php
include '../conectarbanco.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_GET['id'])) {
    die("Parâmetro inválido: id não foi passado.");
}

$solicitacao_id = $_GET['id'];

// Conectar ao banco de dados
$conn = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name']);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Realizar a consulta usando JOIN entre as tabelas retiradas e users_key para buscar o user_id e a api_key correta
$query = "
    SELECT r.user_id, r.tipo_chave, r.chave, r.valor, r.status, uk.api_key 
    FROM retiradas AS r
    JOIN users_key AS uk ON r.user_id = uk.user_id
    WHERE r.id = ?
    LIMIT 1
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $solicitacao_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $solicitacao = $result->fetch_assoc();
    $user_id = $solicitacao['user_id'];

    // Verificar se a retirada já foi aprovada
    if ($solicitacao['status'] == 1) {
        echo '<div style="background-color: black; color: green; height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">';
        echo '<div id="modal" style="display: block; background-color: rgba(0, 0, 0, 0.8); color: white; padding: 20px; border-radius: 10px;">A retirada já foi aprovada!</div>';
        echo '</div>';
        echo '<script>
                setTimeout(function() {
                    window.location.href = "saques_usuarios.php";
                }, 2000); // 2 segundos de espera antes de redirecionar
              </script>';
        exit(); // Encerra a execução do script
    }

    $keypix = $solicitacao['chave'];
    $amount = $solicitacao['valor'];
    $api_key = $solicitacao['api_key']; // A API key correta já foi recuperada diretamente

    // Preparar os dados para a requisição
    $data = [
        "api-key" => $api_key,
        "name" => $user_id, // Enviar o user_id da tabela retiradas
        "cpf" => $keypix, // Remover se cpf não for utilizado
        "keypix" => $keypix,
        "amount" => $amount
    ];

    // Configuração da requisição cURL
    $url = "https://api.pagvox.com/c1/cashout/";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    // Executar a requisição
    $response = curl_exec($ch);

    // Verificar se houve algum erro na requisição cURL
    if ($response === false) {
        $error = curl_error($ch);
        echo "Erro cURL: " . $error;
    } else {
        // Exibir a resposta da API para depuração
        $response_data = json_decode($response, true);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Obter o código de status HTTP

        echo '<pre>';
        print_r($response_data); // Imprime a resposta para depuração
        echo '</pre>';

        if ($http_code == 200 && isset($response_data['status']) && $response_data['status'] == "success") {
            // Atualizar o status da solicitação no banco de dados para 1
            $update_query = "UPDATE retiradas SET status = '1', data_pagamento = NOW() WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("i", $solicitacao_id);
            $stmt->execute();

            // Exibir a mensagem de sucesso e redirecionar
            echo '<div style="background-color: black; color: green; height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">';
            echo '<div id="modal" style="display: none; background-color: rgba(0, 0, 0, 0.8); color: white; padding: 20px; border-radius: 10px;">Solicitação aprovada!</div>';
            echo '</div>';

            echo '<script>
                document.getElementById("modal").style.display = "block"; 
                setTimeout(function() {
                    window.location.href = "saques_usuarios.php";
                }, 2000); // 2 segundos de espera antes de redirecionar
            </script>';

            curl_close($ch);
            exit(); // Encerra a execução do script após exibir a mensagem
        } else {
            // Se a API retornar algo inesperado, exibir a mensagem
            if (isset($response_data['message'])) {
                echo "Erro ao aprovar a solicitação: " . $response_data['message'];
            } else {
                echo "Resposta inesperada da API.";
            }
        }
    }

    curl_close($ch);
} else {
    echo "Solicitação ou API key não encontrada.";
}

$stmt->close();
$conn->close();
?>
