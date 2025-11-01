





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
                            <p class="fw-medium fs-20 mb-0">Ajustes plataforma</p>
                        </div>
</div>




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
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM adquirentes");
$totalRow = $totalResult->fetch_assoc();
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $limit);

// Consulta para obter os registros com paginação
$sql = "SELECT id, adquirente, status, url, referencia 
        FROM adquirentes 
        ORDER BY id ASC 
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
                    Adquirentes PIX
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table text-nowrap table-bordered">
                        <thead>
                            <tr>
    
                                <th scope="col">Adquirente</th>
                                <th scope="col">Status</th>
                                <th scope="col">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    $statusBadge = ($row['status'] == '1') ? 'bg-success-transparent' : 'bg-danger-transparent';
                                    $statusText = ($row['status'] == '1') ? 'ATIVO' : 'DESATIVADO';
                                    $actionButton = ($row['status'] == '1') ?
                                        "<button class='btn btn-warning btn-sm' data-id='{$row['id']}' data-action='deactivate'>Desativar</button>" :
                                        "<button class='btn btn-success btn-sm' data-id='{$row['id']}' data-action='activate'>Ativar</button>";
                                    
                                    echo "<tr>";
                              
                                    echo "<td>{$row['adquirente']}</td>";
                                    echo "<td><span class='badge {$statusBadge}'>{$statusText}</span></td>";
                                    echo "<td>{$actionButton}</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4'>Nenhum registro encontrado</td></tr>";
                            }
                            $stmt->close();
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>

                  <!-- Explicação sobre taxas padrão -->
                  <div class="alert alert-info mt-4">
                    <h5>Informação sobre ativação de adquirentes</h5>
                    <p>Se tiver mais de um adquirente ativa o sistema vai automaticamente dividir 50% das transações em cada adquirente, então lembre-se de ao ativar mais de uma adquirente verificar sempre: </p>
                    <ul>
                        <li><strong>Chaves API:</strong> Lembre-se de verificar se as chaves estão corretas em todas as adquirentes ativads</li>
                        <li><strong>Liberação da adquirente:</strong> Verifique se a adquirente selecionada esta liberada tanto cash in tanto cash out</li>
                    </ul>
                      </div>
           
            </div>
        </div>
    </div>
</div>
<!-- End::row-2 -->

<!-- JavaScript para Ativar/Desativar -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('button[data-action]').forEach(button => {
        button.addEventListener('click', function () {
            var id = this.getAttribute('data-id');
            var action = this.getAttribute('data-action');
            var url = action === 'activate' ? 'activate_adquirente.php' : 'deactivate_adquirente.php';

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'id': id
                })
            }).then(response => response.text())
              .then(result => {
                  console.log(result); // Para depuração
                  if (result === 'success') {
                      window.location.reload();
                  } else {
                      alert('Erro ao atualizar status do adquirente.');
                  }
              });
        });
    });
});
</script>









<!-- Start::row-2 -->
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card custom-card shadow-sm">
            <div class="card-header text-center bg-light">
                <h5 class="card-title m-0">AJUSTES ADQUIRENTES PIX</h5>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-hover table-sm table-striped text-center">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">Adquirentes</th>
                                <th scope="col">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>PagPix</td>
                                <td>
                                    <a href="editar_adquirente_pix.php?id=pagpix" class="btn btn-primary btn-wave">Editar</a>
                                </td>
                            </tr>
                            <tr>
                                <td>BsPay</td>
                                <td>
                                    <a href="editar_adquirente_pix.php?id=bspay" class="btn btn-primary btn-wave">Editar</a>
                                </td>
                            </tr>
                            <tr>
                                <td>SuitPay</td>
                                <td>
                                    <a href="editar_adquirente_pix.php?id=suitpay" class="btn btn-primary btn-wave">Editar</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End::row-2 -->














<div>
                            <p class="fw-medium fs-20 mb-0">Ajustes TAXAS</p>
                        </div>


                        <br><br>







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

// Consulta para obter o único registro da tabela
$sql = "SELECT taxa_cash_in_padrao, taxa_cash_out_padrao, taxa_pix_valor_real_cash_in_padrao, reserva_padrao, indicacao_padrao, taxa_fixa_padrao FROM app LIMIT 1";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$conn->close();
?>

