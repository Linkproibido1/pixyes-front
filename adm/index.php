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

//calculos dos widgets
$sqlTotalCarteiras = "SELECT SUM(saldo) AS total_saldo FROM users";
$resultTotalCarteiras = $conn->query($sqlTotalCarteiras);

if ($resultTotalCarteiras) {
    $rowTotal = $resultTotalCarteiras->fetch_assoc();
    $total_em_carteiras = $rowTotal['total_saldo'] ?: 0;
} else {
    $total_em_carteiras = 0; 
}

// Obter a data e hora atual
$now = new DateTime();
$dataHoje = date('Y-m-d');
$mesAtual = date('Y-m');
$todayStart = $now->format('Y-m-d 00:00:00'); // Início do dia de hoje
$todayEnd = $now->format('Y-m-d 23:59:59');   // Fim do dia de hoje
$startOfMonth = $now->format('Y-m-01 00:00:00'); // Início do mês
$startOfWeek = $now->modify('monday this week')->format('Y-m-d 00:00:00'); // Início da semana

// Reconfigurar a data atual para manter a mesma instância de DateTime
$now = new DateTime();

// Consulta para obter o total de valores de depósitos com status PAID_OUT hoje
$sqlPaidOutToday = "SELECT SUM(CAST(amount AS DECIMAL(10,2)))-taxa_cash_in AS paid_out_today FROM solicitacoes WHERE status = 'PAID_OUT' AND real_data BETWEEN ? AND ?";
$stmtPaidOutToday = $conn->prepare($sqlPaidOutToday);
$stmtPaidOutToday->bind_param("ss", $todayStart, $todayEnd);
$stmtPaidOutToday->execute();
$resultPaidOutToday = $stmtPaidOutToday->get_result();
$rowPaidOutToday = $resultPaidOutToday->fetch_assoc();
$depositsPaidOutToday = $rowPaidOutToday['paid_out_today'];

// Consulta para obter o total de valores de depósitos com status PAID_OUT no mês
$sqlPaidOutMonth = "SELECT SUM(CAST(amount AS DECIMAL(10,2)))-taxa_cash_in AS paid_out_month FROM solicitacoes WHERE status = 'PAID_OUT' AND real_data >= ?";
$stmtPaidOutMonth = $conn->prepare($sqlPaidOutMonth);
$stmtPaidOutMonth->bind_param("s", $startOfMonth);
$stmtPaidOutMonth->execute();
$resultPaidOutMonth = $stmtPaidOutMonth->get_result();
$rowPaidOutMonth = $resultPaidOutMonth->fetch_assoc();
$depositsPaidOutMonth = $rowPaidOutMonth['paid_out_month'];

// Consulta para obter o total de valores de depósitos com status PAID_OUT no total
$sqlPaidOutTotal = "SELECT SUM(CAST(amount AS DECIMAL(10,2))) AS paid_out_total FROM solicitacoes WHERE status = 'PAID_OUT'";
$resultPaidOutTotal = $conn->query($sqlPaidOutTotal);
$rowPaidOutTotal = $resultPaidOutTotal->fetch_assoc();
$depositsPaidOutTotal = $rowPaidOutTotal['paid_out_total'];

// Consulta para obter o total de valores de depósitos com status PAID_OUT no total
$sqlPaidOutTotalLiq = "SELECT SUM(CAST(amount AS DECIMAL(10,2)))-taxa_cash_in AS paid_out_total FROM solicitacoes WHERE status = 'PAID_OUT'";
$resultPaidOutTotal = $conn->query($sqlPaidOutTotalLiq);
$depositsPaidOutLiq = $resultPaidOutTotal->fetch_assoc()['paid_out_total'];

// Consulta para obter o total de valores de depósitos com status PAID_OUT e WAITING_FOR_APPROVAL
$sqlPixGenerated = "SELECT SUM(CAST(amount AS DECIMAL(10,2))) AS pix_generated FROM solicitacoes WHERE status IN ('PAID_OUT', 'WAITING_FOR_APPROVAL')";
$resultPixGenerated = $conn->query($sqlPixGenerated);
$rowPixGenerated = $resultPixGenerated->fetch_assoc();
$pixGeneratedTotal = $rowPixGenerated['pix_generated'];


