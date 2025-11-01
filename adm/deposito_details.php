
<?php
session_start();

// Verificar se o e-mail está presente na sessão
if (!isset($_SESSION['email'])) {
  // Se o e-mail não estiver presente na sessão, redirecione para outra página
  header("Location: ../");
  exit; // Certifique-se de sair do script após o redirecionamento
}

include '../conectarbanco.php';

$conn = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name']);

// Verifica se houve algum erro na conexão
if ($conn->connect_error) {
  die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Recuperar o e-mail da sessão
$email = $_SESSION['email'];

// Consultar a coluna permission do usuário pelo email
$sql = "SELECT permission FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($permission);
$stmt->fetch();

$stmt->close();
$conn->close();

// Verificar o valor da coluna permission
if ($permission == 1) {
  // Redirecionar para a página ../home se o permission for 1
  header("Location: ../home");
  exit;
}
?>



<?php
session_start();

// Verificar se o e-mail está presente na sessão
if (!isset($_SESSION['email'])) {
  // Se o e-mail não estiver presente na sessão, redirecione para outra página
  header("Location: ../");
  exit; // Certifique-se de sair do script após o redirecionamento
}

include '../conectarbanco.php';

$conn = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name']);

// Verifica se houve algum erro na conexão
if ($conn->connect_error) {
  die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Recuperar o e-mail da sessão
$email = $_SESSION['email'];

$sql = "SELECT user_id, nome, status, permission, saldo, transacoes_aproved FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($user_id, $nome, $status, $permission, $saldo, $transacoes_aproved);
$stmt->fetch();

// Armazenar o user_id na sessão
$_SESSION['user_id'] = $user_id;

$stmt->close();
$conn->close();
?>




<?php

// Verifica se o parâmetro de logout foi passado na URL
if (isset($_GET['logout'])) {
    // Destroi a sessão
    session_destroy();
    // Redireciona para a página inicial
    header("Location: ../");
    exit;
}
?>





<?php

// Verificar se o e-mail está presente na sessão
if (!isset($_SESSION['email'])) {
  // Se o e-mail não estiver presente na sessão, redirecione para outra página
  header("Location: ../");
  exit; // Certifique-se de sair do script após o redirecionamento
}

// Incluir o arquivo de configuração do banco de dados
include '../conectarbanco.php';

// Criar uma conexão com o banco de dados usando as credenciais fornecidas
$conn = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name']);

// Verifica se houve algum erro na conexão
if ($conn->connect_error) {
  die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Recuperar o e-mail da sessão
$email = $_SESSION['email'];

// Consulta SQL para obter informações do usuário com base no e-mail da sessão
$sql = "SELECT user_id, nome, status, permission, saldo, transacoes_aproved FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($user_id, $nome, $status, $permission, $saldo, $transacoes_aproved);
$stmt->fetch();

// Armazenar user_id em uma variável
$user_id_var = $user_id;

$stmt->close();
$conn->close();
?>






<?php
session_start();

include '../conectarbanco.php';

// Conectar ao banco de dados
$conn = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name']);

// Verificar a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Obter a data e hora atual
$now = new DateTime();
$todayStart = $now->format('Y-m-d 00:00:00'); // Início do dia de hoje
$todayEnd = $now->format('Y-m-d 23:59:59');   // Fim do dia de hoje
$startOfMonth = $now->format('Y-m-01 00:00:00'); // Início do mês
$startOfWeek = $now->modify('monday this week')->format('Y-m-d 00:00:00'); // Início da semana

// Reconfigurar a data atual para manter a mesma instância de DateTime
$now = new DateTime();

// Consulta para obter o total de cadastros
$sqlTotal = "SELECT COUNT(*) AS total FROM users";
$resultTotal = $conn->query($sqlTotal);
$rowTotal = $resultTotal->fetch_assoc();
$totalCadastros = $rowTotal['total'];

// Consulta para obter o número de cadastros hoje
$sqlToday = "SELECT COUNT(*) AS today FROM users WHERE data_cadastro BETWEEN ? AND ?";
$stmtToday = $conn->prepare($sqlToday);
$stmtToday->bind_param("ss", $todayStart, $todayEnd);
$stmtToday->execute();
$resultToday = $stmtToday->get_result();
$rowToday = $resultToday->fetch_assoc();
$cadastrosHoje = $rowToday['today'];

// Consulta para obter o número de cadastros no mês
$sqlMonth = "SELECT COUNT(*) AS month FROM users WHERE data_cadastro >= ?";
$stmtMonth = $conn->prepare($sqlMonth);
$stmtMonth->bind_param("s", $startOfMonth);
$stmtMonth->execute();
$resultMonth = $stmtMonth->get_result();
$rowMonth = $resultMonth->fetch_assoc();
$cadastrosMes = $rowMonth['month'];

// Consulta para obter o número de cadastros na semana
$sqlWeek = "SELECT COUNT(*) AS week FROM users WHERE data_cadastro >= ?";
$stmtWeek = $conn->prepare($sqlWeek);
$stmtWeek->bind_param("s", $startOfWeek);
$stmtWeek->execute();
$resultWeek = $stmtWeek->get_result();
$rowWeek = $resultWeek->fetch_assoc();
$cadastrosSemana = $rowWeek['week'];

// Fechar a conexão
$conn->close();
?>






<!-- Este código gera o URL base do site combinando o protocolo, o nome de domínio e o caminho do diretório -->
<?php
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/../';
?>
<!-- This code generates the base URL for the website by combining the protocol, domain name, and directory path -->

<!-- This code generates the base URL for the website by combining the protocol, domain name, and directory path -->

<!-- This code is useful for internal styles  -->
<?php ob_start(); ?>



<?php $styles = ob_get_clean(); ?>
<!-- This code is useful for internal styles  -->

<!-- This code is useful for content -->
<?php ob_start(); ?>


<script>
    // Recuperar o user_id do PHP e imprimir no console
    const userId = <?php echo json_encode($_SESSION['user_id']); ?>;
    console.log("User ID:", userId);
  </script>




            <div class="main-content app-content">
                <div class="container-fluid">

                    <!-- Start::page-header -->
                    <div class="d-flex align-items-center justify-content-between my-4 page-header-breadcrumb flex-wrap gap-2">
                        <div>
                            <p class="fw-medium fs-20 mb-0">Olá, ADM</p>
                        </div>
</div>








                   




<?php
include '../conectarbanco.php';

// Obter o ID da solicitação da URL
$id = $_GET['id'];

// Conectar ao banco de dados
$conn = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name']);

// Verificar a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Consulta para obter os dados da solicitação
$sql = "SELECT * FROM solicitacoes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "Solicitação não encontrada.";
    exit;
}

$stmt->close();
$conn->close();
?>



<!-- Botão Voltar -->
<div class="mb-3">
    <a href="usuarios.php" class="btn btn-secondary">Voltar</a>
</div>


<!-- Exibição dos dados -->
<div class="row">
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">
                    DADOS DA SOLICITAÇÃO
                </div>
            </div>
            <div class="card-body">
                <!-- Dados da Solicitação -->
                <div class="row gy-4">
                    <div class="col-12 col-sm-6 col-md-4">
                        <label class="form-label">ID da Solicitação:</label>
                        <p><?= $row['id'] ?></p>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <label class="form-label">Usuário:</label>
                        <p><?= $row['user_id'] ?></p>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <label class="form-label">Referência Externa:</label>
                        <p><?= $row['externalreference'] ?></p>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <label class="form-label">Número da Solicitação:</label>
                        <p><?= $row['requestNumber'] ?></p>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <label class="form-label">Data de Vencimento:</label>
                        <p><?= date('d/m/Y', strtotime($row['dueDate'])) ?></p>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <label class="form-label">Valor:</label>
                        <p>R$ <?= number_format($row['amount'], 2, ',', '.') ?></p>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <label class="form-label">Nome do Cliente:</label>
                        <p><?= $row['client_name'] ?></p>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <label class="form-label">Documento do Cliente:</label>
                        <p><?= $row['client_document'] ?></p>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <label class="form-label">Email do Cliente:</label>
                        <p><?= $row['client_email'] ?></p>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <label class="form-label">Data Real:</label>
                        <p><?= date('d/m/Y', strtotime($row['real_data'])) ?></p>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <label class="form-label">Status:</label>
                        <p class="
                            <?php 
                            switch($row['status']) {
                                case 'WAITING_FOR_APPROVAL':
                                    echo 'bg-warning-transparent text-warning'; // Pendente (amarelo)
                                    break;
                                case 'PAID_OUT':
                                    echo 'bg-success-transparent text-success'; // Aprovada (verde)
                                    break;
                                default:
                                    echo 'bg-secondary text-dark'; // Status Desconhecido (cinza)
                            }
                            ?>
                            p-2 rounded">
                            <?php 
                            switch($row['status']) {
                                case 'WAITING_FOR_APPROVAL':
                                    echo "PENDENTE";
                                    break;
                                case 'PAID_OUT':
                                    echo "APROVADO";
                                    break;
                                default:
                                    echo "Status Desconhecido";
                            }
                            ?>
                        </p>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <label class="form-label">QR Code PIX:</label>
                        <p><?= $row['qrcode_pix'] ?></p>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <label class="form-label">Código de Pagamento:</label>
                        <p><?= $row['paymentcode'] ?></p>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <label class="form-label">ID da Transação:</label>
                        <p><?= $row['idtransaction'] ?></p>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <label class="form-label">Taxa Cash In:</label>
                        <p>R$ <?= number_format($row['taxa_cash_in'], 2, ',', '.') ?></p>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <label class="form-label">Depósito Líquido:</label>
                        <p><?= $row['deposito_liquido'] ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



                   

                </div>
            </div>

<?php $content = ob_get_clean(); ?>
<!-- This code is useful for content -->

<!-- This code is useful for internal scripts  -->
<?php ob_start(); ?>

        <!-- Apex Charts JS -->
        <script src="<?php echo $baseUrl; ?>/assets/libs/apexcharts/apexcharts.min.js"></script>
        
 

<?php $scripts = ob_get_clean(); ?>
<!-- This code is useful for internal scripts  -->

<!-- This code use for render base file -->
<?php include '../layouts/base.php'; ?>
<!-- This code use for render base file -->

 

<!-- Internal Apex Area Charts JS -->
<script src="../assets/js/apexcharts-area.js"></script>













<!-- Internal Apex Area Charts JS -->
<script src="../assets/js/apexcharts-area.js"></script>



