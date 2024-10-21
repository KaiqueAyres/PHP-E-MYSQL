<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "faesa_landing";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Variáveis para armazenar erros e mensagens de sucesso
$mensagem_sucesso = "";
$mensagem_erro = "";

// Processamento do formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar e sanitizar os dados do formulário
    $nome = htmlspecialchars(trim($_POST['nome']));
    $email = htmlspecialchars(trim($_POST['email']));
    $telefone = htmlspecialchars(trim($_POST['telefone']));
    $mensagem = htmlspecialchars(trim($_POST['mensagem']));

    // Validação dos campos
    if (empty($nome)) {
        $mensagem_erro = "O nome é obrigatório!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem_erro = "Formato de e-mail inválido!";
    } elseif (!preg_match('/^\(\d{2}\)\s?\d{4,5}-\d{4}$/', $telefone)) {
        $mensagem_erro = "Formato de telefone inválido! Use (99) 99999-9999.";
    } elseif (strlen($mensagem) < 10) {
        $mensagem_erro = "A mensagem deve ter pelo menos 10 caracteres!";
    } else {
        // Preparar SQL para inserir dados
        $sql = "INSERT INTO usuarios (nome, email, telefone, mensagem) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssss", $nome, $email, $telefone, $mensagem);

            if ($stmt->execute()) {
                $mensagem_sucesso = "Dados enviados com sucesso!";
            } else {
                $mensagem_erro = "Erro ao enviar dados: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $mensagem_erro = "Erro ao preparar o SQL: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page Faesa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Imagem de destaque acima do cabeçalho -->
    <img src="images/new-header-image.jpg" alt="Imagem de Destaque" class="header-image">

    <header>
        <h1>Bem-vindo à Faesa</h1>
        <p>Preencha o formulário abaixo para entrar em contato</p>
    </header>

    <main>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <label for="nome">Nome</label>
            <input type="text" id="nome" name="nome" required>

            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required>

            <label for="telefone">Telefone</label>
            <input type="text" id="telefone" name="telefone" required placeholder="(99) 99999-9999">

            <label for="mensagem">Mensagem</label>
            <textarea id="mensagem" name="mensagem" required minlength="10"></textarea>

            <button type="submit">Enviar</button>
        </form>

        <!-- Exibir mensagem de sucesso ou erro -->
        <?php if (!empty($mensagem_sucesso)): ?>
            <p class="sucesso"><?php echo $mensagem_sucesso; ?></p>
        <?php elseif (!empty($mensagem_erro)): ?>
            <p class="erro"><?php echo $mensagem_erro; ?></p>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2024 Faesa</p>
    </footer>

</body>
</html>
