
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
$sql = "SELECT  status, permission FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($status, $permission);
$stmt->fetch();

$stmt->close();
$conn->close();

// ================================== FIM DO CODIGO DE PERMICOES ====================================================
?>























<?php
include '../conectarbanco.php';

$conn = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name']);


if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$sql_aprovados = "SELECT SUM(valor_liquido) AS total_saque_aprovado FROM retiradas WHERE status = '1'";
$result_aprovados = $conn->query($sql_aprovados);
$sql_pendentes = "SELECT SUM(valor_liquido) AS total_saque_pendente FROM retiradas WHERE status = '0'";
$result_pendentes = $conn->query($sql_pendentes);


if ($result_aprovados) {

    $row_aprovados = $result_aprovados->fetch_assoc();

    $total_saque_aprovado = $row_aprovados['total_saque_aprovado'];

} else {
    echo "Erro  " . $conn->error . "<br>";
}

if ($result_pendentes) {

    $row_pendentes = $result_pendentes->fetch_assoc();

    $total_saque_pendente = $row_pendentes['total_saque_pendente'];

} else {
    echo "Erro" . $conn->error;
}

// Feche a conexão
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





            <div class="main-content app-content">
                <div class="container-fluid">

                    <!-- Start::page-header -->
                    <div class="d-flex align-items-center justify-content-between my-4 page-header-breadcrumb flex-wrap gap-2">
                   



                     
                    </div>
                    <!-- End::page-header -->

                    <!-- Start:: row-1 -->
                    <div class="row">
                        <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="card custom-card">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                            <div>
                                                <span class="d-block mb-2">SAQUES APROVADOS</span>
                                                <h5 class="mb-4 fs-4">R$  <?php echo number_format($total_saque_aprovado, 2, ',', '.'); ?></h5>
                                            </div>
                                            <span class="text-success me-2 fw-medium d-inline-block"></span><span class="text-muted">Saques enviados</span>
                                        </div>
                                        <div>
                                            <div class="main-card-icon success">
                                                <div class="avatar avatar-lg bg-success-transparent border border-success border-opacity-10">
                                                    <div class="avatar avatar-sm svg-white">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256"><rect width="256" height="256" fill="none">

                                                    </rect><path d="M40,192a16,16,0,0,0,16,16H216a8,8,0,0,0,8-8V88a8,8,0,0,0-8-8H56A16,16,0,0,1,40,64Z" opacity="0.2"></path><path d="M40,64V192a16,16,0,0,0,16,16H216a8,8,0,0,0,8-8V88a8,8,0,0,0-8-8H56A16,16,0,0,1,40,64h0A16,16,0,0,1,56,48H192" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"></path><circle cx="180" cy="140" r="12"></circle></svg>   </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="card custom-card main-card">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                            <div>
                                                <span class="d-block mb-2">SAQUES PENDENTES</span>
                                                <h5 class="mb-4 fs-4">R$ <?php echo number_format($total_saque_pendente, 2, ',', '.'); ?></h5>
                                            </div>
                                            <span class="text-success me-2 fw-medium d-inline-block"></span><span class="text-muted">Saques em espera</span>
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
                  
                        
                    </div>
                    <!-- End:: row-1 -->










                    <?php
session_start();

// Verificar se o e-mail está presente na sessão
if (!isset($_SESSION['email'])) {
    // Se o e-mail não estiver presente na sessão, redirecione para outra página
    header("Location: ../");
    exit; // Certifique-se de sair do script após o redirecionamento
}

include '../conectarbanco.php';

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

// Consulta para obter o número total de registros com status 'Pendente'
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM retiradas WHERE status = '0'");
$totalRow = $totalResult->fetch_assoc();
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $limit);