// Contagem de transações aprovadas hoje
$sqlDia = "SELECT SUM(amount) as total FROM solicitacoes_cash_out WHERE status = 'COMPLETED' AND DATE(date) = '$dataHoje'";
$resultDia = $conn->query($sqlDia);
if ($resultDia->num_rows > 0) {
    $rowDia = $resultDia->fetch_assoc();
    $totalaprovadasHoje = $rowDia['total'];
} else {
    $totalaprovadasHoje = 0;
}

// Contagem de transações aprovadas no mês
$sqlMes = "SELECT SUM(amount) as total FROM solicitacoes_cash_out WHERE status = 'COMPLETED' AND DATE_FORMAT(date, '%Y-%m') = '$mesAtual'";
$resultMes = $conn->query($sqlMes);
if ($resultMes->num_rows > 0) {
    $rowMes = $resultMes->fetch_assoc();
    $totalaprovadasMes = $rowMes['total'];
} else {
    $totalaprovadasMes = 0;
}

// Contagem total de transações aprovadas
$sqlTotal = "SELECT SUM(amount) as total FROM solicitacoes_cash_out WHERE status = 'COMPLETED'";
$resultTotal = $conn->query($sqlTotal);
if ($resultTotal->num_rows > 0) {
    $rowTotal = $resultTotal->fetch_assoc();
    $totalaprovadas = $rowTotal['total'];
} else {
    $totalaprovadas = 0;
}

// Contagem total de transações aprovadas
$sqlTotal = "SELECT SUM(valor) as total FROM retiradas WHERE status = 0";
$resultTotal = $conn->query($sqlTotal);
if ($resultTotal->num_rows > 0) {
    $rowTotal = $resultTotal->fetch_assoc();
    $totalpendentes = $rowTotal['total'];
} else {
    $totalpendentes = 0;
}

// Contagem total de transações (independente do status)
$sqltotalsolicitacoes = "SELECT SUM(amount) as total FROM solicitacoes_cash_out";
$resulttotalsolicitacoes = $conn->query($sqltotalsolicitacoes);
if ($resulttotalsolicitacoes->num_rows > 0) {
    $rowtotalsolicitacoes = $resulttotalsolicitacoes->fetch_assoc();
    $totalsolicitacoes = $rowtotalsolicitacoes['total'];
} else {
    $totalsolicitacoes = 0;
}

// COntagem de usuarios
$sqltotalusuarios = $conn->query("SELECT COUNT(*) as total FROM users");
if ($sqltotalusuarios->num_rows > 0) {
    $totalusuarios = $sqltotalusuarios->fetch_assoc()['total'];
} else {
    $totalusuarios = 0;
}
// usuarios cadastrados no dia
$sqltotalusuariosHoje = $conn->query("SELECT COUNT(*) as total FROM users WHERE DATE(data_cadastro) = '$dataHoje'");
if ($sqltotalusuariosHoje->num_rows > 0) {
    $totalusuariosHoje = $sqltotalusuariosHoje->fetch_assoc()['total'];
} else {
    $totalusuariosHoje = 0;
}
//usuarios bloqueados
$sqltotalusuariosBlock = $conn->query("SELECT COUNT(*) as total FROM users WHERE status = 3");
if ($sqltotalusuariosBlock->num_rows > 0) {
    $totalusuariosBlock = $sqltotalusuariosBlock->fetch_assoc()['total'];
} else {
    $totalusuariosBlock = 0;
}
//usuarios em analise
$sqltotalusuariosAnalise = $conn->query("SELECT COUNT(*) as total FROM users WHERE status = 0 OR status = 5");
if ($sqltotalusuariosAnalise->num_rows > 0) {
    $totalusuariosAnalise = $sqltotalusuariosAnalise->fetch_assoc()['total'];
} else {
    $totalusuariosAnalise = 0;
}

// Calcular somas dos depósitos aprovados e depósitos líquidos aprovados
$sql_somas = "SELECT SUM(deposito_liquido) as total_liquido, SUM(amount) as total_aprovado 
              FROM solicitacoes 
              WHERE status = 'PAID_OUT'";
$stmt_somas = $conn->query($sql_somas);
$result_somas = $stmt_somas->fetch_assoc();

$sql_somas_mes = "SELECT SUM(deposito_liquido) as total_liquido FROM solicitacoes WHERE status = 'PAID_OUT' AND DATE_FORMAT(real_data, '%Y-%m') = '$mesAtual'";
$stmt_somas_mes = $conn->query($sql_somas_mes);
$result_somas_mes = $stmt_somas_mes->fetch_assoc();

