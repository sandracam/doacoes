<?php
require_once 'config/database.php';
require_once 'includes/header.php';

// Verifica se é admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Processar exclusão
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $query = "DELETE FROM categorias WHERE id = ?";
    $stmt = $db->prepare($query);
    if ($stmt->execute([$id])) {
        $_SESSION['message'] = 'Categoria excluída com sucesso!';
        $_SESSION['message_type'] = 'success';
    }
}

// Processar adição/edição
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete'])) {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    
    if (isset($_POST['id'])) {
        // Edição
        $query = "UPDATE categorias SET nome = ?, descricao = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        if ($stmt->execute([$nome, $descricao, $_POST['id']])) {
            $_SESSION['message'] = 'Categoria atualizada com sucesso!';
            $_SESSION['message_type'] = 'success';
        }
    } else {
        // Nova categoria
        $query = "INSERT INTO categorias (nome, descricao) VALUES (?, ?)";
        $stmt = $db->prepare($query);
        if ($stmt->execute([$nome, $descricao])) {
            $_SESSION['message'] = 'Categoria adicionada com sucesso!';
            $_SESSION['message_type'] = 'success';
        }
    }
}

// Buscar todas as categorias
$query = "SELECT * FROM categorias ORDER BY nome";
$stmt = $db->prepare($query);
$stmt->execute();
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row mb-4">
    <div class="col">
        <h2>Gerenciar Categorias</h2>
    </div>
    <div class="col text-end">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoriaModal">
            Nova Categoria
        </button>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categorias as $categoria): ?>
            <tr>
                <td><?php echo htmlspecialchars($categoria['nome']); ?></td>
                <td><?php echo htmlspecialchars($categoria['descricao']); ?></td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="editarCategoria(<?php echo htmlspecialchars(json_encode($categoria)); ?>)">
                        Editar
                    </button>
                    <form method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?');">
                        <input type="hidden" name="id" value="<?php echo $categoria['id']; ?>">
                        <button type="submit" name="delete" class="btn btn-sm btn-danger">Excluir</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal para adicionar/editar categoria -->
<div class="modal fade" id="categoriaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Categoria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="categoriaId">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editarCategoria(categoria) {
    document.getElementById('categoriaId').value = categoria.id;
    document.getElementById('nome').value = categoria.nome;
    document.getElementById('descricao').value = categoria.descricao;
    new bootstrap.Modal(document.getElementById('categoriaModal')).show();
}
</script>

<?php require_once 'includes/footer.php'; ?> 