<!-- Start::row-2 -->
<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">
                    AJUSTES GATEWAY
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table text-nowrap table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">Taxa Cash In Padrão %</th>
                                <th scope="col">Taxa Cash Out Padrão R$</th>
                                <th scope="col">Taxa PIX Cash In Padrão R$</th> <!-- Nova coluna -->
                                <th scope="col">Reserva Padrão %</th> <!-- Nova coluna -->
                                <th scope="col">Taxa Fixa Padrão R$</th> <!-- Nova coluna -->
                                <th scope="col">Indicação Padrão %</th> <!-- Nova coluna -->
                                <th scope="col">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo htmlspecialchars($row['taxa_cash_in_padrao']); ?></td>
                                <td><?php echo htmlspecialchars($row['taxa_cash_out_padrao']); ?></td>
                                <td><?php echo htmlspecialchars($row['taxa_pix_valor_real_cash_in_padrao']); ?></td> <!-- Novo valor -->
                                <td><?php echo htmlspecialchars($row['reserva_padrao']); ?></td>
                                <td><?php echo htmlspecialchars($row['taxa_fixa_padrao']); ?></td>
                                <td><?php echo htmlspecialchars($row['indicacao_padrao']); ?></td>
                                <td>
                                    <button class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#editAppModal'
                                            data-taxacashin='<?php echo htmlspecialchars($row['taxa_cash_in_padrao']); ?>'
                                            data-taxacashout='<?php echo htmlspecialchars($row['taxa_cash_out_padrao']); ?>'
                                            data-indicacao='<?php echo htmlspecialchars($row['indicacao_padrao']); ?>'
                                            data-reserva='<?php echo htmlspecialchars($row['reserva_padrao']); ?>'
                                            data-fixa='<?php echo htmlspecialchars($row['taxa_fixa_padrao']); ?>'
                                            data-taxapix='<?php echo htmlspecialchars($row['taxa_pix_valor_real_cash_in_padrao']); ?>'> <!-- Novo atributo -->
                                        Editar
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Explicação sobre taxas padrão -->
                <div class="alert alert-info mt-4">
                    <h5>Informação sobre Taxas Padrão</h5>
                    <p>A taxa padrão é o valor que será creditado na conta de um novo usuário em relação a operações de entrada e saída. Especificamente:</p>
                    <ul>
                        <li><strong>Taxa Cash In Padrão:</strong> Esta é a taxa aplicada para cada entrada de dinheiro na conta do usuário.</li>
                        <li><strong>Taxa Cash Out Padrão:</strong> Esta é a taxa aplicada para cada saída de dinheiro da conta do usuário.</li>
                        <li><strong>Taxa PIX Cash In Padrão:</strong> Esta é a taxa aplicada para entradas de dinheiro via PIX na conta do usuário.</li> <!-- Explicação da nova taxa -->
                    </ul>
                    <p>Essas taxas são aplicadas automaticamente a todos os novos usuários ao abrir uma conta e são definidas aqui para garantir a consistência em todas as transações.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End::row-2 -->

<!-- Modal Editar -->
<div class="modal fade" id="editAppModal" tabindex="-1" aria-labelledby="editAppModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAppModalLabel">Editar Configuração</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editAppForm">
                    <div class="mb-3">
                        <label for="editTaxaCashIn" class="form-label">Taxa Cash In Padrão</label>
                        <input type="text" class="form-control" id="editTaxaCashIn" name="taxa_cash_in_padrao">
                    </div>
                    <div class="mb-3">
                        <label for="editTaxaCashOut" class="form-label">Taxa Cash Out Padrão</label>
                        <input type="text" class="form-control" id="editTaxaCashOut" name="taxa_cash_out_padrao">
                    </div>
                    <div class="mb-3">
                        <label for="editTaxaPix" class="form-label">Taxa PIX Cash In Padrão</label>
                        <input type="text" class="form-control" id="editTaxaPix" name="taxa_pix_valor_real_cash_in_padrao"> <!-- Novo campo -->
                    </div>
                    <div class="mb-3">
                        <label for="editTaxaPixFixa" class="form-label">Taxa Fixa Padrão Padrão</label>
                        <input type="text" class="form-control" id="editTaxaPixFixa"
                                    name="taxa_fixa_padrao"> <!-- Novo campo -->
                    </div>
                    <div class="mb-3">
                        <label for="editReserva" class="form-label">Taxa Reserva Padrão</label>
                        <input type="text" class="form-control" id="editReserva"
                                    name="reserva_padrao"> <!-- Novo campo -->
                    </div>
                    <div class="mb-3">
                        <label for="editIndicacao" class="form-label">Taxa Indicação Padrão</label>
                        <input type="text" class="form-control" id="editIndicacao"
                                    name="indicacao_padrao"> <!-- Novo campo -->
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar alterações</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript para Edição e Atualização -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    var editAppModal = document.getElementById('editAppModal');

    // Preencher o modal de edição
    editAppModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        document.getElementById('editTaxaCashIn').value = button.getAttribute('data-taxacashin');
        document.getElementById('editTaxaCashOut').value = button.getAttribute('data-taxacashout');
        document.getElementById('editTaxaPix').value = button.getAttribute('data-taxapix'); // Novo preenchimento
        document.getElementById('editTaxaPix').value = button.getAttribute('data-taxapix'); // Novo preenchimento
        document.getElementById('editReserva').value = button.getAttribute('data-reserva'); // Novo preenchimento
        document.getElementById('editIndicacao').value = button.getAttribute('data-indicacao'); // Novo preenchimento
    });

    // Enviar o formulário de edição
    document.getElementById('editAppForm').addEventListener('submit', function (event) {
        event.preventDefault();
        var formData = new FormData(this);
        fetch('update_app.php', {
            method: 'POST',
            body: formData
        }).then(response => response.text())
          .then(result => {
              console.log(result); // Para depuração
              if (result === 'success') {
                  window.location.reload();
              } else {
                  alert('Erro ao atualizar configuração.');
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



