

<?php
session_start();
if (!isset($_SESSION['email'])) {
  header("Location: ../");
  exit; 
}

include '../conectarbanco.php';

$conn = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name']);
if ($conn->connect_error) {
  die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}
$email = $_SESSION['email'];
$sql = "SELECT permission FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($permission);
$stmt->fetch();

$stmt->close();
$conn->close();

if ($permission == 1) {
  header("Location: ../home");
  exit;
}
?>



<?php
session_start();


if (!isset($_SESSION['email'])) {
  header("Location: ../");
  exit; 
}

include '../conectarbanco.php';

$conn = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name']);

if ($conn->connect_error) {
  die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

$email = $_SESSION['email'];

$sql = "SELECT user_id, nome, status, permission, saldo, transacoes_aproved FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($user_id, $nome, $status, $permission, $saldo, $transacoes_aproved);
$stmt->fetch();

$_SESSION['user_id'] = $user_id;

$stmt->close();
$conn->close();
?>




<?php

if (isset($_GET['logout'])) {

    session_destroy();

    header("Location: ../");
    exit;
}
?>





<?php


if (!isset($_SESSION['email'])) {
  header("Location: ../");
  exit; 
}


include '../conectarbanco.php';


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
include '../conectarbanco.php';

date_default_timezone_set('America/Sao_Paulo');

$dataHoje = date('Y-m-d');
$mesAtual = date('Y-m');
$conn = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name']);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
$sqlDia = "SELECT COUNT(*) as total FROM solicitacoes WHERE status = 'PAID_OUT' AND DATE(real_data) = '$dataHoje'";
$resultDia = $conn->query($sqlDia);
if ($resultDia->num_rows > 0) {
    $rowDia = $resultDia->fetch_assoc();
    $totalaprovadasHoje = $rowDia['total'];
} else {
    $totalaprovadasHoje = 0;
}
$sqlMes = "SELECT COUNT(*) as total FROM solicitacoes WHERE status = 'PAID_OUT' AND DATE_FORMAT(real_data, '%Y-%m') = '$mesAtual'";
$resultMes = $conn->query($sqlMes);
if ($resultMes->num_rows > 0) {
    $rowMes = $resultMes->fetch_assoc();
    $totalaprovadasMes = $rowMes['total'];
} else {
    $totalaprovadasMes = 0;
}
$sqlTotal = "SELECT COUNT(*) as total FROM solicitacoes WHERE status = 'PAID_OUT'";
$resultTotal = $conn->query($sqlTotal);
if ($resultTotal->num_rows > 0) {
    $rowTotal = $resultTotal->fetch_assoc();
    $totalaprovadas = $rowTotal['total'];
} else {
    $totalaprovadas = 0;
}
$sqltotalsolicitacoes = "SELECT COUNT(*) as total FROM solicitacoes";
$resulttotalsolicitacoes = $conn->query($sqltotalsolicitacoes);
if ($resulttotalsolicitacoes->num_rows > 0) {
    $rowtotalsolicitacoes = $resulttotalsolicitacoes->fetch_assoc();
    $totalsolicitacoes = $rowtotalsolicitacoes['total'];
} else {
    $totalsolicitacoes = 0;
}

$conn->close();
?>



<?php
include '../conectarbanco.php';

date_default_timezone_set('America/Sao_Paulo');

$dataHoje = date('Y-m-d');
$mesAtual = date('Y-m');

$conn = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name']);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Valor total aprovado hoje (PAID_OUT)
$sqlValorHoje = "SELECT SUM(amount) as total_valor FROM solicitacoes WHERE status = 'PAID_OUT' AND DATE(real_data) = '$dataHoje'";
$resultValorHoje = $conn->query($sqlValorHoje);

if ($resultValorHoje->num_rows > 0) {
    $rowValorHoje = $resultValorHoje->fetch_assoc();
    $valorAprovadoHoje = $rowValorHoje['total_valor'] ? $rowValorHoje['total_valor'] : 0;
} else {
    $valorAprovadoHoje = 0;
}

// Valor total aprovado no mês (PAID_OUT)
$sqlValorMes = "SELECT SUM(amount) as total_valor FROM solicitacoes WHERE status = 'PAID_OUT' AND DATE_FORMAT(real_data, '%Y-%m') = '$mesAtual'";
$resultValorMes = $conn->query($sqlValorMes);

if ($resultValorMes->num_rows > 0) {
    $rowValorMes = $resultValorMes->fetch_assoc();
    $valorAprovadoMes = $rowValorMes['total_valor'] ? $rowValorMes['total_valor'] : 0;
} else {
    $valorAprovadoMes = 0;
}

// Valor total aprovado (PAID_OUT)
$sqlValorTotal = "SELECT SUM(amount) as total_valor FROM solicitacoes WHERE status = 'PAID_OUT'";
$resultValorTotal = $conn->query($sqlValorTotal);

if ($resultValorTotal->num_rows > 0) {
    $rowValorTotal = $resultValorTotal->fetch_assoc();
    $valorAprovadoTotal = $rowValorTotal['total_valor'] ? $rowValorTotal['total_valor'] : 0;
} else {
    $valorAprovadoTotal = 0;
}

$conn->close();
?>

<?php
include '../conectarbanco.php';

date_default_timezone_set('America/Sao_Paulo');

$dataHoje = date('Y-m-d');
$mesAtual = date('Y-m');

$conn = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name']);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Valor total de depósito líquido aprovado hoje (PAID_OUT)
$sqlDepositoHoje = "SELECT SUM(deposito_liquido) as total_valor FROM solicitacoes WHERE status = 'PAID_OUT' AND DATE(real_data) = '$dataHoje'";
$resultDepositoHoje = $conn->query($sqlDepositoHoje);

if ($resultDepositoHoje->num_rows > 0) {
    $rowDepositoHoje = $resultDepositoHoje->fetch_assoc();
    $valorDepositoAprovadoHoje = $rowDepositoHoje['total_valor'] ? $rowDepositoHoje['total_valor'] : 0;
} else {
    $valorDepositoAprovadoHoje = 0;
}

// Valor total de depósito líquido aprovado no mês (PAID_OUT)
$sqlDepositoMes = "SELECT SUM(deposito_liquido) as total_valor FROM solicitacoes WHERE status = 'PAID_OUT' AND DATE_FORMAT(real_data, '%Y-%m') = '$mesAtual'";
$resultDepositoMes = $conn->query($sqlDepositoMes);

if ($resultDepositoMes->num_rows > 0) {
    $rowDepositoMes = $resultDepositoMes->fetch_assoc();
    $valorDepositoAprovadoMes = $rowDepositoMes['total_valor'] ? $rowDepositoMes['total_valor'] : 0;
} else {
    $valorDepositoAprovadoMes = 0;
}

// Valor total de depósito líquido aprovado (PAID_OUT)
$sqlDepositoTotal = "SELECT SUM(deposito_liquido) as total_valor FROM solicitacoes WHERE status = 'PAID_OUT'";
$resultDepositoTotal = $conn->query($sqlDepositoTotal);

if ($resultDepositoTotal->num_rows > 0) {
    $rowDepositoTotal = $resultDepositoTotal->fetch_assoc();
    $valorDepositoAprovadoTotal = $rowDepositoTotal['total_valor'] ? $rowDepositoTotal['total_valor'] : 0;
} else {
    $valorDepositoAprovadoTotal = 0;
}

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
                            <p class="fw-medium fs-20 mb-0">Transações Aprovadas de entrada</p>
                        </div>
</div>









<!-- Start:: row-1 -->
<div class="row">
    <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
        <div class="card custom-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div>
                            <span class="d-block mb-2">Transações aprovadas</span>
                            <h5 class="mb-4 fs-4"><?php echo number_format($totalaprovadas); ?></h5>
                        </div>
                        <span class="text-success me-2 fw-medium d-inline-block"></span><span class="text-muted">Total</span>
                    </div>
                    <div>
                        <div class="main-card-icon success">
                            <div class="avatar avatar-lg bg-success-transparent border border-success border-opacity-10">
                                <div class="avatar avatar-sm svg-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256"><rect width="256" height="256" fill="none"></rect><path d="M40,192a16,16,0,0,0,16,16H216a8,8,0,0,0,8-8V88a8,8,0,0,0-8-8H56A16,16,0,0,1,40,64Z" opacity="0.2"></path><path d="M40,64V192a16,16,0,0,0,16,16H216a8,8,0,0,0,8-8V88a8,8,0,0,0-8-8H56A16,16,0,0,1,40,64h0A16,16,0,0,1,56,48H192" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"></path><circle cx="180" cy="140" r="12"></circle></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
        <div class="card custom-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div>
                            <span class="d-block mb-2">Transações aprovadas</span>
                            <h5 class="mb-4 fs-4"><?php echo number_format($totalaprovadasHoje); ?></h5>
                        </div>
                        <span class="text-success me-2 fw-medium d-inline-block"></span><span class="text-muted">Hoje</span>
                    </div>
                    <div>
                        <div class="main-card-icon success">
                            <div class="avatar avatar-lg bg-success-transparent border border-success border-opacity-10">
                                <div class="avatar avatar-sm svg-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256"><rect width="256" height="256" fill="none"></rect><path d="M40,192a16,16,0,0,0,16,16H216a8,8,0,0,0,8-8V88a8,8,0,0,0-8-8H56A16,16,0,0,1,40,64Z" opacity="0.2"></path><path d="M40,64V192a16,16,0,0,0,16,16H216a8,8,0,0,0,8-8V88a8,8,0,0,0-8-8H56A16,16,0,0,1,40,64h0A16,16,0,0,1,56,48H192" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"></path><circle cx="180" cy="140" r="12"></circle></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
        <div class="card custom-card main-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div>
                            <span class="d-block mb-2">Transações aprovadas</span>
                            <h5 class="mb-4 fs-4"><?php echo number_format($totalaprovadasMes); ?></h5>
                        </div>
                        <span class="text-success me-2 fw-medium d-inline-block"></span><span class="text-muted">Mês</span>
                    </div>
                    <div>
                        <div class="main-card-icon secondary">
                            <div class="avatar avatar-lg bg-secondary-transparent border border-secondary border-opacity-10">
                                <div class="avatar avatar-sm svg-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#000000" viewBox="0 0 256 256"><path d="M216,72H56a8,8,0,0,1,0-16H192a8,8,0,0,0,0-16H56A24,24,0,0,0,32,64V192a24,24,0,0,0,24,24H216a16,16,0,0,0,16-16V88A16,16,0,0,0,216,72Zm0,128H56a8,8,0,0,1-8-8V86.63A23.84,23.84,0,0,0,56,88H216Zm-48-60a12,12,0,1,1,12,12A12,12,0,0,1,168,140Z"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
        <div class="card custom-card main-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div>
                            <span class="d-block mb-2">Transações geral</span>
                            <h5 class="mb-4 fs-4"><?php echo number_format($totalsolicitacoes); ?></h5>
                        </div>
                        <span class="text-danger me-2 fw-medium d-inline-block"></span><span class="text-muted">Total Pendente + Aprovada</span>
                    </div>
                    <div>
                        <div class="main-card-icon orange">
                            <div class="avatar avatar-lg avatar-rounded bg-primary-transparent svg-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#000000" viewBox="0 0 256 256"><path d="M224,200h-8V40a8,8,0,0,0-8-8H152a8,8,0,0,0-8,8V80H96a8,8,0,0,0-8,8v40H48a8,8,0,0,0-8,8v64H32a8,8,0,0,0,0,16H224a8,8,0,0,0,0-16ZM160,48h40V200H160ZM104,96h40V200H104ZM56,144H88v56H56Z"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End:: row-1 -->





<!-- Start:: row-2 -->
<div class="row">
    <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12">
        <div class="card custom-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div>
                            <span class="d-block mb-2">Valor aprovado</span>
                            <h5 class="mb-4 fs-4"><?php echo number_format($valorAprovadoTotal, 2, ',', '.'); ?></h5>
                        </div>
                        <span class="text-success me-2 fw-medium d-inline-block"></span><span class="text-muted">Total Bruto</span>
                    </div>
                    <div>
                        <div class="main-card-icon success">
                            <div class="avatar avatar-lg bg-success-transparent border border-success border-opacity-10">
                                <div class="avatar avatar-sm svg-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256"><rect width="256" height="256" fill="none"></rect><path d="M40,192a16,16,0,0,0,16,16H216a8,8,0,0,0,8-8V88a8,8,0,0,0-8-8H56A16,16,0,0,1,40,64Z" opacity="0.2"></path><path d="M40,64V192a16,16,0,0,0,16,16H216a8,8,0,0,0,8-8V88a8,8,0,0,0-8-8H56A16,16,0,0,1,40,64h0A16,16,0,0,1,56,48H192" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"></path><circle cx="180" cy="140" r="12"></circle></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12">
        <div class="card custom-card main-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div>
                            <span class="d-block mb-2">Valor aprovado</span>
                            <h5 class="mb-4 fs-4"><?php echo number_format($valorAprovadoHoje, 2, ',', '.'); ?></h5>
                        </div>
                        <span class="text-success me-2 fw-medium d-inline-block"></span><span class="text-muted">Hoje Bruto</span>
                    </div>
                    <div>
                        <div class="main-card-icon success">
                            <div class="avatar avatar-lg bg-success-transparent border border-success border-opacity-10">
                                <div class="avatar avatar-sm svg-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#000000" viewBox="0 0 256 256"><path d="M200,168a48.05,48.05,0,0,1-48,48H136v16a8,8,0,0,1-16,0V216H104a48.05,48.05,0,0,1-48-48,8,8,0,0,1,16,0,32,32,0,0,0,32,32h48a32,32,0,0,0,0-64H112a48,48,0,0,1,0-96h8V24a8,8,0,0,1,16,0V40h8a48.05,48.05,0,0,1,48,48,8,8,0,0,1-16,0,32,32,0,0,0-32-32H112a32,32,0,0,0,0,64h40A48.05,48.05,0,0,1,200,168Z"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12">
        <div class="card custom-card main-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div>
                            <span class="d-block mb-2">Valor aprovado</span>
                            <h5 class="mb-4 fs-4"><?php echo number_format($valorAprovadoMes, 2, ',', '.'); ?></h5>
                        </div>
                        <span class="text-success me-2 fw-medium d-inline-block"></span><span class="text-muted">Mês Bruto</span>
                    </div>
                    <div>
                        <div class="main-card-icon secondary">
                            <div class="avatar avatar-lg bg-secondary-transparent border border-secondary border-opacity-10">
                                <div class="avatar avatar-sm svg-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#000000" viewBox="0 0 256 256"><path d="M216,72H56a8,8,0,0,1,0-16H192a8,8,0,0,0,0-16H56A24,24,0,0,0,32,64V192a24,24,0,0,0,24,24H216a16,16,0,0,0,16-16V88A16,16,0,0,0,216,72Zm0,128H56a8,8,0,0,1-8-8V86.63A23.84,23.84,0,0,0,56,88H216Zm-48-60a12,12,0,1,1,12,12A12,12,0,0,1,168,140Z"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- End:: row-2 -->


<!-- Start:: row-3 -->
<div class="row">
    <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12">
        <div class="card custom-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div>
                            <span class="d-block mb-2">Valor aprovado</span>
                            <h5 class="mb-4 fs-4"><?php echo number_format($valorDepositoAprovadoTotal, 2, ',', '.'); ?></h5>
                        </div>
                        <span class="text-success me-2 fw-medium d-inline-block"></span><span class="text-muted">Total liquido</span>
                    </div>
                    <div>
                        <div class="main-card-icon success">
                            <div class="avatar avatar-lg bg-success-transparent border border-success border-opacity-10">
                                <div class="avatar avatar-sm svg-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256"><rect width="256" height="256" fill="none"></rect><path d="M40,192a16,16,0,0,0,16,16H216a8,8,0,0,0,8-8V88a8,8,0,0,0-8-8H56A16,16,0,0,1,40,64Z" opacity="0.2"></path><path d="M40,64V192a16,16,0,0,0,16,16H216a8,8,0,0,0,8-8V88a8,8,0,0,0-8-8H56A16,16,0,0,1,40,64h0A16,16,0,0,1,56,48H192" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"></path><circle cx="180" cy="140" r="12"></circle></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12">
        <div class="card custom-card main-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div>
                            <span class="d-block mb-2">Valor aprovado</span>
                            <h5 class="mb-4 fs-4"><?php echo number_format($valorDepositoAprovadoHoje, 2, ',', '.'); ?></h5>
                        </div>
                        <span class="text-success me-2 fw-medium d-inline-block"></span><span class="text-muted">Hoje liquido</span>
                    </div>
                    <div>
                        <div class="main-card-icon success">
                            <div class="avatar avatar-lg bg-success-transparent border border-success border-opacity-10">
                                <div class="avatar avatar-sm svg-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#000000" viewBox="0 0 256 256"><path d="M200,168a48.05,48.05,0,0,1-48,48H136v16a8,8,0,0,1-16,0V216H104a48.05,48.05,0,0,1-48-48,8,8,0,0,1,16,0,32,32,0,0,0,32,32h48a32,32,0,0,0,0-64H112a48,48,0,0,1,0-96h8V24a8,8,0,0,1,16,0V40h8a48.05,48.05,0,0,1,48,48,8,8,0,0,1-16,0,32,32,0,0,0-32-32H112a32,32,0,0,0,0,64h40A48.05,48.05,0,0,1,200,168Z"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12">
        <div class="card custom-card main-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div>
                            <span class="d-block mb-2">Valor aprovado</span>
                            <h5 class="mb-4 fs-4"><?php echo number_format($valorDepositoAprovadoMes, 2, ',', '.'); ?></h5>
                        </div>
                        <span class="text-success me-2 fw-medium d-inline-block"></span><span class="text-muted">Mês liquido</span>
                    </div>
                    <div>
                        <div class="main-card-icon secondary">
                            <div class="avatar avatar-lg bg-secondary-transparent border border-secondary border-opacity-10">
                                <div class="avatar avatar-sm svg-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#000000" viewBox="0 0 256 256"><path d="M216,72H56a8,8,0,0,1,0-16H192a8,8,0,0,0,0-16H56A24,24,0,0,0,32,64V192a24,24,0,0,0,24,24H216a16,16,0,0,0,16-16V88A16,16,0,0,0,216,72Zm0,128H56a8,8,0,0,1-8-8V86.63A23.84,23.84,0,0,0,56,88H216Zm-48-60a12,12,0,1,1,12,12A12,12,0,0,1,168,140Z"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- End:: row-3 -->













<!-- Inclua os CSS e JS do Flatpickr (ou outra biblioteca de date picker que você preferir) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<?php
session_start();

include '../conectarbanco.php';

// Conectar ao banco de dados
$conn = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name']);

// Verifique a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Configurações de paginação
$limit = 100; // Número de registros por página
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Página atual
$offset = ($page - 1) * $limit;

// Filtros de data
$dataInicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '';
$dataFim = isset($_GET['data_fim']) ? $_GET['data_fim'] : '';


// Consulta para obter a soma filtrada com status PAID_OUT
$sqlFilteredTotal = "SELECT SUM(deposito_liquido) AS total_entrada_liquido_filtrado, SUM(amount) AS total_entrada_bruto_filtrada 
                     FROM solicitacoes WHERE status = 'PAID_OUT'";
if (!empty($dataInicio) && !empty($dataFim)) {
    $sqlFilteredTotal .= " AND real_data BETWEEN ? AND ?";
}
$stmtFilteredTotal = $conn->prepare($sqlFilteredTotal);
if (!empty($dataInicio) && !empty($dataFim)) {
    $stmtFilteredTotal->bind_param("ss", $dataInicio, $dataFim);
}
$stmtFilteredTotal->execute();
$filteredResult = $stmtFilteredTotal->get_result();
$filteredRow = $filteredResult->fetch_assoc();
$total_entrada_liquido_filtrado = $filteredRow['total_entrada_liquido_filtrado'] ?: 0;
$total_entrada_bruto_filtrada = $filteredRow['total_entrada_bruto_filtrada'] ?: 0;

$lucro_plataforma_filtrada = $total_entrada_bruto_filtrada - $total_entrada_liquido_filtrado;


// Consulta para obter o número total de registros, ajustando para o filtro de datas
$sqlCount = "SELECT COUNT(*) AS total FROM solicitacoes WHERE 1=1";
if (!empty($dataInicio) && !empty($dataFim)) {
    $sqlCount .= " AND real_data BETWEEN ? AND ?";
}
$stmtCount = $conn->prepare($sqlCount);
if (!empty($dataInicio) && !empty($dataFim)) {
    $stmtCount->bind_param("ss", $dataInicio, $dataFim);
}
$stmtCount->execute();
$totalResult = $stmtCount->get_result();
$totalRow = $totalResult->fetch_assoc();
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $limit);