// Consulta para obter os registros com status 'Pendente'
$sql = "SELECT id, user_id, referencia, valor, valor_liquido, tipo_chave, chave, status, data_solicitacao, data_pagamento, taxa_cash_out
        FROM retiradas 
        WHERE status = '0'
        ORDER BY data_solicitacao DESC 
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
                    SOLICITAÇÕES DE SAQUE PENDENTES
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table text-nowrap table-bordered">
                        <thead>
                            <tr>    
                                <th scope="col">User ID</th> 
                                <th scope="col">Referência</th>
                                <th scope="col">Valor Líquido</th>
                                <th scope="col">Chave PIX</th>
                                <th scope="col">Status</th>
                                <th scope="col">Data de Solicitação</th>
                                <th scope="col">Data de Pagamento</th>
                                <th scope="col">Taxa de Cash Out</th>
                                <th scope="col">Ações</th> <!-- Nova coluna para ações -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                          if ($result->num_rows > 0) {
                            // Itera sobre os resultados e exibe cada linha na tabela
                            while ($row = $result->fetch_assoc()) {
                                $statusBadge = $row['status'] == '1' ? 'bg-success-transparent' : 'bg-light text-dark';
                                $statusText = $row['status'] == '1' ? 'Pago' : 'Pendente';
                        
                                // Exibe os dados na tabela
                                echo "<tr>";
                                echo "<td>{$row['user_id']}</td>"; 
                                echo "<td>{$row['referencia']}</td>";
                                echo "<td>{$row['valor_liquido']}</td>";
                                echo "<td>{$row['chave']}</td>";
                                echo "<td><span class='badge {$statusBadge}'>{$statusText}</span></td>";
                                echo "<td>{$row['data_solicitacao']}</td>";
                                echo "<td>{$row['data_pagamento']}</td>";
                                echo "<td>{$row['taxa_cash_out']}</td>";
                        
                                // Ações com o user_id correto na URL
                                echo "<td>
                                    <a href='aprovar_solicitacao_saque.php?id={$row['id']}' class='btn btn-success'>Aprovar</a>
                                    <a href='recusar_solicitacao_saque.php?id={$row['id']}&user_id={$row['user_id']}' class='btn btn-danger'>Recusar</a>
                                    <a href='excluir_solicitacao_saque.php?id={$row['id']}&user_id={$row['user_id']}' class='btn btn-warning'>Excluir</a> 
                                </td>";
                                }
                            } else {
                                echo "<tr><td colspan='9'>Nenhum registro encontrado</td></tr>";
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






<?php
session_start();

// Verificar se o e-mail está presente na sessão
if (!isset($_SESSION['email'])) {
    // Se o e-mail não estiver presente na sessão, redirecione para outra página
    header("Location: ../");
    exit; // Certifique-se de sair do script após o redirecionamento
}

include '../conectarbanco.php';

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

// Consulta para obter o número total de registros aprovados
$totalResult = $conn->prepare("SELECT COUNT(*) AS total FROM retiradas WHERE status = '1'");
$totalResult->execute();
$totalRow = $totalResult->get_result()->fetch_assoc();
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $limit);

// Consulta para obter os registros aprovados
$sql = "SELECT id, user_id, referencia, valor, valor_liquido, tipo_chave, chave, status, data_solicitacao, data_pagamento, taxa_cash_out
        FROM retiradas 
        WHERE status = '1'
        ORDER BY data_solicitacao DESC 
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
                    SOLICITAÇÕES DE SAQUE APROVADOS
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table text-nowrap table-bordered">
                        <thead>
                            <tr>    
                                <th scope="col">User ID</th> 
                                <th scope="col">Referência</th>
                                <th scope="col">Valor Líquido</th>
                                <th scope="col">Chave PIX</th>
                                <th scope="col">Status</th>
                                <th scope="col">Data de Solicitação</th>
                                <th scope="col">Data de Pagamento</th>
                                <th scope="col">Taxa de Cash Out</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                // Itera sobre os resultados e exibe cada linha na tabela
                                while ($row = $result->fetch_assoc()) {
                                    $statusBadge = $row['status'] == '1' ? 'bg-success-transparent' : 'bg-light text-dark';
                                    $statusText = $row['status'] == '1' ? 'Pago' : 'Pendente';
                                    echo "<tr>";
                                    echo "<td>{$row['user_id']}</td>"; 
                                    echo "<td>{$row['referencia']}</td>";
                                    echo "<td>{$row['valor_liquido']}</td>";
                                    echo "<td>{$row['chave']}</td>";
                                    echo "<td><span class='badge {$statusBadge}'>{$statusText}</span></td>";
                                    echo "<td>{$row['data_solicitacao']}</td>";
                                    echo "<td>{$row['data_pagamento']}</td>";
                                    echo "<td>{$row['taxa_cash_out']}</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8'>Nenhum registro encontrado</td></tr>";
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






