<?php
require_once 'config/database.php';
require_once 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    
    $query = "SELECT id, nome, senha, CASE WHEN email = 'admin@admin.com' THEN 'admin' ELSE 'user' END as tipo 
              FROM usuarios WHERE email = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$email]);
    
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (password_verify($senha, $row['senha'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_nome'] = $row['nome'];
            $_SESSION['user_type'] = $row['tipo'];
            $_SESSION['message'] = 'Bem-vindo(a) ' . $row['nome'] . '!';
            $_SESSION['message_type'] = 'success';
            header('Location: index.php');
            exit;
        }
    }
    
    $_SESSION['message'] = 'Email ou senha inválidos!';
    $_SESSION['message_type'] = 'danger';
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="text-center">Login</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Entrar</button>
                    </div>
                </form>
                <div class="text-center mt-3">
                    <p>Não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 