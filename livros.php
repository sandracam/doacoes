<?php
require_once 'config/database.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Processar doação de livro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $id_categoria = $_POST['categoria'];
    $ano_publicacao = $_POST['ano_publicacao'];
    $editora = $_POST['editora'];
    $estado_conservacao = $_POST['estado_conservacao'];
    $descricao = $_POST['descricao'];
    
    // Inserir livro
    $query = "INSERT INTO livros (titulo, id_categoria, ano_publicacao, editora, estado_conservacao, descricao) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    
    if ($stmt->execute([$titulo, $id_categoria, $ano_publicacao, $editora, $estado_conservacao, $descricao])) {
        $id_livro = $db->lastInsertId();
        
        // Registrar doação
        $query = "INSERT INTO doacoes (id_livro, id_doador, status) VALUES (?, ?, 'Disponível')";
        $stmt = $db->prepare($query);
        
        if ($stmt->execute([$id_livro, $_SESSION['user_id']])) {
            $_SESSION['message'] = 'Livro cadastrado para doação com sucesso!';
            $_SESSION['message_type'] = 'success';
        }
    } else {
        $_SESSION['message'] = 'Erro ao cadastrar livro!';
        $_SESSION['message_type'] = 'danger';
    }
}

// Buscar categorias para o formulário
$query = "SELECT id, nome FROM categorias ORDER BY nome";
$stmt = $db->prepare($query);
$stmt->execute();
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar livros disponíveis
$query = "SELECT l.*, c.nome as categoria, u.nome as doador, d.status, d.id as doacao_id, d.id_doador
          FROM livros l
          JOIN categorias c ON l.id_categoria = c.id
          JOIN doacoes d ON l.id = d.id_livro
          JOIN usuarios u ON d.id_doador = u.id
          WHERE d.status = 'Disponível'
          ORDER BY d.data_doacao DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$livros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row mb-4">
    <div class="col">
        <h2>Livros Disponíveis para Doação</h2>
    </div>
    <div class="col text-end">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#livroModal">
            Doar Livro
        </button>
    </div>
</div>

<div class="row">
    <?php foreach ($livros as $livro): ?>
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($livro['titulo']); ?></h5>
                <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($livro['categoria']); ?></h6>
                <p class="card-text">
                    <strong>Editora:</strong> <?php echo htmlspecialchars($livro['editora']); ?><br>
                    <strong>Ano:</strong> <?php echo htmlspecialchars($livro['ano_publicacao']); ?><br>
                    <strong>Estado:</strong> <?php echo htmlspecialchars($livro['estado_conservacao']); ?><br>
                    <strong>Doador:</strong> <?php echo htmlspecialchars($livro['doador']); ?>
                </p>
                <p class="card-text"><?php echo htmlspecialchars($livro['descricao']); ?></p>
                <?php if ($livro['id_doador'] !== $_SESSION['user_id']): ?>
                <form method="POST" action="solicitar_doacao.php">
                    <input type="hidden" name="doacao_id" value="<?php echo $livro['doacao_id']; ?>">
                    <button type="submit" class="btn btn-success">Solicitar Livro</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Modal para doar livro -->
<div class="modal fade" id="livroModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Doar Livro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" required>
                    </div>
                    <div class="mb-3">
                        <label for="categoria" class="form-label">Categoria</label>
                        <select class="form-select" id="categoria" name="categoria" required>
                            <option value="">Selecione uma categoria</option>
                            <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo $categoria['id']; ?>">
                                <?php echo htmlspecialchars($categoria['nome']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="ano_publicacao" class="form-label">Ano de Publicação</label>
                        <input type="number" class="form-control" id="ano_publicacao" name="ano_publicacao" required>
                    </div>
                    <div class="mb-3">
                        <label for="editora" class="form-label">Editora</label>
                        <input type="text" class="form-control" id="editora" name="editora" required>
                    </div>
                    <div class="mb-3">
                        <label for="estado_conservacao" class="form-label">Estado de Conservação</label>
                        <select class="form-select" id="estado_conservacao" name="estado_conservacao" required>
                            <option value="Novo">Novo</option>
                            <option value="Ótimo">Ótimo</option>
                            <option value="Bom">Bom</option>
                            <option value="Regular">Regular</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Doar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 