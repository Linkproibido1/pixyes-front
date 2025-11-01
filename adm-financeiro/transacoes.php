

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

// Consulta para obter o total de valores de depósitos com status PAID_OUT hoje
$sqlPaidOutToday = "SELECT SUM(CAST(valor AS DECIMAL(10,2))) AS paid_out_today FROM confirmar_deposito WHERE status = 'PAID_OUT' AND data BETWEEN ? AND ?";
$stmtPaidOutToday = $conn->prepare($sqlPaidOutToday);
$stmtPaidOutToday->bind_param("ss", $todayStart, $todayEnd);
$stmtPaidOutToday->execute();
$resultPaidOutToday = $stmtPaidOutToday->get_result();
$rowPaidOutToday = $resultPaidOutToday->fetch_assoc();
$depositsPaidOutToday = $rowPaidOutToday['paid_out_today'];

// Consulta para obter o total de valores de depósitos com status PAID_OUT no mês
$sqlPaidOutMonth = "SELECT SUM(CAST(valor AS DECIMAL(10,2))) AS paid_out_month FROM confirmar_deposito WHERE status = 'PAID_OUT' AND data >= ?";
$stmtPaidOutMonth = $conn->prepare($sqlPaidOutMonth);
$stmtPaidOutMonth->bind_param("s", $startOfMonth);
$stmtPaidOutMonth->execute();
$resultPaidOutMonth = $stmtPaidOutMonth->get_result();
$rowPaidOutMonth = $resultPaidOutMonth->fetch_assoc();
$depositsPaidOutMonth = $rowPaidOutMonth['paid_out_month'];

// Consulta para obter o total de valores de depósitos com status PAID_OUT no total
$sqlPaidOutTotal = "SELECT SUM(CAST(valor AS DECIMAL(10,2))) AS paid_out_total FROM confirmar_deposito WHERE status = 'PAID_OUT'";
$resultPaidOutTotal = $conn->query($sqlPaidOutTotal);
$rowPaidOutTotal = $resultPaidOutTotal->fetch_assoc();
$depositsPaidOutTotal = $rowPaidOutTotal['paid_out_total'];

// Consulta para obter o total de valores de depósitos com status PAID_OUT e WAITING_FOR_APPROVAL
$sqlPixGenerated = "SELECT SUM(CAST(valor AS DECIMAL(10,2))) AS pix_generated FROM confirmar_deposito WHERE status IN ('PAID_OUT', 'WAITING_FOR_APPROVAL')";
$resultPixGenerated = $conn->query($sqlPixGenerated);
$rowPixGenerated = $resultPixGenerated->fetch_assoc();
$pixGeneratedTotal = $rowPixGenerated['pix_generated'];

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
                            <p class="fw-medium fs-20 mb-0">Transações Aprovadas</p>
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
                            <span class="d-block mb-2">Transdações</span>
                            <h5 class="mb-4 fs-4"><?php echo number_format($total_saldo, 2, ',', '.'); ?></h5>
                        </div>
                        <span class="text-success me-2 fw-medium d-inline-block"></span><span class="text-muted">Total de numero de transações aprovadas, cash in & cash out</span>
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
                            <span class="d-block mb-2">Lucro Liquido</span>
                            <h5 class="mb-4 fs-4"><?php echo number_format($total_saldo, 2, ',', '.'); ?></h5>
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
                            <span class="d-block mb-2">Lucro Liquido</span>
                            <h5 class="mb-4 fs-4"><?php echo number_format($total_month, 2, ',', '.'); ?></h5>
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
                            <span class="d-block mb-2">Lucro Liquido</span>
                            <h5 class="mb-4 fs-4"><?php echo number_format($total, 2, ',', '.'); ?></h5>
                        </div>
                        <span class="text-danger me-2 fw-medium d-inline-block"></span><span class="text-muted">TOTAL</span>
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
    <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
        <div class="card custom-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div>
                            <span class="d-block mb-2">Transações aprovadas</span>
                            <h5 class="mb-4 fs-4"><?php echo number_format($total_saldo, 2, ',', '.'); ?></h5>
                        </div>
                        <span class="text-success me-2 fw-medium d-inline-block"></span><span class="text-muted">Total </span>
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
                            <span class="d-block mb-2">Valor aprovado</span>
                            <h5 class="mb-4 fs-4"><?php echo number_format($total_today, 2, ',', '.'); ?></h5>
                        </div>
                        <span class="text-success me-2 fw-medium d-inline-block"></span><span class="text-muted">Hoje</span>
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
    <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
        <div class="card custom-card main-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div>
                            <span class="d-block mb-2">Valor aprovado</span>
                            <h5 class="mb-4 fs-4"><?php echo number_format($total_month, 2, ',', '.'); ?></h5>
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
                            <span class="d-block mb-2">Valor aprovado</span>
                            <h5 class="mb-4 fs-4"><?php echo number_format($total, 2, ',', '.'); ?></h5>
                        </div>
                        <span class="text-danger me-2 fw-medium d-inline-block"></span><span class="text-muted">TOTAL</span>
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




                    <?php
