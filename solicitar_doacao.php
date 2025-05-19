<?php
require_once 'config/database.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['doacao_id'])) {
    $database = new Database();
    $db = $database->getConnection();
    
    $doacao_id = $_POST['doacao_id'];
    
    // Verificar se o livro ainda está disponível
    $query = "SELECT d.*, l.titulo, u.nome as doador, u.email as doador_email 
              FROM doacoes d 
              JOIN livros l ON d.id_livro = l.id 
              JOIN usuarios u ON d.id_doador = u.id 
              WHERE d.id = ? AND d.status = 'Disponível'";
    $stmt = $db->prepare($query);
    $stmt->execute([$doacao_id]);
    
    if ($doacao = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Atualizar status da doação
        $query = "UPDATE doacoes SET status = 'Reservado', id_receptor = ?, data_retirada = NOW() WHERE id = ?";
        $stmt = $db->prepare($query);
        
        if ($stmt->execute([$_SESSION['user_id'], $doacao_id])) {
            $_SESSION['message'] = 'Solicitação de doação realizada com sucesso! Entre em contato com o doador.';
            $_SESSION['message_type'] = 'success';
            header('Location: minhas_doacoes.php');
            exit;
        } else {
            $_SESSION['message'] = 'Erro ao solicitar doação!';
            $_SESSION['message_type'] = 'danger';
        }
    } else {
        $_SESSION['message'] = 'Este livro não está mais disponível!';
        $_SESSION['message_type'] = 'warning';
    }
    
    header('Location: livros.php');
    exit;
} else {
    header('Location: livros.php');
    exit;
} 