// Consulta para obter os registros com paginação e filtro de data
$sql = "SELECT * FROM solicitacoes WHERE 1=1";
if (!empty($dataInicio) && !empty($dataFim)) {
    $sql .= " AND real_data BETWEEN ? AND ?";
}
$sql .= " ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
if (!empty($dataInicio) && !empty($dataFim)) {
    $stmt->bind_param("ssii", $dataInicio, $dataFim, $limit, $offset);
} else {
    $stmt->bind_param("ii", $limit, $offset);
}
$stmt->execute();
$result = $stmt->get_result();



// Consulta para obter os 3 usuários com maior faturamento
$sqlTopUsers = "SELECT u.user_id, u.email, u.telefone, SUM(s.amount) AS total_faturamento 
                 FROM users u 
                 JOIN solicitacoes s ON u.user_id = s.user_id 
                 WHERE s.status = 'PAID_OUT' 
                 GROUP BY u.user_id, u.email, u.telefone 
                 ORDER BY total_faturamento DESC 
                 LIMIT 3";
$stmtTopUsers = $conn->prepare($sqlTopUsers);
$stmtTopUsers->execute();
$resultTopUsers = $stmtTopUsers->get_result();
?>

<!-- Start::row-2 -->
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header justify-content-between d-flex align-items-center">
                <div class="card-title">
                    Relatório de Transações de Entrada
                </div>
                <!-- Botão que abre o modal para escolher as datas -->
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#dateFilterModal">
                    Filtrar por Data
                </button>
            </div>