session_start();

include '../conectarbanco.php';

// Obter o e-mail da sessão
$email = $_SESSION['email'];

// Conectar ao banco de dados
$conn = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name']);

// Verifique a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Configurações de paginação
$limit = 10; // Número de registros por página
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Página atual
$offset = ($page - 1) * $limit;

// Consulta para obter o número total de registros
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM confirmar_deposito");
$totalRow = $totalResult->fetch_assoc();
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $limit);

// Consulta para obter os registros com paginação
$sql = "SELECT email, externalreference, valor, status, data 
        FROM confirmar_deposito 
        ORDER BY data DESC 
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<!-- Start::row-2 -->
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">
                    Confirmações de Depósito
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table text-nowrap table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">Email</th>
                                <th scope="col">Referência Externa</th>
                                <th scope="col">Valor</th>
                                <th scope="col">Status</th>
                                <th scope="col">Data</th>
                                <th scope="col">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                // Itera sobre os resultados e exibe cada linha na tabela
                                while($row = $result->fetch_assoc()) {
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
                                    echo "<tr>";
                                    echo "<td>{$row['email']}</td>";
                                    echo "<td>{$row['externalreference']}</td>";
                                    echo "<td>{$row['valor']}</td>";
                                    echo "<td><span class='badge {$statusBadge}'>{$statusText}</span></td>";
                                    echo "<td>{$row['data']}</td>";
                                    echo "<td>
                                        <button class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#editModal' data-email='{$row['email']}' data-externalreference='{$row['externalreference']}' data-valor='{$row['valor']}' data-status='{$row['status']}' data-data='{$row['data']}'>Editar</button>
                                        <button class='btn btn-danger btn-sm' data-bs-toggle='modal' data-bs-target='#deleteModal' data-email='{$row['email']}'>Excluir</button>
                                    </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>Nenhum registro encontrado</td></tr>";
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
                            <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- End::row-2 -->

<!-- Modal Editar -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Editar Confirmação de Depósito</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" id="editEmail" name="email">
                    <div class="mb-3">
                        <label for="editExternalReference" class="form-label">Referência Externa</label>
                        <input type="text" class="form-control" id="editExternalReference" name="externalreference">
                    </div>
                    <div class="mb-3">
                        <label for="editValor" class="form-label">Valor</label>
                        <input type="text" class="form-control" id="editValor" name="valor">
                    </div>
                    <div class="mb-3">
                        <label for="editStatus" class="form-label">Status</label>
                        <select class="form-select" id="editStatus" name="status">
                            <option value="WAITING_FOR_APPROVAL">Pendente</option>
                            <option value="PAID_OUT">Aprovado</option>
                            <option value="CANCELLED">Cancelado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editData" class="form-label">Data</label>
                        <input type="text" class="form-control" id="editData" name="data">
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar alterações</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmar Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Você tem certeza que deseja excluir este depósito?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Excluir</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript para Preencher o Modal e Enviar Alterações -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    var editModal = document.getElementById('editModal');
    var deleteModal = document.getElementById('deleteModal');
    var emailToDelete = null;

    // Preencher o modal de edição
    editModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        document.getElementById('editEmail').value = button.getAttribute('data-email');
        document.getElementById('editExternalReference').value = button.getAttribute('data-externalreference');
        document.getElementById('editValor').value = button.getAttribute('data-valor');
        document.getElementById('editStatus').value = button.getAttribute('data-status');
        document.getElementById('editData').value = button.getAttribute('data-data');
    });

    // Enviar o formulário de edição
    document.getElementById('editForm').addEventListener('submit', function (event) {
        event.preventDefault();
        var formData = new FormData(this);
        fetch('update_confirmar_deposito.php', {
            method: 'POST',
            body: formData
        }).then(response => response.text())
          .then(result => {
              console.log(result); // Para depuração
              if (result === 'success') {
                  window.location.reload();
              } else {
                  alert('Erro ao atualizar depósito.');
              }
          });
    });

    // Definir o email do depósito para exclusão
    deleteModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        emailToDelete = button.getAttribute('data-email');
    });

    // Confirmar a exclusão
    document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
        fetch('delete_confirmar_deposito.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'email': emailToDelete
            })
        }).then(response => response.text())
          .then(result => {
              console.log(result); // Para depuração
              if (result === 'success') {
                  window.location.reload();
              } else {
                  alert('Erro ao excluir depósito.');
              }
          });
    });
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



