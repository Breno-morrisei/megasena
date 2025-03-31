<?php
session_start();
if (!isset($_SESSION['email']) && !isset($_COOKIE['email'])) {
   header("Location: login.php");
   exit;
}
$email = $_SESSION['email'] ?? $_COOKIE['email'];
function gerarNumerosMegaSena() {
   $numeros = range(1, 60);
   shuffle($numeros);
   return array_slice($numeros, 0, 6);
}
function contarAcertos($aposta, $sorteio) {
   return count(array_intersect($aposta, $sorteio));
}
function salvarAposta($aposta, $sorteio, $acertos) {
   if (!is_dir('historico_apostas')) {
       mkdir('historico_apostas', 0777, true);
   }
   $id = uniqid();
   $data = date('Y-m-d H:i:s');
   $conteudo = "ID: $id\n";
   $conteudo .= "Aposta: " . implode(", ", $aposta) . "\n";
   $conteudo .= "Sorteio: " . implode(", ", $sorteio) . "\n";
   $conteudo .= "Acertos: $acertos\n";
   $conteudo .= "Data: $data\n";
   file_put_contents("historico_apostas/$id.txt", $conteudo);
}
if (isset($_GET['logout'])) {
   setcookie('email', '', time() - 3600);
   session_destroy();
   header("Location: login.php");
   exit;
}
if (isset($_GET['excluir'])) {
   $id_excluir = $_GET['excluir'];
   $arquivo = "historico_apostas/$id_excluir.txt";
   if (file_exists($arquivo)) {
       unlink($arquivo);
   }
   header("Location: index.php");
   exit;
}
$resultado = "";
$meusNumeros = [];
$sorteio = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['numeros'])) {
   $meusNumeros = array_map('intval', $_POST['numeros']);
   $meusNumeros = array_unique($meusNumeros);
   sort($meusNumeros);
   if (count($meusNumeros) === 6) {
       $sorteio = gerarNumerosMegaSena();
       sort($sorteio);
       $acertos = contarAcertos($meusNumeros, $sorteio);
       $resultado = "Seus números: " . implode(", ", $meusNumeros) . "<br>";
       $resultado .= "Números sorteados: " . implode(", ", $sorteio) . "<br>";
       $resultado .= "Você acertou <strong>$acertos</strong> números!<br>";
       if ($acertos == 6) {
           $resultado .= "<span class='text-success'>Parabéns! Você ganhou na Mega-Sena!</span>";
       } elseif ($acertos == 5) {
           $resultado .= "<span class='text-warning'Quase lá, você acertou na Quina!</span>";
       } elseif ($acertos == 4) {
           $resultado .= "<span class='text-info'>Legal, você fez uma Quadra!</span>";
       } else {
           $resultado .= "<span class='text-danger'>Não foi dessa vez, tente novamente!</span>";
       }
       salvarAposta($meusNumeros, $sorteio, $acertos);
   } else {
       $resultado = "<span class='text-danger'>Escolha exatamente 6 números distintos entre 1 e 60.</span>";
   }
}
$arquivos = glob("historico_apostas/*.txt");
$historico = [];
foreach ($arquivos as $arquivo) {
   $conteudo = file_get_contents($arquivo);
   $linhas = explode("\n", $conteudo);
   $dados = [];
   foreach ($linhas as $linha) {
       if (strpos($linha, "ID: ") === 0) {
           $dados['id'] = trim(str_replace("ID: ", "", $linha));
       } elseif (strpos($linha, "Aposta: ") === 0) {
           $dados['aposta'] = explode(", ", trim(str_replace("Aposta: ", "", $linha)));
       } elseif (strpos($linha, "Sorteio: ") === 0) {
           $dados['sorteio'] = explode(", ", trim(str_replace("Sorteio: ", "", $linha)));
       } elseif (strpos($linha, "Acertos: ") === 0) {
           $dados['acertos'] = trim(str_replace("Acertos: ", "", $linha));
       } elseif (strpos($linha, "Data: ") === 0) {
           $dados['data'] = trim(str_replace("Data: ", "", $linha));
       }
   }
   if (!empty($dados)) {
       $historico[] = $dados;
   }
}
if (isset($_GET['baixar_historico'])) {
   $zip = new ZipArchive();
   $zipNome = "historico_apostas.zip";
   if ($zip->open($zipNome, ZipArchive::CREATE) === true) {
       foreach ($arquivos as $arquivo) {
           $zip->addFile($arquivo, basename($arquivo));
       }
       $zip->close();
       header('Content-Type: application/zip');
       header('Content-Disposition: attachment; filename="' . $zipNome . '"');
       header('Content-Length: ' . filesize($zipNome));
       readfile($zipNome);
       unlink($zipNome);
       exit;
   }
}
?>
<!DOCTYPE html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mega-Sena - Apostas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5" style="max-width: 500px;">
        <div class="card shadow p-3">
            <img src="img/sena.png" class="card-img-top" alt="Mega-Sena">
            <div class="card-body text-center">
                <h3>Olá, <?php echo htmlspecialchars($email); ?>!</h3>
                <a href="?logout=true" class="btn btn-danger mb-3">Sair</a>
                <a href="loja.php" class="btn btn-primary mb-3">Ir para a Loja</a>
                <p class="text-success">Escolha 6 números entre 1 e 60 e tente acertar os números sorteados!</p>

                <form method="POST">
                    <div class="row g-2">
                        <?php for ($i = 0; $i < 6; $i++): ?>
                            <div class="col-4">
                                <input type="number" name="numeros[]" min="1" max="60" class="form-control" required>
                            </div>
                        <?php endfor; ?>
                    </div>
                    <button type="submit" class="btn btn-success mt-3 w-100">Apostar</button>
                </form>

                <div id="result" class="mt-3">
                    <?php if ($resultado) echo "<div class='alert alert-info'>$resultado</div>"; ?>
                </div>

                <div class="mt-4">
                    <h3>Histórico de Apostas</h3>
                    <?php if (!empty($historico)): ?>
                        <ul class="list-group">
                            <?php foreach ($historico as $aposta): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <pre class="mb-0"><?php
                                       echo "Aposta: " . implode(", ", $aposta['aposta']) . "\n";
                                       echo "Sorteio: " . implode(", ", $aposta['sorteio']) . "\n";
                                       echo "Acertos: " . $aposta['acertos'] . "\n";
                                       echo "Data: " . $aposta['data'];
                                   ?></pre>
                                    <a href="?excluir=<?php echo htmlspecialchars($aposta['id']); ?>" class="btn btn-danger btn-sm">Excluir</a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Você ainda não fez nenhuma aposta.</p>
                    <?php endif; ?>
                </div>

                <a href="?baixar_historico=true" class="btn btn-secondary mt-3">Baixar Histórico</a>
            </div>
        </div>
    </div>
</body>
</html>