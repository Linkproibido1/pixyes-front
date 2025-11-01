
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










                    
                
                    <?php
include '../conectarbanco.php';
$conn = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name']);
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Consulta para obter todos os usuários
$result = $conn->query("SELECT user_id, email FROM users");

?>

<!-- Start::row-1 -->
<div class="row">
    <div class="col-xl-6">
        <div class="card custom-card">
            <div class="card-body p-0">
                <div class="p-3 d-grid border-bottom border-block-end-dashed">
                    <button class="btn btn-primary d-flex align-items-center justify-content-center" 
                            data-bs-toggle="modal" 
                            data-bs-target="#addtask" 
                            data-saldo="<?php echo htmlspecialchars($saldoliquido); ?>">
                        <i class="ri-add-circle-line fs-16 align-middle me-1"></i> Criar Transação de entrada
                    </button>
                    
                    <!-- Modal -->
                    <div class="modal fade" id="addtask" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h6 class="modal-title" id="mail-ComposeLabel">Novo Saque</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form id="inserir-entrada" method="POST" action="insert_entrada.php" enctype="multipart/form-data">
    <div class="modal-body px-4">
        <div class="row gy-2">

            <!-- Verificação de saldo baixo -->
            <?php if ($saldoBaixo): ?>
                <div class="alert alert-danger mt-4">
                    <strong>Saldo muito baixo para realizar um saque.</strong>
                </div>
            <?php endif; ?>

            <!-- Campo de seleção de usuário -->
            <div class="col-xl-12">
                <label for="user_id" class="form-label">Selecionar Usuário</label>
                <select class="form-select" id="user_id" name="user_id" required>
                    <option value="">Selecione um usuário</option>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($row['user_id']); ?>">
                            <?php echo htmlspecialchars($row['email']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Campo de valor -->
            <div class="col-xl-12">
                <label for="valor" class="form-label">Valor</label>
                <input type="number" step="0.01" class="form-control" id="valor" name="valor" placeholder="Valor" required>
                <div id="valorError" class="text-danger mt-2" style="display: none;">Saldo insuficiente para o valor solicitado.</div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary" <?php echo ($count > 0) ? 'disabled' : ''; ?>>Solicitar</button>
    </div>
</form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Explicação sobre taxas padrão -->
                <div class="alert alert-info mt-4">
                    <ul>
                        <li><strong>Crie pagamentos de entradas</strong></li>
                        <li><strong>Escolha o usuário e insira o valor do saldo que vai ser inserido</strong></li>
                        <li><strong>A descrição vai ficar como TKIPAY</strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End::row-1 -->





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

// Consulta para obter o número total de registros com descricao_transacao 'entrada-criada'
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM solicitacoes WHERE descricao_transacao = 'entrada-criada'");
$totalRow = $totalResult->fetch_assoc();
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $limit);

// Consulta para obter os registros com descricao_transacao 'entrada-criada'
$sql = "SELECT id, user_id, externalreference, amount, client_name, client_document, client_email, real_data, status, qrcode_pix, paymentcode, idtransaction, paymentCodeBase64, adquirente_ref, taxa_cash_in, deposito_liquido, taxa_pix_cash_in_adquirente, taxa_pix_cash_in_valor_fixo, client_telefone, executor_ordem, descricao_transacao
        FROM solicitacoes 
        WHERE descricao_transacao = 'entrada-criada'
        ORDER BY real_data DESC 
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
                    SOLICITAÇÕES DE ENTRADA CRIADAS
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table text-nowrap table-bordered">
                        <thead>
                            <tr>    
                                <th scope="col">User ID</th> 
                                <th scope="col">Referência Externa</th>
                                <th scope="col">Valor</th>
                                <th scope="col">Data Real</th>
                                <th scope="col">Ações</th> <!-- Nova coluna para ações -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                // Itera sobre os resultados e exibe cada linha na tabela
                                while ($row = $result->fetch_assoc()) {
                                    $statusBadge = $row['status'] == '1' ? 'bg-success-transparent' : 'bg-light text-dark';
                                    $statusText = $row['status'] == '1' ? 'Aprovado' : 'Pendente';
                        
                                    // Exibe os dados na tabela
                                    echo "<tr>";
                                    echo "<td>{$row['user_id']}</td>"; 
                                    echo "<td>{$row['externalreference']}</td>";
                                    echo "<td>{$row['amount']}</td>";
                                    echo "<td>{$row['real_data']}</td>";
                        
                                    // Ações com o user_id correto na URL
                                    echo "<td>
                                        <a href='excluir_entrada_criada.php?id={$row['id']}&user_id={$row['user_id']}' class='btn btn-warning'>Excluir</a> 
                                    </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='10'>Nenhum registro encontrado</td></tr>";
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






