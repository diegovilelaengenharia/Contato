<?php
session_set_cookie_params(0, '/');
session_name('CLIENTE_SESSID');
session_start();
// Auth Check
if (!isset($_SESSION['cliente_id'])) {
    // DEBUG MODE: Stop redirect to see what's happening
    $debug_info = "Session Name: " . session_name() . "<br>";
    $debug_info .= "Session ID: " . session_id() . "<br>";
    $debug_info .= "Cookie: " . print_r($_COOKIE, true) . "<br>";
    $debug_info .= "Session Vars: " . print_r($_SESSION, true) . "<br>";
    die("<h1>DEBUG: Acesso Negado pelo PHP (dashboard.php)</h1><hr>" . $debug_info);
    // header("Location: index.php?error=sessao_expirada");
    exit;
}
?>
<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/png" href="../assets/logo.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vilela Engenharia | Área do Cliente</title>
    <script type="module" crossorigin src="./assets/index-jGMwF09j.js"></script>
    <link rel="stylesheet" crossorigin href="./assets/index-l4boxe_g.css">
  </head>
  <body>
    <div id="root"></div>
  </body>
</html>
