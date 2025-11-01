<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Teste PIX</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }

        .container {
            display: flex;
            justify-content: space-around;
            align-items: flex-start;
            margin-top: 20px;
        }

        .form-container,
        .api-container {
            max-width: 700px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-container h1,
        .api-container h1 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .form-container input,
        .api-container input {
            width: 100%;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1.2rem;
        }

        .form-container button,
        .api-container button {
            width: 100%;
            padding: 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.2rem;
        }

        #qrcode {
            display: none;
            text-align: center;
            margin-top: 20px;
        }

        #copyButton {
            display: none;
            margin-top: 20px;
        }

        #apiResponse {
            margin: 20px auto;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            white-space: pre-wrap;
            max-width: 800px;
            word-wrap: break-word;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-container">
            <h5 class="modal-title" id="exampleModalLabel">Dados de Pagamento</h5>
            <div id="qrcode"></div>
            <div class="form">
                <button type="button" class="btn btn-success" id="copyButton">Copiar Pix</button>
            </div>
            <div class="properties">
                <h1>Nome</h1>
                <div class="form">
                    <input type="text" value="Paulo" id="name" name="name" readonly>
                </div>
                <h1>CPF</h1>
                <div class="form">
                    <input maxlength="11" value="70291669492" placeholder="70291669492" type="text" id="document" name="document" required>
                </div>
                <h1>Valor</h1>
                <div class="form">
                    <input type="text" value="10" placeholder="10" readonly id="valuedeposit">
                </div>
                <button type="button" onclick="generateQRCode()">Pagar</button>
                <div id="loadingSpinner" class="loading-spinner"></div>
            </div>
        </div>

        <div class="api-container" style="width: 700px;">
            <h1>URL da API</h1>
            <div class="form">
                <input type="text" placeholder="URL de Requisição" id="apiUrl" value="https://api.4netpay.com/v1/gateway/">
            </div>
            <h1>Chave API</h1>
            <div class="form">
                <input type="text" placeholder="Chave key" id="clientId" value="">
            </div>
        </div>
    </div>

    <div id="apiResponse"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="../js/confetti.min.js"></script>
    <script>
        confetti.start();
        setTimeout(function() {
            confetti.stop();
        }, 8000);
    </script>
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
    <script>
        var paymentCode;
        var transactionId;
    
        async function generateQRCode() {
            var name = "Paulo";
            var cpf = document.getElementById('document').value;
            var amount = document.getElementById('valuedeposit').value;
            var apiUrl = document.getElementById('apiUrl').value;
            var clientId = document.getElementById('clientId').value;
    
            var payload = {
                "api-key": clientId,
                "requestNumber": "12356",
                "dueDate": "2023-12-31",
                "amount": parseFloat(amount),
                "client": {
                    "name": name,
                    "document": cpf,
                    "email": "cliente@email.com"
                }
            };
    
            try {
                const response = await fetch(apiUrl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify(payload)
                });
    
                const data = await response.json();
                document.getElementById('apiResponse').innerHTML = JSON.stringify(data);
    
                if (data.paymentCode) {
                    paymentCode = data.paymentCode;
                    transactionId = data.idTransaction; // Ajustado para pegar idTransaction
    
                    console.log("Transaction ID:", transactionId); // Imprime o ID da transação no console
    
                    document.querySelectorAll('.properties').forEach(function(element) {
                        element.style.display = 'none';
                    });
    
                    var qrcode = new QRCode(document.getElementById('qrcode'), {
                        text: data.paymentCode,
                        width: 256,
                        height: 256
                    });
    
                    document.getElementById('qrcode').style.display = 'block';
                    document.getElementById('copyButton').style.display = 'block';
                    
                    // Inicia a verificação do pagamento a cada 2 segundos
                    setInterval(checkPaymentStatus, 2000);
                } else {
                    console.error("Erro na solicitação:", data.message);
                }
            } catch (error) {
                console.error("Erro na solicitação:", error);
            }
        }
    
        async function checkPaymentStatus() {
            var apiUrl = "https://api.4netpay.com/v1/webhook/";
            var payload = {
                "idtransaction": transactionId
            };
    
            try {
                const response = await fetch(apiUrl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify(payload)
                });
    
                const data = await response.json();
                document.getElementById('apiResponse').innerHTML = JSON.stringify(data);
    
                if (data.status === "PAID_OUT") {
                    clearInterval(checkPaymentStatus); // Para a verificação quando o pagamento for confirmado
                    alert("Pagamento confirmado!");
                } else if (data.status === "WAITING_FOR_APPROVAL") {
                    console.log("Aguardando aprovação...");
                }
            } catch (error) {
                console.error("Erro na verificação do pagamento:", error);
            }
        }
    
        function copyPixCode() {
            navigator.clipboard.writeText(paymentCode)
                .then(() => {
                    alert("PIX Key copiada para a área de transferência.");
                })
                .catch(err => {
                    console.error('Erro ao copiar PIX Key: ', err);
                    alert("Erro ao copiar PIX Key. Verifique se o seu navegador suporta esta funcionalidade.");
                });
        }
    
        document.getElementById('copyButton').addEventListener('click', copyPixCode);
    </script>
    
    
</body>

</html>