<!-- End:: row-1 -->


            <div class="card-body">

      
    <div class="alert alert-info">
    <strong>Total Entrada Bruta:</strong> R$ <?= number_format($total_entrada_bruto_filtrada, 2, ',', '.') ?><br>
        <strong>Total Entrada Liquido: </strong> R$ <?= number_format($total_entrada_liquido_filtrado, 2, ',', '.') ?><br>
        <strong>Total Lucro para plataforma:</strong> R$ <?= number_format($lucro_plataforma_filtrada, 2, ',', '.') ?>
    </div>


    <h5>Top 3 Usuários com Mais Faturamento</h5>
                <div class="alert alert-success">
                    <ul>
                        <?php if ($resultTopUsers->num_rows > 0): ?>
                            <?php while ($topUser = $resultTopUsers->fetch_assoc()): ?>
                                <li>
                                    <strong>User:</strong> <?= $topUser['user_id'] ?> | 
                                    <strong>Total Faturamento:</strong> R$ <?= number_format($topUser['total_faturamento'], 2, ',', '.') ?> | 
                                    <a href="https://wa.me/55<?= preg_replace('/[^0-9]/', '', $topUser['telefone']); ?>" target="_blank" class="btn btn-sm btn-info">WhatsApp</a>
                                <br></li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li>Nenhum usuário encontrado</li>
                        <?php endif; ?>
                    </ul>
                </div>



                <div class="table-responsive">
                    <table class="table text-nowrap table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">User ID</th>
                                <th scope="col">Referência Externa</th>
                                <th scope="col">Valor</th>
                                <th scope="col">Valor Líquido</th>
                                <th scope="col">ID da Transação</th>
                                <th scope="col">Status</th>
                                <th scope="col">Nome do Cliente</th>
                                <th scope="col">Documento do Cliente</th>
                                <th scope="col">Email do Cliente</th>
                                <th scope="col">Data</th>
                                <th scope="col">Código de Pagamento</th>
                                <th scope="col">Adquirente</th>
                                <th scope="col">Taxa Cash In</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                // Itera sobre os resultados e exibe cada linha na tabela
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>{$row['user_id']}</td>";
                                    echo "<td>{$row['externalreference']}</td>";
                                    echo "<td>{$row['amount']}</td>";
                                    echo "<td>{$row['deposito_liquido']}</td>";
                                    echo "<td>{$row['idtransaction']}</td>";
                                    
                                    // Ajustar a exibição do status
                                    switch ($row['status']) {
                                        case 'PAID_OUT':
                                            $statusBadge = 'bg-success-transparent';
                                            $statusText = 'Aprovado';
                                            break;
                                        case 'WAITING_FOR_APPROVAL':
                                            $statusBadge = 'bg-warning-transparent';
                                            $statusText = 'Pendente';
                                            break;
                                        default:
                                            $statusBadge = 'bg-danger-transparent';
                                            $statusText = 'Cancelado';
                                    }
                                    echo "<td><span class='badge {$statusBadge}'>{$statusText}</span></td>";

                                    echo "<td>{$row['client_name']}</td>";
                                    echo "<td>{$row['client_document']}</td>";
                                    echo "<td>{$row['client_email']}</td>";
                                    echo "<td>{$row['real_data']}</td>";
                                    echo "<td>{$row['paymentcode']}</td>";
                                    echo "<td>{$row['adquirente_ref']}</td>";
                                    echo "<td>{$row['taxa_cash_in']}</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='13'>Nenhum registro encontrado</td></tr>";
                            }
                            $stmt->close();
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?>&data_inicio=<?= htmlspecialchars($dataInicio) ?>&data_fim=<?= htmlspecialchars($dataFim) ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&data_inicio=<?= htmlspecialchars($dataInicio) ?>&data_fim=<?= htmlspecialchars($dataFim) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?>&data_inicio=<?= htmlspecialchars($dataInicio) ?>&data_fim=<?= htmlspecialchars($dataFim) ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>




<div class="modal fade" id="dateFilterModal" tabindex="-1" role="dialog" aria-labelledby="dateFilterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg">
            <form method="GET" action="">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title" id="dateFilterModalLabel">Filtrar por Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="data_inicio">Data Início:</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                             
                            </div>
                            <input type="text" id="data_inicio" name="data_inicio" class="form-control flatpickr" placeholder="Selecione a data de início" aria-label="Data de Início">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="data_fim">Data Fim:</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                            </div>
                            <input type="text" id="data_fim" name="data_fim" class="form-control flatpickr" placeholder="Selecione a data de fim" aria-label="Data de Fim">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="submit" class="btn btn-success">Filtrar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr('.flatpickr', {
        enableTime: false,
        dateFormat: "Y-m-d",
        locale: "pt"
    });
</script>














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