$sql_somas_dia = "SELECT SUM(deposito_liquido) as total_liquido FROM solicitacoes WHERE status = 'PAID_OUT' AND DATE_FORMAT(real_data, '%Y-%m-%d') = '$dataHoje'";
$stmt_somas_dia = $conn->query($sql_somas_dia);
$result_somas_dia = $stmt_somas_dia->fetch_assoc();

$total_liquido = $result_somas['total_liquido'] ? number_format($result_somas['total_liquido'], 2, ',', '.') : '0.00';
$total_aprovado = $result_somas['total_aprovado'] ? number_format($result_somas['total_aprovado'], 2) : '0.00';
$total_liquido_mes = $result_somas_mes['total_liquido'] ? number_format($result_somas_mes['total_liquido'], 2, ',', '.') : '0.00';
$total_liquido_dia = $result_somas_dia['total_liquido'] ? number_format($result_somas_dia['total_liquido'], 2, ',', '.') : '0.00';

// Calcular o lucro da plataforma (depósito - depósito líquido)
$lucro_plataforma = ($result_somas['total_aprovado'] - $result_somas['total_liquido']) ? number_format($result_somas['total_aprovado'] - $result_somas['total_liquido'], 2) : '0.00';

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









        <!-- Start:: row-1 -->
        <div class="row">
            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                <div class="card custom-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div>
                                    <span class="d-block mb-2">Total em carteiras</span>
                                    <h5 class="mb-4 fs-4">R$ <?php echo number_format($total_em_carteiras, 2, ',', '.'); ?></h5>
                                </div>
                                <span class="text-success me-2 fw-medium d-inline-block"></span><span
                                    class="text-muted">Total Nas carterias dos usuários do gateway </span>
                            </div>
                            <div>
                                <div class="main-card-icon success">
                                    <div
                                        class="avatar avatar-lg bg-success-transparent border border-success border-opacity-10">
                                        <div class="avatar avatar-sm svg-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256">
                                                <rect width="256" height="256" fill="none"></rect>
                                                <path
                                                    d="M40,192a16,16,0,0,0,16,16H216a8,8,0,0,0,8-8V88a8,8,0,0,0-8-8H56A16,16,0,0,1,40,64Z"
                                                    opacity="0.2"></path>
                                                <path
                                                    d="M40,64V192a16,16,0,0,0,16,16H216a8,8,0,0,0,8-8V88a8,8,0,0,0-8-8H56A16,16,0,0,1,40,64h0A16,16,0,0,1,56,48H192"
                                                    fill="none" stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="16"></path>
                                                <circle cx="180" cy="140" r="12"></circle>
                                            </svg>
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
                                    <span class="d-block mb-2">Lucro Liquido</span>
                                    <h5 class="mb-4 fs-4">R$ <?php echo $total_liquido_dia; ?></h5>
                                </div>
                                <span class="text-success me-2 fw-medium d-inline-block"></span><span
                                    class="text-muted">Hoje</span>
                            </div>
                            <div>
                                <div class="main-card-icon success">
                                    <div
                                        class="avatar avatar-lg bg-success-transparent border border-success border-opacity-10">
                                        <div class="avatar avatar-sm svg-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256">
                                                <rect width="256" height="256" fill="none"></rect>
                                                <path
                                                    d="M40,192a16,16,0,0,0,16,16H216a8,8,0,0,0,8-8V88a8,8,0,0,0-8-8H56A16,16,0,0,1,40,64Z"
                                                    opacity="0.2"></path>
                                                <path
                                                    d="M40,64V192a16,16,0,0,0,16,16H216a8,8,0,0,0,8-8V88a8,8,0,0,0-8-8H56A16,16,0,0,1,40,64h0A16,16,0,0,1,56,48H192"
                                                    fill="none" stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="16"></path>
                                                <circle cx="180" cy="140" r="12"></circle>
                                            </svg>
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
                                    <span class="d-block mb-2">Lucro Liquido</span>
                                    <h5 class="mb-4 fs-4">R$ <?php echo $total_liquido_mes; ?></h5>
                                </div>
                                <span class="text-success me-2 fw-medium d-inline-block"></span><span
                                    class="text-muted">Mês</span>
                            </div>
                            <div>
                                <div class="main-card-icon secondary">
                                    <div
                                        class="avatar avatar-lg bg-secondary-transparent border border-secondary border-opacity-10">
                                        <div class="avatar avatar-sm svg-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                                fill="#000000" viewBox="0 0 256 256">
                                                <path
                                                    d="M216,72H56a8,8,0,0,1,0-16H192a8,8,0,0,0,0-16H56A24,24,0,0,0,32,64V192a24,24,0,0,0,24,24H216a16,16,0,0,0,16-16V88A16,16,0,0,0,216,72Zm0,128H56a8,8,0,0,1-8-8V86.63A23.84,23.84,0,0,0,56,88H216Zm-48-60a12,12,0,1,1,12,12A12,12,0,0,1,168,140Z">
                                                </path>
                                            </svg>
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
                                    <span class="d-block mb-2">Lucro Liquido</span>
                                    <h5 class="mb-4 fs-4">R$ <?php echo $total_liquido; ?></h5>
                                </div>
                                <span class="text-danger me-2 fw-medium d-inline-block"></span><span
                                    class="text-muted">TOTAL</span>
                            </div>
                            <div>
                                <div class="main-card-icon orange">
                                    <div class="avatar avatar-lg avatar-rounded bg-primary-transparent svg-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#000000"
                                            viewBox="0 0 256 256">
                                            <path
                                                d="M224,200h-8V40a8,8,0,0,0-8-8H152a8,8,0,0,0-8,8V80H96a8,8,0,0,0-8,8v40H48a8,8,0,0,0-8,8v64H32a8,8,0,0,0,0,16H224a8,8,0,0,0,0-16ZM160,48h40V200H160ZM104,96h40V200H104ZM56,144H88v56H56Z">
                                            </path>
                                        </svg>
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
            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                <div class="card custom-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div>
                                    <span class="d-block mb-2">Transações aprovadas</span>
                                    <h5 class="mb-4 fs-4">R$ <?php echo number_format($depositsPaidOutTotal, 2, ',', '.'); ?></h5>
                                </div>
                                <span class="text-success me-2 fw-medium d-inline-block"></span><span
                                    class="text-muted">Total </span>
                            </div>
                            <div>
                                <div class="main-card-icon success">
                                    <div
                                        class="avatar avatar-lg bg-success-transparent border border-success border-opacity-10">
                                        <div class="avatar avatar-sm svg-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256">
                                                <rect width="256" height="256" fill="none"></rect>
                                                <path
                                                    d="M40,192a16,16,0,0,0,16,16H216a8,8,0,0,0,8-8V88a8,8,0,0,0-8-8H56A16,16,0,0,1,40,64Z"
                                                    opacity="0.2"></path>
                                                <path
                                                    d="M40,64V192a16,16,0,0,0,16,16H216a8,8,0,0,0,8-8V88a8,8,0,0,0-8-8H56A16,16,0,0,1,40,64h0A16,16,0,0,1,56,48H192"
                                                    fill="none" stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="16"></path>
                                                <circle cx="180" cy="140" r="12"></circle>
                                            </svg>
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
                                    <span class="d-block mb-2">Valor aprovado</span>
                                    <h5 class="mb-4 fs-4">R$ <?php echo number_format($depositsPaidOutToday, 2, ',', '.'); ?></h5>
                                </div>
                                <span class="text-success me-2 fw-medium d-inline-block"></span><span
                                    class="text-muted">Hoje</span>
                            </div>
                            <div>
                                <div class="main-card-icon success">
                                    <div
                                        class="avatar avatar-lg bg-success-transparent border border-success border-opacity-10">
                                        <div class="avatar avatar-sm svg-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                                fill="#000000" viewBox="0 0 256 256">
                                                <path
                                                    d="M200,168a48.05,48.05,0,0,1-48,48H136v16a8,8,0,0,1-16,0V216H104a48.05,48.05,0,0,1-48-48,8,8,0,0,1,16,0,32,32,0,0,0,32,32h48a32,32,0,0,0,0-64H112a48,48,0,0,1,0-96h8V24a8,8,0,0,1,16,0V40h8a48.05,48.05,0,0,1,48,48,8,8,0,0,1-16,0,32,32,0,0,0-32-32H112a32,32,0,0,0,0,64h40A48.05,48.05,0,0,1,200,168Z">
                                                </path>
                                            </svg>
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
                                    <span class="d-block mb-2">Valor aprovado</span>
                                    <h5 class="mb-4 fs-4">R$ <?php echo number_format($depositsPaidOutMonth, 2, ',', '.'); ?></h5>
                                </div>
                                <span class="text-success me-2 fw-medium d-inline-block"></span><span
                                    class="text-muted">Mês</span>
                            </div>
                            <div>
                                <div class="main-card-icon secondary">
                                    <div
                                        class="avatar avatar-lg bg-secondary-transparent border border-secondary border-opacity-10">
                                        <div class="avatar avatar-sm svg-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                                fill="#000000" viewBox="0 0 256 256">
                                                <path
                                                    d="M216,72H56a8,8,0,0,1,0-16H192a8,8,0,0,0,0-16H56A24,24,0,0,0,32,64V192a24,24,0,0,0,24,24H216a16,16,0,0,0,16-16V88A16,16,0,0,0,216,72Zm0,128H56a8,8,0,0,1-8-8V86.63A23.84,23.84,0,0,0,56,88H216Zm-48-60a12,12,0,1,1,12,12A12,12,0,0,1,168,140Z">
                                                </path>
                                            </svg>
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
                                    <span class="d-block mb-2">Valor aprovado</span>
                                    <h5 class="mb-4 fs-4">R$ <?php echo number_format($depositsPaidOutLiq, 2, ',', '.'); ?></h5>
                                </div>
                                <span class="text-danger me-2 fw-medium d-inline-block"></span><span
                                    class="text-muted">TOTAL</span>
                            </div>
                            <div>
                                <div class="main-card-icon orange">
                                    <div class="avatar avatar-lg avatar-rounded bg-primary-transparent svg-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#000000"
                                            viewBox="0 0 256 256">
                                            <path
                                                d="M224,200h-8V40a8,8,0,0,0-8-8H152a8,8,0,0,0-8,8V80H96a8,8,0,0,0-8,8v40H48a8,8,0,0,0-8,8v64H32a8,8,0,0,0,0,16H224a8,8,0,0,0,0-16ZM160,48h40V200H160ZM104,96h40V200H104ZM56,144H88v56H56Z">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End:: row-1 -->






        <!-- Start:: row-1 -->
        <div class="row">
            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                <div class="card custom-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div>
                                    <span class="d-block mb-2">Usuarios cadastrados</span>
                                    <h5 class="mb-4 fs-4"><?php echo htmlspecialchars($totalusuarios); ?></h5>
                                </div>
                                <span class="text-success me-2 fw-medium d-inline-block"></span><span
                                    class="text-muted">TOTAL</span>
                            </div>
                            <div>
                                <div class="main-card-icon success">
                                    <div
                                        class="avatar avatar-lg bg-success-transparent border border-success border-opacity-10">
                                        <div class="avatar avatar-sm svg-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                                fill="#000000" viewBox="0 0 256 256">
                                                <path
                                                    d="M117.25,157.92a60,60,0,1,0-66.5,0A95.83,95.83,0,0,0,3.53,195.63a8,8,0,1,0,13.4,8.74,80,80,0,0,1,134.14,0,8,8,0,0,0,13.4-8.74A95.83,95.83,0,0,0,117.25,157.92ZM40,108a44,44,0,1,1,44,44A44.05,44.05,0,0,1,40,108Zm210.14,98.7a8,8,0,0,1-11.07-2.33A79.83,79.83,0,0,0,172,168a8,8,0,0,1,0-16,44,44,0,1,0-16.34-84.87,8,8,0,1,1-5.94-14.85,60,60,0,0,1,55.53,105.64,95.83,95.83,0,0,1,47.22,37.71A8,8,0,0,1,250.14,206.7Z">
                                                </path>
                                            </svg>
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




                                    <span class="d-block mb-2">Cadastros</span>
                                    <h5 class="mb-4 fs-4"><?php echo htmlspecialchars($totalusuariosHoje); ?></h5>
                                </div>
                                <span class="text-success me-2 fw-medium d-inline-block"></span><span
                                    class="text-muted">HOJE</span>
                            </div>
                            <div>
                                <div class="main-card-icon success">
                                    <div
                                        class="avatar avatar-lg bg-success-transparent border border-success border-opacity-10">
                                        <div class="avatar avatar-sm svg-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                                fill="#000000" viewBox="0 0 256 256">
                                                <path
                                                    d="M117.25,157.92a60,60,0,1,0-66.5,0A95.83,95.83,0,0,0,3.53,195.63a8,8,0,1,0,13.4,8.74,80,80,0,0,1,134.14,0,8,8,0,0,0,13.4-8.74A95.83,95.83,0,0,0,117.25,157.92ZM40,108a44,44,0,1,1,44,44A44.05,44.05,0,0,1,40,108Zm210.14,98.7a8,8,0,0,1-11.07-2.33A79.83,79.83,0,0,0,172,168a8,8,0,0,1,0-16,44,44,0,1,0-16.34-84.87,8,8,0,1,1-5.94-14.85,60,60,0,0,1,55.53,105.64,95.83,95.83,0,0,1,47.22,37.71A8,8,0,0,1,250.14,206.7Z">
                                                </path>
                                            </svg>
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
                                    <span class="d-block mb-2">Cadastros Bloqueados</span>
                                    <h5 class="mb-4 fs-4"><?php echo htmlspecialchars($totalusuariosBlock); ?></h5>
                                </div>
                                <span class="text-success me-2 fw-medium d-inline-block"></span><span
                                    class="text-muted">Usuarios com conta bloqueada</span>
                            </div>
                            <div>
                                <div class="main-card-icon secondary">
                                    <div
                                        class="avatar avatar-lg bg-secondary-transparent border border-secondary border-opacity-10">
                                        <div class="avatar avatar-sm svg-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                                fill="#000000" viewBox="0 0 256 256">
                                                <path
                                                    d="M117.25,157.92a60,60,0,1,0-66.5,0A95.83,95.83,0,0,0,3.53,195.63a8,8,0,1,0,13.4,8.74,80,80,0,0,1,134.14,0,8,8,0,0,0,13.4-8.74A95.83,95.83,0,0,0,117.25,157.92ZM40,108a44,44,0,1,1,44,44A44.05,44.05,0,0,1,40,108Zm210.14,98.7a8,8,0,0,1-11.07-2.33A79.83,79.83,0,0,0,172,168a8,8,0,0,1,0-16,44,44,0,1,0-16.34-84.87,8,8,0,1,1-5.94-14.85,60,60,0,0,1,55.53,105.64,95.83,95.83,0,0,1,47.22,37.71A8,8,0,0,1,250.14,206.7Z">
                                                </path>
                                            </svg>
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
                                    <span class="d-block mb-2">Cadastros em analise</span>
                                    <h5 class="mb-4 fs-4"><?php echo htmlspecialchars($totalusuariosAnalise); ?></h5>
                                </div>
                                <span class="text-danger me-2 fw-medium d-inline-block">
                                </span><span class="text-muted">Usuarios em analise</span>
                            </div>
                            <div>
                                <div class="main-card-icon orange">
                                    <div class="avatar avatar-lg avatar-rounded bg-primary-transparent svg-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#000000"
                                            viewBox="0 0 256 256">
                                            <path
                                                d="M117.25,157.92a60,60,0,1,0-66.5,0A95.83,95.83,0,0,0,3.53,195.63a8,8,0,1,0,13.4,8.74,80,80,0,0,1,134.14,0,8,8,0,0,0,13.4-8.74A95.83,95.83,0,0,0,117.25,157.92ZM40,108a44,44,0,1,1,44,44A44.05,44.05,0,0,1,40,108Zm210.14,98.7a8,8,0,0,1-11.07-2.33A79.83,79.83,0,0,0,172,168a8,8,0,0,1,0-16,44,44,0,1,0-16.34-84.87,8,8,0,1,1-5.94-14.85,60,60,0,0,1,55.53,105.64,95.83,95.83,0,0,1,47.22,37.71A8,8,0,0,1,250.14,206.7Z">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- End:: row-1 -->



        <!-- Start:: row-1 -->
        <div class="row">
            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                <div class="card custom-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div>
                                    <span class="d-block mb-2">Total Retiradas</span>
                                    <h5 class="mb-4 fs-4">R$ <?php echo number_format($totalaprovadasHoje, 2, ',', '.'); ?></h5>
                                </div>
                                <span class="text-success me-2 fw-medium d-inline-block"></span><span
                                    class="text-muted">Aprovadas hoje </span>
                            </div>
                            <div>
                                <div class="main-card-icon success">
                                    <div
                                        class="avatar avatar-lg bg-success-transparent border border-success border-opacity-10">
                                        <div class="avatar avatar-sm svg-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256">
                                                <rect width="256" height="256" fill="none"></rect>
                                                <path
                                                    d="M40,192a16,16,0,0,0,16,16H216a8,8,0,0,0,8-8V88a8,8,0,0,0-8-8H56A16,16,0,0,1,40,64Z"
                                                    opacity="0.2"></path>
                                                <path
                                                    d="M40,64V192a16,16,0,0,0,16,16H216a8,8,0,0,0,8-8V88a8,8,0,0,0-8-8H56A16,16,0,0,1,40,64h0A16,16,0,0,1,56,48H192"
                                                    fill="none" stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="16"></path>
                                                <circle cx="180" cy="140" r="12"></circle>
                                            </svg>
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
                                    <span class="d-block mb-2">Retiradas</span>
                                    <h5 class="mb-4 fs-4">R$ <?php echo number_format($totalaprovadasMes, 2, ',', '.'); ?></h5>
                                </div>
                                <span class="text-success me-2 fw-medium d-inline-block"></span><span
                                    class="text-muted">Mês</span>
                            </div>
                            <div>
                                <div class="main-card-icon success">
                                    <div
                                        class="avatar avatar-lg bg-success-transparent border border-success border-opacity-10">
                                        <div class="avatar avatar-sm svg-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                                fill="#000000" viewBox="0 0 256 256">
                                                <path
                                                    d="M200,168a48.05,48.05,0,0,1-48,48H136v16a8,8,0,0,1-16,0V216H104a48.05,48.05,0,0,1-48-48,8,8,0,0,1,16,0,32,32,0,0,0,32,32h48a32,32,0,0,0,0-64H112a48,48,0,0,1,0-96h8V24a8,8,0,0,1,16,0V40h8a48.05,48.05,0,0,1,48,48,8,8,0,0,1-16,0,32,32,0,0,0-32-32H112a32,32,0,0,0,0,64h40A48.05,48.05,0,0,1,200,168Z">
                                                </path>
                                            </svg>
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
                                    <span class="d-block mb-2">Retiradas</span>
                                    <h5 class="mb-4 fs-4">R$ <?php echo number_format($totalaprovadas, 2, ',', '.'); ?></h5>
                                </div>
                                <span class="text-success me-2 fw-medium d-inline-block"></span><span
                                    class="text-muted">TOTAL</span>
                            </div>
                            <div>
                                <div class="main-card-icon secondary">
                                    <div
                                        class="avatar avatar-lg bg-secondary-transparent border border-secondary border-opacity-10">
                                        <div class="avatar avatar-sm svg-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                                fill="#000000" viewBox="0 0 256 256">
                                                <path
                                                    d="M216,72H56a8,8,0,0,1,0-16H192a8,8,0,0,0,0-16H56A24,24,0,0,0,32,64V192a24,24,0,0,0,24,24H216a16,16,0,0,0,16-16V88A16,16,0,0,0,216,72Zm0,128H56a8,8,0,0,1-8-8V86.63A23.84,23.84,0,0,0,56,88H216Zm-48-60a12,12,0,1,1,12,12A12,12,0,0,1,168,140Z">
                                                </path>
                                            </svg>
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
                                    <span class="d-block mb-2">Retiradas Pendentes</span>
                                    <h5 class="mb-4 fs-4">R$ <?php echo number_format($totalpendentes, 2, ',', '.'); ?></h5>
                                </div>
                                <span class="text-danger me-2 fw-medium d-inline-block"></span><span
                                    class="text-muted">TOTAL</span>
                            </div>
                            <div>
                                <div class="main-card-icon orange">
                                    <div class="avatar avatar-lg avatar-rounded bg-primary-transparent svg-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#000000"
                                            viewBox="0 0 256 256">
                                            <path
                                                d="M224,200h-8V40a8,8,0,0,0-8-8H152a8,8,0,0,0-8,8V80H96a8,8,0,0,0-8,8v40H48a8,8,0,0,0-8,8v64H32a8,8,0,0,0,0,16H224a8,8,0,0,0,0-16ZM160,48h40V200H160ZM104,96h40V200H104ZM56,144H88v56H56Z">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End:: row-1 -->




    </div>

    <!-- End:: row-3 -->



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