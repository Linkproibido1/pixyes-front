<?php
session_start();

// Verificar se o e-mail está presente na sessão
function formatarTelefone($telefone)
{
    $telefone = preg_replace('/\D/', '', $telefone); // Remove tudo que não é número

    if (strlen($telefone) === 11) {
        return '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 5) . '-' . substr($telefone, 7);
    } elseif (strlen($telefone) === 10) {
        return '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 4) . '-' . substr($telefone, 6);
    }

    return $telefone; // Retorna o número original se não for possível formatar
}


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
} elseif ($permission == 4) {
    // Permissão de compliance, permitir apenas editar usuários
    // Continue a execução da página de edição de usuários
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

        // Obter o ID do usuário da URL
        $id = $_GET['id'];

        // Conectar ao banco de dados
        $conn = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name']);

        // Verificar a conexão
        if ($conn->connect_error) {
            die("Falha na conexão: " . $conn->connect_error);
        }

        // Consulta para obter os dados do usuário
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        } else {
            echo "Usuário não encontrado.";
            exit;
        }

        // Verifica se o botão de aprovação foi pressionado
        if (isset($_POST['aprovar'])) {
            $update_sql = "UPDATE users SET status = 1 WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $id);
            $update_stmt->execute();

            if ($update_stmt->affected_rows > 0) {
                echo "<script>alert('Usuário aprovado com sucesso!');</script>";
            } else {
                echo "<script>alert('Erro ao aprovar o usuário.');</script>";
            }

            $update_stmt->close();
            // Recarrega os dados após a atualização
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
        }

        // Verifica se o botão "Colocar em Análise" foi pressionado
        if (isset($_POST['reavaliar'])) {
            $update_sql = "UPDATE users SET status = 5 WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $id);
            $update_stmt->execute();

            if ($update_stmt->affected_rows > 0) {
                echo "<script>alert('Usuário colocado em análise com sucesso!');</script>";
            } else {
                echo "<script>alert('Erro ao colocar o usuário em análise.');</script>";
            }

            $update_stmt->close();
            // Recarrega os dados após a atualização
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
        }

        // Verifica se o botão "Bloquear" foi pressionado
        if (isset($_POST['bloquear'])) {
            $update_sql = "UPDATE users SET status = 3 WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $id);
            $update_stmt->execute();

            if ($update_stmt->affected_rows > 0) {
                echo "<script>alert('Usuário bloqueado com sucesso!');</script>";
            } else {
                echo "<script>alert('Erro ao bloquear o usuário.');</script>";
            }

            $update_stmt->close();
            // Recarrega os dados após a atualização
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
        }

        // Verifica se o botão "Desbloquear" foi pressionado
        if (isset($_POST['desbloquear'])) {
            $update_sql = "UPDATE users SET status = 1 WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $id);
            $update_stmt->execute();

            if ($update_stmt->affected_rows > 0) {
                echo "<script>alert('Usuário desbloqueado com sucesso!');</script>";
            } else {
                echo "<script>alert('Erro ao desbloquear o usuário.');</script>";
            }

            $update_stmt->close();
            // Recarrega os dados após a atualização
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
        }

        if (isset($_POST['user_level'])) {
            $update_sql = "UPDATE users SET permission = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ii", $_POST['user_level'], $id);
            $update_stmt->execute();

            if ($update_stmt->affected_rows > 0) {
                echo "<script>alert('Permissão atualizada sucesso!');</script>";
            } else {
                echo "<script>alert('Erro ao atualizar o usuário.');</script>";
            }

            $update_stmt->close();
            // Recarrega os dados após a atualização
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
        }

        if (isset($_POST['taxa_cash_in'])) {
            $update_sql = "UPDATE users SET taxa_cash_in = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $_POST['taxa_cash_in'], $id);
            $update_stmt->execute();

            if ($update_stmt->affected_rows > 0) {
                echo "<script>alert('Taxa atualizada com sucesso!');</script>";
            } else {
                echo "<script>alert('Erro ao atualizar o usuário.');</script>";
            }

            $update_stmt->close();
            // Recarrega os dados após a atualização
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
        }

        if (isset($_POST['saldo_retido']) && !empty($_POST['saldo_retido']) && $permission == 3) {
            $saldo_atual = 0;
            $saldo_atual_retido = 0;
            $valor_atualizar = floatval($_POST['saldo_retido']);
            $sql_saldo = $conn->query("SELECT saldo_retido,saldo FROM users WHERE id = $id");
            $saldos = $sql_saldo->fetch_assoc();

            $saldo_atual = floatval($saldos['saldo'] ?? 0.00);
            $saldo_atual_retido = floatval($saldos['saldo_retido'] ?? 0.00);

            if ($valor_atualizar > $saldo_atual && isset($_POST['add_saldo_retido'])) {
                echo "<script>alert('Saldo superior ao atual!');</script>";
            }

            if ($valor_atualizar > $saldo_atual_retido && isset($_POST['del_saldo_retido'])) {
                echo "<script>alert('Saldo superior ao atual!');</script>";
            }

            if (isset($_POST['add_saldo_retido']) && $valor_atualizar > 0 && $valor_atualizar <= $saldo_atual) {
                $saldo_atual = $saldo_atual - $valor_atualizar;
                $saldo_atual_retido = $saldo_atual_retido + $valor_atualizar;
            }

            if (isset($_POST['del_saldo_retido']) && $valor_atualizar > 0 && $valor_atualizar <= $saldo_atual_retido) {
                $saldo_atual = $saldo_atual + $valor_atualizar;
                $saldo_atual_retido = $saldo_atual_retido - $valor_atualizar;
            }

            /*if($valor_atualizar == 0 || $valor_atualizar == 0.00){
                $saldo_atual = $saldo_atual - $saldo_atual_retido;
                $saldo_atual_retido = 0;
            }*/

            $update_sql_saldo = "UPDATE users SET saldo = ? WHERE id = ?";
            $update_stmt_saldo = $conn->prepare($update_sql_saldo);
            $update_stmt_saldo->bind_param("si", $saldo_atual, $id);
            $update_stmt_saldo->execute();

            $update_sql = "UPDATE users SET saldo_retido = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $saldo_atual_retido, $id);
            $update_stmt->execute();

            if ($update_stmt->affected_rows > 0 && $update_stmt_saldo->affected_rows > 0) {
                echo "<script>alert('Saldo atualizado com sucesso!');</script>";
            } else {
                echo "<script>alert('Erro ao atualizar o saldo.');</script>";
            }

            $update_stmt->close();
            // Recarrega os dados após a atualização
            echo "<script>window.location.href = 'user_details.php?id=$id';</script>";
        }

        if (isset($_POST['referencia_porcentagem'])) {
            $update_sql = "UPDATE users SET referencia_porcentagem = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $_POST['referencia_porcentagem'], $id);
            $update_stmt->execute();

            if ($update_stmt->affected_rows > 0) {
                echo "<script>alert('Porcentagem atualizada com sucesso!');</script>";
            } else {
                echo "<script>alert('Erro ao atualizar o usuário.');</script>";
            }

            $update_stmt->close();
            // Recarrega os dados após a atualização
            echo "<script>window.location.href = 'user_details.php?id=$id';</script>";
        }

        if (isset($_POST['reserva_financeira'])) {
            $update_sql = "UPDATE users SET reserva_financeira = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $_POST['reserva_financeira'], $id);
            $update_stmt->execute();

            if ($update_stmt->affected_rows > 0) {
                echo "<script>alert('Reserva salva com sucesso!');</script>";
            } else {
                echo "<script>alert('Erro ao atualizar reserva.');</script>";
            }

            $update_stmt->close();
            // Recarrega os dados após a atualização
            echo "<script>window.location.href = 'user_details.php?id=$id';</script>";
        }

        if (isset($_POST['taxa_cash_in_fixa'])) {
            $update_sql = "UPDATE users SET taxa_cash_in_fixa = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $_POST['taxa_cash_in_fixa'], $id);
            $update_stmt->execute();

            if ($update_stmt->affected_rows > 0) {
                echo "<script>alert('Taxa atualizada com sucesso!');</script>";
            } else {
                echo "<script>alert('Erro ao atualizar o usuário.');</script>";
            }

            $update_stmt->close();
            // Recarrega os dados após a atualização
            echo "<script>window.location.href = 'user_details.php?id=$id';</script>";
        }

        if (isset($_POST['taxa_percentual'])) {
            $update_sql = "UPDATE users SET taxa_percentual = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $_POST['taxa_percentual'], $id);
            $update_stmt->execute();

            if ($update_stmt->affected_rows > 0) {
                echo "<script>alert('Taxa atualizada com sucesso!');</script>";
            } else {
                echo "<script>alert('Erro ao atualizar o usuário.');</script>";
            }

            $update_stmt->close();
            // Recarrega os dados após a atualização
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
        }

        if (isset($_POST['taxa_cash_out'])) {
            $update_sql = "UPDATE users SET taxa_cash_out = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $_POST['taxa_cash_out'], $id);
            $update_stmt->execute();

            if ($update_stmt->affected_rows > 0) {
                echo "<script>alert('Taxa atualizada com sucesso!');</script>";
            } else {
                echo "<script>alert('Erro ao atualizar o usuário.');</script>";
            }

            $update_stmt->close();
            // Recarrega os dados após a atualização
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
        }

        if (isset($_POST['taxa_cash_in_tipo'])) {
            $update_sql = "UPDATE users SET taxa_cash_in_tipo = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $_POST['taxa_cash_in_tipo'], $id);
            $update_stmt->execute();

            if ($update_stmt->affected_rows > 0) {
                echo "<script>alert('Tipo de taxa atualizada com sucesso!');</script>";
            } else {
                echo "<script>alert('Erro ao atualizar o usuário.');</script>";
            }

            $update_stmt->close();
            // Recarrega os dados após a atualização
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
        }

        if (isset($_POST['taxa_cash_out_tipo'])) {
            $update_sql = "UPDATE users SET taxa_cash_out_tipo = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $_POST['taxa_cash_out_tipo'], $id);
            $update_stmt->execute();

            if ($update_stmt->affected_rows > 0) {
                echo "<script>alert('Tipo de taxa atualizada com sucesso!');</script>";
            } else {
                echo "<script>alert('Erro ao atualizar o usuário.');</script>";
            }

            $update_stmt->close();
            // Recarrega os dados após a atualização
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
        }

        $stmt->close();
        $conn->close();
        ?>
        <!-- Exibição dos dados -->
        <div class="row">
            <div class="col-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            DADOS DO USUÁRIO
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Dados do Usuário -->
                        <div class="row gy-4">
                            <div class="col-12 col-sm-6 col-md-4">
                                <label class="form-label">Usuario:</label>
                                <p><?= $row['user_id'] ?></p>
                            </div>

                            <div class="col-12 col-sm-6 col-md-4">
                                <label class="form-label">Nome:</label>
                                <p><?= $row['nome'] ?></p>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <label class="form-label">Email:</label>
                                <p><?= $row['email'] ?></p>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <label class="form-label">CPF/CNPJ:</label>
                                <p><?= $row['cpf_cnpj'] ?></p>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <label class="form-label">Data de Nascimento:</label>
                                <p><?= $row['data_nascimento'] ?></p>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <label class="form-label">Telefone:</label>
                                <p><?= formatarTelefone($row['telefone']) ?></p>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <label class="form-label">Status:</label>
                                <p class="
        <?php
        switch ($row['status']) {
            case 0:
                echo 'bg-warning-transparent text-warning'; // Pendente (amarelo)
                break;
            case 1:
                echo 'bg-success-transparent text-success'; // Aprovada (verde)
                break;
            case 3:
                echo 'bg-danger-transparent text-danger'; // Banido (vermelho)
                break;
            case 5:
                echo 'bg-warning-transparent text-warning'; // Em Análise (amarelo)
                break;
            default:
                echo 'bg-secondary text-dark'; // Status Desconhecido (cinza)
        }
        ?>
        p-2 rounded">
                                    <?php
                                    switch ($row['status']) {
                                        case 0:
                                            echo "Pendente";
                                            break;
                                        case 1:
                                            echo "Aprovado";
                                            break;
                                        case 3:
                                            echo "Banido";
                                            break;
                                        case 5:
                                            echo "Em Análise";
                                            break;
                                        default:
                                            echo "Status Desconhecido";
                                    }
                                    ?>
                                </p>
                            </div>


                            <!-- Botão de Aprovação -->
                            <div class="col-12">
                                <form method="POST">
                                    <?php if ($row['status'] == 1): ?>
                                        <button type="submit" name="reavaliar" class="btn btn-warning">Colocar em
                                            Análise</button>
                                    <?php else: ?>
                                        <button type="submit" name="aprovar" class="btn btn-success">Aprovar
                                            Usuário</button>
                                    <?php endif; ?>
                                </form>
                            </div>

                            <div class="col-12">
                                <form method="POST">
                                    <?php if (intval($row['permission']) == 1): ?>
                                        <button type="submit" name="user_level" value="3" class="btn btn-warning">Tornar
                                            admin</button>
                                    <?php else: ?>
                                        <button type="submit" name="user_level" value="1" class="btn btn-success">Tornar
                                            usuario comum</button>
                                    <?php endif; ?>
                                </form>
                            </div>

                            <div class="col-12">
                                <form method="POST">
                                    <?php if ($row['status'] != 3): ?>
                                        <button type="submit" name="bloquear" class="btn btn-warning">Bloquear
                                            Usuário</button>
                                    <?php else: ?>
                                        <button type="submit" name="desbloquear" class="btn btn-success">Desbloquear
                                            Usuário</button>
                                    <?php endif; ?>
                                </form>
                            </div>



                            <div class="col-12 col-sm-6 col-md-4">
                                <label class="form-label">Data de Cadastro:</label>
                                <p><?= date('d/m/Y H:i', strtotime($row['data_cadastro'])) ?></p>
                            </div>
                        </div>

                        <!-- Contêiner para as fotos -->
                        <div class="row gy-4 mt-4">
                            <div class="col-12">
                                <div class="card-title">
                                    FOTOS DE DOCUMENTAÇÃO
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 text-center">
                                <img src="../uploads/<?= $row['foto_rg_frente'] ?>" alt="Foto de Frente RG"
                                    class="img-thumbnail" width="150" style="cursor: pointer;" data-bs-toggle="modal"
                                    data-bs-target="#fotoFrenteModal">
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 text-center">
                                <img src="../uploads/<?= $row['foto_rg_verso'] ?>" alt="Foto de Verso RG"
                                    class="img-thumbnail" width="150" style="cursor: pointer;" data-bs-toggle="modal"
                                    data-bs-target="#fotoVersoModal">
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 text-center">
                                <img src="../uploads/<?= $row['selfie_rg'] ?>" alt="Selfie RG" class="img-thumbnail"
                                    width="150" style="cursor: pointer;" data-bs-toggle="modal"
                                    data-bs-target="#selfieModal">
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>




        <!-- Modais para exibir as fotos maiores -->
        <!-- Modal Foto Frente RG -->
        <div class="modal fade" id="fotoFrenteModal" tabindex="-1" aria-labelledby="fotoFrenteLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="fotoFrenteLabel">Foto de Frente RG</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="../uploads/<?= $row['foto_rg_frente'] ?>" alt="Foto de Frente RG" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Foto Verso RG -->
        <div class="modal fade" id="fotoVersoModal" tabindex="-1" aria-labelledby="fotoVersoLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="fotoVersoLabel">Foto de Verso RG</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="../uploads/<?= $row['foto_rg_verso'] ?>" alt="Foto de Verso RG" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Selfie RG -->
        <div class="modal fade" id="selfieModal" tabindex="-1" aria-labelledby="selfieLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="selfieLabel">Selfie com RG</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="../uploads/<?= $row['selfie_rg'] ?>" alt="Selfie com RG" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>


        <!-- Dados de Endereço -->
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-header justify-content-between">
                    <div class="card-title">
                        DADOS DE ENDEREÇO
                    </div>
                </div>
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-12 col-md-4">
                            <label class="form-label">CEP:</label>
                            <p><?= $row['cep'] ?></p>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Rua:</label>
                            <p><?= $row['rua'] ?></p>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Número:</label>
                            <p><?= $row['numero_residencia'] ?></p>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Complemento:</label>
                            <p><?= $row['complemento'] ?></p>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Bairro:</label>
                            <p><?= $row['bairro'] ?></p>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Cidade:</label>
                            <p><?= $row['cidade'] ?></p>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Estado:</label>
                            <p><?= $row['estado'] ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Dados Financeiros -->
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-header justify-content-between">
                    <div class="card-title">
                        DADOS FINANCEIROS
                    </div>
                </div>
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-12 col-md-6">
                            <label class="form-label">Saldo:</label>
                            <p>R$ <?= number_format($row['saldo'], 2, ',', '.') ?></p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Saldo Retido:</label>
                            <p>R$ <?= number_format($row['saldo_retido'], 2, ',', '.') ?></p>

                            <form method="POST" action="">
                                <input type="text" name="saldo_retido" id="saldo_retido"
                                    value="<?= $row['saldo_retido'] ?>">
                                <button type="submit" name="add_saldo_retido" id="add_saldo_retido" class="btn btn-success">Adicionar</button>
                                <button type="submit" name="del_saldo_retido" id="del_saldo_retido" class="btn btn-success">Remover</button>
                            </form>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Total de Transações:</label>
                            <p><?= $row['total_transacoes'] ?></p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Transações Aprovadas:</label>
                            <p><?= $row['transacoes_aproved'] ?></p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Transações Recusadas:</label>
                            <p><?= $row['transacoes_recused'] ?></p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Valor Sacado:</label>
                            <p>R$ <?= number_format($row['valor_sacado'], 2, ',', '.') ?></p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Valor de Saque Pendente:</label>
                            <p>R$ <?= number_format($row['valor_saque_pendente'], 2, ',', '.') ?></p>
                        </div>

                        <div class="col-12 col-md-6 d-flex gap-2 flex-wrap">
                            <div class="align-items-center">
                                <label class="form-label">Referencia Porcentagem: <?= $row['referencia_porcentagem'] ?>%</label>

                                <form method="POST" action="">
                                    <input type="text" name="referencia_porcentagem" id="referencia_porcentagem"
                                        value="<?= $row['referencia_porcentagem'] ?>">
                                    <button type="submit" class="btn btn-success">Alterar</button>
                                </form>
                            </div>

                        </div>
                        
                        <div class="col-12 col-md-6 d-flex gap-2 flex-wrap">
                            <div class="align-items-center">
                                <label class="form-label">Reserva Financeira: <?= $row['reserva_financeira'] ?>%</label>

                                <form method="POST" action="">
                                    <input type="text" name="reserva_financeira" id="reserva_financeira"
                                        value="<?= $row['reserva_financeira'] ?>">
                                    <button type="submit" class="btn btn-success">Alterar taxa</button>
                                </form>
                            </div>

                        </div>

                        <div class="col-12 col-md-6 d-flex gap-2 flex-wrap">
                            <div class="align-items-center">
                                <label class="form-label">Taxa Cash In: <?= $row['taxa_cash_in_fixa'] ?> Fixa</label>

                                <form method="POST" action="">
                                    <input type="text" name="taxa_cash_in_fixa" id="taxa_cash_in_fixa"
                                        value="<?= $row['taxa_cash_in_fixa'] ?>">
                                    <button type="submit" class="btn btn-success">Alterar taxa</button>
                                </form>
                            </div>

                        </div>

                        <div class="col-12 col-md-6 d-flex gap-2 flex-wrap">
                            <div class="align-items-center">
                                <label class="form-label">Taxa Cash In: <?= $row['taxa_cash_in'] ?>%</label>

                                <form method="POST" action="">
                                    <input type="text" name="taxa_cash_in" id="taxa_cash_in"
                                        value="<?= $row['taxa_cash_in'] ?>">
                                    <button type="submit" class="btn btn-success">Alterar taxa</button>
                                </form>
                            </div>

                        </div>

                        <div class="col-12 col-md-6 d-flex gap-2 flex-wrap">
                            <div class="align-items-center">
                                <label class="form-label">Taxa Cash Out (Percentual):
                                    <?= $row['taxa_percentual'] ?>%</label>

                                <form method="POST" action="">
                                    <input type="text" name="taxa_percentual" id="taxa_percentual"
                                        value="<?= $row['taxa_percentual'] ?>">
                                    <button type="submit" class="btn btn-success">Alterar taxa</button>
                                </form>
                            </div>

                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label">Taxa Cash Out: R$ <?= $row['taxa_cash_out'] ?></label>

                            <form method="POST" action="">
                                <input type="text" name="taxa_cash_out" id="taxa_cash_out"
                                    value="<?= $row['taxa_cash_out'] ?>">
                                <button type="submit" class="btn btn-success">Alterar taxa</button>
                            </form>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Volume Transacionado:</label>
                            <p>R$ <?= number_format($row['volume_transacionado'], 2, ',', '.') ?></p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Valor Pago em Taxas:</label>
                            <p>R$ <?= number_format($row['valor_pago_taxa'], 2, ',', '.') ?></p>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label">Média de Faturamento mensal:</label>
                            <p><?= $row['media_faturamento'] ?></p>
                        </div>
                    </div>
                </div>
            </div>





            <style>
                .pagination {
                    display: flex;
                    justify-content: center;
                    padding: 10px 0;
                }

                .pagination-link {
                    display: inline-block;
                    padding: 8px 8px;
                    margin: 0 4px;
                    text-decoration: none;
                    color: #007bff;
                    border: 1px solid #007bff;
                    border-radius: 4px;
                    transition: background-color 0.3s, color 0.3s;
                }

                .pagination-link:hover {
                    background-color: #007bff;
                    color: white;
                }

                .pagination-link.active {
                    background-color: #007bff;
                    color: white;
                    border: 1px solid #007bff;
                }

                .pagination-link.disabled {
                    color: #6c757d;
                    border: 1px solid #6c757d;
                    cursor: not-allowed;
                }
            </style>



            <?php
            // Incluir o arquivo de configuração do banco de dados
            include '../conectarbanco.php';

            // Criar a conexão usando as credenciais fornecidas no arquivo incluído
            $conn = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name']);

            // Verifica se houve algum erro na conexão
            if ($conn->connect_error) {
                die("Erro na conexão com o banco de dados: " . $conn->connect_error);
            }

            // Obter o ID do usuário da URL
            $user_id_var = isset($_GET['id']) ? $_GET['id'] : die("ID não especificado.");

            // Buscar o user_id na tabela users
            $sql_user = "SELECT id, user_id FROM users WHERE id = ?";
            $stmt_user = $conn->prepare($sql_user);
            $stmt_user->bind_param("s", $user_id_var);
            $stmt_user->execute();
            $user_result = $stmt_user->get_result();

            if ($user_result->num_rows === 0) {
                die("Usuário não encontrado.");
            }

            $user_row = $user_result->fetch_assoc();
            $user_id = $user_row['user_id']; // Obter o user_id para filtrar as solicitações
            
            // Número de registros por página
            $limit = 10;

            // Página atual
            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $offset = ($page - 1) * $limit;

            // Atualizar a consulta SQL para buscar as solicitações usando o user_id
            $sql_solicitacoes = "SELECT id, externalreference, amount, client_name, client_document, client_email, real_data, status, paymentcode, adquirente_ref, deposito_liquido 
                     FROM solicitacoes 
                     WHERE user_id = ? 
                     ORDER BY id DESC 
                     LIMIT ? OFFSET ?";
            $stmt_solicitacoes = $conn->prepare($sql_solicitacoes);
            $stmt_solicitacoes->bind_param("sii", $user_id, $limit, $offset); // 's' para string, 'i' para inteiro
            $stmt_solicitacoes->execute();
            $result_solicitacoes = $stmt_solicitacoes->get_result();

            // Contar o total de registros para a paginação
            $sql_count = "SELECT COUNT(*) as total FROM solicitacoes WHERE user_id = ?";
            $stmt_count = $conn->prepare($sql_count);
            $stmt_count->bind_param("s", $user_id);
            $stmt_count->execute();
            $total_result = $stmt_count->get_result()->fetch_assoc();
            $total_records = $total_result['total'];
            $total_pages = ceil($total_records / $limit);

            // Calcular somas dos depósitos aprovados e depósitos líquidos aprovados
            $sql_somas = "SELECT SUM(deposito_liquido) as total_liquido, SUM(amount) as total_aprovado 
              FROM solicitacoes 
              WHERE user_id = ? AND status = 'PAID_OUT'";
            $stmt_somas = $conn->prepare($sql_somas);
            $stmt_somas->bind_param("s", $user_id);
            $stmt_somas->execute();
            $result_somas = $stmt_somas->get_result()->fetch_assoc();
            $total_liquido = $result_somas['total_liquido'] ? number_format($result_somas['total_liquido'], 2) : '0.00';
            $total_aprovado = $result_somas['total_aprovado'] ? number_format($result_somas['total_aprovado'], 2) : '0.00';

            // Calcular quantas linhas tem com aquele user_id
            $sql_pix_gerados = "SELECT COUNT(*) as total_pix FROM solicitacoes WHERE user_id = ?";
            $stmt_pix_gerados = $conn->prepare($sql_pix_gerados);
            $stmt_pix_gerados->bind_param("s", $user_id);
            $stmt_pix_gerados->execute();
            $result_pix_gerados = $stmt_pix_gerados->get_result()->fetch_assoc();
            $pix_gerados = $result_pix_gerados['total_pix']; // Contagem de registros de PIX
            
            // Calcular o lucro da plataforma (depósito - depósito líquido)
            $lucro_plataforma = ($result_somas['total_aprovado'] - $result_somas['total_liquido']) ? number_format($result_somas['total_aprovado'] - $result_somas['total_liquido'], 2) : '0.00';

            // Fechar a conexão
            $stmt_user->close();
            $stmt_solicitacoes->close();
            $stmt_count->close();
            $stmt_somas->close();
            $stmt_pix_gerados->close();
            $conn->close();
            ?>









            <!-- Start:: row-1 -->
            <div class="row">
                <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                    <div class="card custom-card">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <div>
                                        <span class="d-block mb-2">Depositos aprovados </span>
                                        <h5 class="mb-4 fs-4">R$ <?php echo $total_aprovado; ?></h5>
                                    </div>
                                    <span class="text-success me-2 fw-medium d-inline-block"></span>
                                    <span class="text-muted">TOTAL</span>
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
                                        <span class="d-block mb-2">Depositos liquido aprovado</span>
                                        <h5 class="mb-4 fs-4">R$ <?php echo $total_liquido; ?></h5>
                                    </div>
                                    <span class="text-success me-2 fw-medium d-inline-block"></span>
                                    <span class="text-muted">TOTAL</span>
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
                                        <span class="d-block mb-2">PIX Gerados</span>
                                        <h5 class="mb-4 fs-4"> <?php echo $pix_gerados; ?></h5>
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
                                        <span class="d-block mb-2">Lucro para a Plataforma</span>
                                        <h5 class="mb-4 fs-4">R$ <?php echo $lucro_plataforma; ?> </h5>
                                    </div>
                                    <span class="text-danger me-2 fw-medium d-inline-block">
                                    </span><span class="text-muted">TOTAL</span>
                                </div>
                                <div>
                                    <div class="main-card-icon orange">
                                        <div class="avatar avatar-lg avatar-rounded bg-primary-transparent svg-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                                fill="#000000" viewBox="0 0 256 256">
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
        </div>
        <!-- End:: row-1 -->





        <!-- Start:: row-3 -->
        <div class="row">
            <div class="card custom-card overflow-hidden">
                <div class="card-header justify-content-between">
                    <div class="card-title">
                        TRANSAÇÕES RECENTES DO USUARIO: <?= htmlspecialchars($user_row['user_id']) ?>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table text-nowrap">
                            <thead>
                                <tr>
                                    <th scope="col">Order ID</th>
                                    <th scope="col">Método de Pagamento</th>
                                    <th scope="col">Valor</th>
                                    <th scope="col">Depósito Líquido</th>
                                    <th scope="col">Adquirente Ref</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Ação</th> <!-- Nova coluna para o botão -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result_solicitacoes->fetch_assoc()): ?>
                                    <?php
                                    // Determinar o badge de status
                                    if ($row['status'] == 'PAID_OUT') {
                                        $status_badge = "<span class='text-success'>Completed</span>";
                                    } elseif ($row['status'] == 'WAITING_FOR_APPROVAL') {
                                        $status_badge = "<span class='text-info'>Pending</span>";
                                    } else {
                                        $status_badge = $row['status'];
                                    }
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                                        <td>
                                            <div class="d-flex align-items-start gap-2">
                                                <div>
                                                    <span class="avatar avatar-sm bg-success-transparent">
                                                        <i class="ri-wallet-3-line fs-18"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="d-block fw-medium mb-1">PIX CASH IN</span>
                                                    <span class="d-block fs-11 text-muted">Online Transaction</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block fw-medium mb-1">R$
                                                    <?php echo number_format($row['amount'], 2); ?></span>
                                                <span
                                                    class="d-block fs-11 text-muted"><?php echo date('M d, Y', strtotime($row['real_data'])); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block fw-medium mb-1">R$
                                                    <?php echo number_format($row['deposito_liquido'], 2); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span
                                                    class="d-block fw-medium mb-1"><?php echo htmlspecialchars($row['adquirente_ref']); ?></span>
                                            </div>
                                        </td>
                                        <td><?php echo $status_badge; ?></td>
                                        <td>
                                            <a href="deposito_details.php?id=<?php echo htmlspecialchars($row['id']); ?>"
                                                class="btn btn-info">Detalhes</a> <!-- Botão para detalhes -->
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Paginação -->
                    <div class="pagination">
                        <a href="?id=<?php echo $user_id_var; ?>&page=<?php echo $page - 1; ?>" class="pagination-link <?php if ($page == 1)
                                    echo 'disabled'; ?>" aria-label="Previous">&laquo; Previous</a>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?id=<?php echo $user_id_var; ?>&page=<?php echo $i; ?>" class="pagination-link <?php if ($i == $page)
                                      echo 'active'; ?>"><?php echo $i; ?></a>
                        <?php endfor; ?>

                        <a href="?id=<?php echo $user_id_var; ?>&page=<?php echo $page + 1; ?>" class="pagination-link <?php if ($page == $total_pages)
                                    echo 'disabled'; ?>" aria-label="Next">Next &raquo;</a>
                    </div>
                </div>
            </div>
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