<?php
require_once 'config/database.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Processar confirmação de doação
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_doacao'])) {
    $doacao_id = $_POST['doacao_id'];
    $query = "UPDATE doacoes SET status = 'Doado' WHERE id = ? AND id_doador = ?";
    $stmt = $db->prepare($query);
    
    if ($stmt->execute([$doacao_id, $_SESSION['user_id']])) {
        $_SESSION['message'] = 'Doação confirmada com sucesso!';
        $_SESSION['message_type'] = 'success';
    }
}

// Buscar livros que doei
$query = "SELECT l.titulo, l.estado_conservacao, c.nome as categoria, 
          d.status, d.data_doacao, d.data_retirada, d.id as doacao_id,
          u.nome as receptor, u.email as receptor_email
          FROM doacoes d
          JOIN livros l ON d.id_livro = l.id
          JOIN categorias c ON l.id_categoria = c.id
          LEFT JOIN usuarios u ON d.id_receptor = u.id
          WHERE d.id_doador = ?
          ORDER BY d.data_doacao DESC";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$minhas_doacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar livros que recebi
$query = "SELECT l.titulo, l.estado_conservacao, c.nome as categoria,
          d.status, d.data_doacao, d.data_retirada, d.id as doacao_id,
          u.nome as doador, u.email as doador_email
          FROM doacoes d
          JOIN livros l ON d.id_livro = l.id
          JOIN categorias c ON l.id_categoria = c.id
          JOIN usuarios u ON d.id_doador = u.id
          WHERE d.id_receptor = ?
          ORDER BY d.data_retirada DESC";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$livros_recebidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#minhas-doacoes">Minhas Doações</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#livros-recebidos">Livros Recebidos</a>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="minhas-doacoes">
        <h3 class="mb-4">Livros que Doei</h3>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Categoria</th>
                        <th>Estado</th>
                        <th>Status</th>
                        <th>Data Doação</th>
                        <th>Receptor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($minhas_doacoes as $doacao): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($doacao['titulo']); ?></td>
                        <td><?php echo htmlspecialchars($doacao['categoria']); ?></td>
                        <td><?php echo htmlspecialchars($doacao['estado_conservacao']); ?></td>
                        <td><?php echo htmlspecialchars($doacao['status']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($doacao['data_doacao'])); ?></td>
                        <td>
                            <?php if ($doacao['receptor']): ?>
                                <?php echo htmlspecialchars($doacao['receptor']); ?><br>
                                <small><?php echo htmlspecialchars($doacao['receptor_email']); ?></small>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($doacao['status'] === 'Reservado'): ?>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="doacao_id" value="<?php echo $doacao['doacao_id']; ?>">
                                <button type="submit" name="confirmar_doacao" class="btn btn-sm btn-success">
                                    Confirmar Doação
                                </button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="tab-pane fade" id="livros-recebidos">
        <h3 class="mb-4">Livros que Recebi</h3>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Categoria</th>
                        <th>Estado</th>
                        <th>Status</th>
                        <th>Data Retirada</th>
                        <th>Doador</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($livros_recebidos as $livro): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($livro['titulo']); ?></td>
                        <td><?php echo htmlspecialchars($livro['categoria']); ?></td>
                        <td><?php echo htmlspecialchars($livro['estado_conservacao']); ?></td>
                        <td><?php echo htmlspecialchars($livro['status']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($livro['data_retirada'])); ?></td>
                        <td>
                            <?php echo htmlspecialchars($livro['doador']); ?><br>
                            <small><?php echo htmlspecialchars($livro['doador_email']); ?></small>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 