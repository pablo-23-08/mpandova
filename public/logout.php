<?php
    require_once "../config/bootstrap.php";

    //Supprimer la session de la base mysql
    $stmt = $pdo->prepare('DELETE FROM session WHERE id_session = :sid');
    $stmt->execute([':sid' => session_id()]);

    //vider toutes les variables de session
    $_SESSION = [];

    //Destruction de cookie de session
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    session_destroy();
    header("Location: index.php");
    exit();