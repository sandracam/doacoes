<?php
require_once 'config/database.php';
require_once 'includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Buscar estatísticas
$stats = [
    'total_livros' => 0,
    'livros_disponiveis' => 0,
    'doacoes_realizadas' => 0,
    'usuarios' => 0
];

$query = "SELECT COUNT(*) as total FROM livros";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['total_livros'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$query = "SELECT COUNT(*) as total FROM doacoes WHERE status = 'Disponível'";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['livros_disponiveis'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$query = "SELECT COUNT(*) as total FROM doacoes WHERE status = 'Doado'";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['doacoes_realizadas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$query = "SELECT COUNT(*) as total FROM usuarios";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['usuarios'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Buscar últimas doações
$query = "SELECT l.titulo, c.nome as categoria, d.data_doacao, u.nome as doador
          FROM doacoes d
          JOIN livros l ON d.id_livro = l.id
          JOIN categorias c ON l.id_categoria = c.id
          JOIN usuarios u ON d.id_doador = u.id
          WHERE d.status = 'Disponível'
          ORDER BY d.data_doacao DESC
          LIMIT 5";
$stmt = $db->prepare($query);
$stmt->execute();
$ultimas_doacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="px-4 py-5 my-5 text-center">
    <h1 class="display-5 fw-bold">Sistema de Doação de Livros</h1>
    <div class="col-lg-6 mx-auto">
        <p class="lead mb-4">
            Compartilhe conhecimento doando seus livros ou encontre livros disponíveis para leitura.
        </p>
        <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
            <a href="cadastro.php" class="btn btn-primary btn-lg px-4 gap-3">Cadastre-se</a>
            <a href="login.php" class="btn btn-outline-secondary btn-lg px-4">Login</a>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="card-title"><?php echo $stats['total_livros']; ?></h3>
                <p class="card-text">Livros Cadastrados</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="card-title"><?php echo $stats['livros_disponiveis']; ?></h3>
                <p class="card-text">Livros Disponíveis</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="card-title"><?php echo $stats['doacoes_realizadas']; ?></h3>
                <p class="card-text">Doações Realizadas</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="card-title"><?php echo $stats['usuarios']; ?></h3>
                <p class="card-text">Usuários Cadastrados</p>
            </div>
        </div>
    </div>
</div>

<?php if ($ultimas_doacoes): ?>
<div class="row">
    <div class="col">
        <h3 class="mb-4">Últimas Doações Disponíveis</h3>
        <div class="list-group">
            <?php foreach ($ultimas_doacoes as $doacao): ?>
            <div class="list-group-item">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1"><?php echo htmlspecialchars($doacao['titulo']); ?></h5>
                    <small><?php echo date('d/m/Y', strtotime($doacao['data_doacao'])); ?></small>
                </div>
                <p class="mb-1">Categoria: <?php echo htmlspecialchars($doacao['categoria']); ?></p>
                <small>Doado por: <?php echo htmlspecialchars($doacao['doador']); ?></small>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-3">
            <a href="livros.php" class="btn btn-primary">Ver Todos os Livros</a>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?> 