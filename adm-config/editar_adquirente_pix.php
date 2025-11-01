





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
                            <p class="fw-medium fs-20 mb-0">Ajustes Adquirente pix</p>
                        </div>
</div>




<?php
include '../conectarbanco.php';

// Conectar ao banco de dados
$conn = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name']);

// Verificar se o ID foi passado via GET
if (isset($_GET['id'])) {
    $adquirente = $_GET['id'];

    // Definir a tabela com base no adquirente
    if ($adquirente == 'pagpix') {
        $tabela = 'ad_pagpix';
    } elseif ($adquirente == 'suitpay') {
        $tabela = 'ad_suitpay';
    } else {
        echo "Adquirente inválido.";
        exit;
    }

    // Selecionar os dados da tabela correspondente sem filtro de ID (já que só há uma linha)
    $sql = "SELECT * FROM $tabela LIMIT 1"; // Selecionar a primeira linha da tabela
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "Nenhum registro encontrado para este adquirente.";
        exit;
    }
}
?>

<!-- Exibir Dados Financeiros -->
<div class="col-12">
    <div class="card custom-card">
        <div class="card-header justify-content-between">
            <div class="card-title">
               DETALHES ADQUIRENTE
            </div>
        </div>
        <div class="card-body">
            <div class="row gy-4">
                <div class="col-12 col-md-6">
                    <label class="form-label">URL Cash In:</label>
                    <p><?= $row['url_cash_in'] ?></p>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">URL Cash Out:</label>
                    <p><?= $row['url_cash_out'] ?></p>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Taxa Pix Cash In:</label>
                    <p>R$ <?= $row['taxa_pix_cash_in'] ?></p>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Taxa Pix Cash Out:</label>
                    <p>R$ <?= $row['taxa_pix_cash_out'] ?></p>
                </div>
            </div>
            <!-- Botão para abrir o modal de edição -->
            <button class="btn btn-warning mt-4" data-bs-toggle="modal" data-bs-target="#editModal">Editar</button>
        </div>
    </div>
</div>

<!-- Modal de Edição -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Editar Dados Financeiros</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editForm">
          <div class="mb-3">
            <label for="url_cash_in" class="form-label">URL Cash In</label>
            <input type="text" class="form-control" id="url_cash_in" name="url_cash_in" value="<?= $row['url_cash_in'] ?>">
          </div>
          <div class="mb-3">
            <label for="url_cash_out" class="form-label">URL Cash Out</label>
            <input type="text" class="form-control" id="url_cash_out" name="url_cash_out" value="<?= $row['url_cash_out'] ?>">
          </div>
          <div class="mb-3">
            <label for="taxa_pix_cash_in" class="form-label">Taxa Pix Cash In (R$)</label>
            <input type="number" step="0.01" class="form-control" id="taxa_pix_cash_in" name="taxa_pix_cash_in" value="<?= $row['taxa_pix_cash_in'] ?>">
          </div>
          <div class="mb-3">
            <label for="taxa_pix_cash_out" class="form-label">Taxa Pix Cash Out (R$)</label>
            <input type="number" step="0.01" class="form-control" id="taxa_pix_cash_out" name="taxa_pix_cash_out" value="<?= $row['taxa_pix_cash_out'] ?>">
          </div>
          <input type="hidden" name="adquirente" value="<?= $adquirente ?>">
          <button type="submit" class="btn btn-primary">Salvar alterações</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// Função para enviar os dados via AJAX
document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('atualizar_dados_pagpix.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Dados atualizados com sucesso!');
            location.reload(); // Recarregar a página para mostrar os dados atualizados
        } else {
            alert('Erro ao atualizar os dados.');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Ocorreu um erro ao atualizar os dados.');
    });
});
</script>

<?php
$stmt->close();
$conn->close();
?>




                     

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



