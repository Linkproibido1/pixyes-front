

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

$user_id_var = $user_id;

$stmt->close();
$conn->close();
?>



<?php
include '../conectarbanco.php';
$conn = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name']);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
$sqlTotalCarteiras = "SELECT SUM(saldo) AS total_saldo FROM users";
$resultTotalCarteiras = $conn->query($sqlTotalCarteiras);

// Verifica se a consulta retornou resultados
if ($resultTotalCarteiras) {
    $rowTotal = $resultTotalCarteiras->fetch_assoc();
    $total_em_carteiras = $rowTotal['total_saldo'] ?: 0;
} else {
    $total_em_carteiras = 0; 
}
$conn->close();

?>




<?php
session_start();

include '../conectarbanco.php';
$conn = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name']);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}


$saquesaprovados = 0; 
$depositosaprovados = 0; 


$sqlSolicitacoes = "SELECT SUM(amount) AS total_paid_out FROM solicitacoes WHERE status = 'PAID_OUT'";
$resultSolicitacoes = $conn->query($sqlSolicitacoes);
$rowSolicitacoes = $resultSolicitacoes->fetch_assoc();
$totalPaidOut = $rowSolicitacoes['total_paid_out'] ?: 0;

$sqlCashOut = "SELECT SUM(amount) AS total_completed FROM solicitacoes_cash_out WHERE status = 'COMPLETED'";
$resultCashOut = $conn->query($sqlCashOut);
$rowCashOut = $resultCashOut->fetch_assoc();
$totalCompleted = $rowCashOut['total_completed'] ?: 0;

// Calcular total bruto no gateway
$totalBrutoGateway = $totalPaidOut - $totalCompleted;

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
                            <p class="fw-medium fs-20 mb-0">Carteiras</p>
                        </div>
</div>





<!-- Start:: row-2 -->
<div class="row">
    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
        <div class="card custom-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div>
                            <span class="d-block mb-2">TOTAL EM CARTEIRAS</span>
                            <h5 class="mb-4 fs-4"><?php echo number_format($total_em_carteiras, 2, ',', '.'); ?></h5>
                        </div>
                        <span class="text-success me-2 fw-medium d-inline-block"></span><span class="text-muted">Total nas carteiras dos usuarios</span>
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
    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
        <div class="card custom-card main-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div>
                            <span class="d-block mb-2">TOTAL NO GATEWAY</span>
                            <h5 class="mb-4 fs-4"><?php echo number_format($totalBrutoGateway, 2, ',', '.'); ?></h5>
                        </div>
                        <span class="text-success me-2 fw-medium d-inline-block"></span><span class="text-muted">valor total no gateway</span>
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
<!-- End:: row-2 -->









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

// Consulta para obter todos os usuários
$sql = "SELECT user_id, saldo, email, telefone FROM users";
$result = $conn->query($sql);

// Consulta para obter os 3 usuários com mais faturamento
$sqlTopUsers = "SELECT user_id, saldo, email, telefone FROM users ORDER BY saldo DESC LIMIT 3";
$resultTopUsers = $conn->query($sqlTopUsers);
?>

<!-- Start::row-2 -->
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header justify-content-between d-flex align-items-center">
                <div class="card-title">
                    Relatório de Usuários
                </div>
            </div>
            <div class="card-body">

                <h5>Top 3 Usuários com Mais saldo em carteira</h5>
                <div class="alert alert-success">
                    <ul>
                        <?php if ($resultTopUsers->num_rows > 0): ?>
                            <?php while ($topUser = $resultTopUsers->fetch_assoc()): ?>
                                <li>
                                    <strong>User:</strong> <?= $topUser['user_id'] ?> | 
                                    <strong>Saldo:</strong> R$ <?= number_format($topUser['saldo'], 2, ',', '.') ?> | 
                               </li>
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
                                <th scope="col">Carteira</th>
                                <th scope="col">Email</th>
                                <th scope="col">Telefone</th>
                                <th scope="col">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                // Itera sobre os resultados e exibe cada linha na tabela
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>{$row['user_id']}</td>";
                                    echo "<td>R$ " . number_format($row['saldo'], 2, ',', '.') . "</td>";
                                    echo "<td>{$row['email']}</td>";
                                    echo "<td>{$row['telefone']}</td>";
                                    echo "<td><a href='https://wa.me/55" . preg_replace('/[^0-9]/', '', $row['telefone']) . "' target='_blank' class='btn btn-sm btn-info'>WhatsApp</a></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>Nenhum registro encontrado</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Encerrar a conexão -->
                <?php $conn->close(); ?>
            </div>
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



