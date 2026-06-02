<?php
    //demarrage de session unique et securise
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime'=>0,
            'path'    =>'/',
            'secure'  =>false, //mettre true en production(HTTPS)
            'httponly'=>true,
        ]);
        session_start();
    }

    
    //authentification
        //redirection vers l'accueil if not connected
        function check_auth():void
        {
            if (!isset($_SESSION['id_utilisateur'])) {
                header("Location:index.php");
                exit();
            }

            global $pdo;
            $stmt = $pdo->prepare('SELECT * FROM session WHERE id_session = :sid');
            $stmt->execute([':sid' => session_id()]);
            $sessionRow = $stmt->fetch();

            if (!$sessionRow || $sessionRow['id_utilisateur'] !== $_SESSION['id_utilisateur']) {
                header('Location: index.php');
                exit();
            }

            // mettre à jour la date d'activité
            $stmt = $pdo->prepare('UPDATE session SET initial = :time WHERE id_session = :sid');
            $stmt->execute([':time' => time(), ':sid' => session_id()]);
        }

        //redirection if role is not the same
        function check_role(string $role):void
        {
            check_auth();
            if ($_SESSION['role'] !== $role) {
                header("Location:index.php");
                exit();
            }
        }

        //redirection vers accueil_*.php if already connected 
        function redirect_if_logged():void
        {
            if (!isset($_SESSION['id_utilisateur'])) {
                return;
            }

            // Valider la session en base avant de rediriger (évite les boucles)
            global $pdo;
            $stmt = $pdo->prepare('SELECT * FROM session WHERE id_session = :sid');
            $stmt->execute([':sid' => session_id()]);
            $sessionRow = $stmt->fetch();

            if (!$sessionRow || $sessionRow['id_utilisateur'] !== $_SESSION['id_utilisateur']) {
                // session invalide ou supprimée : nettoyer côté serveur et ne pas rediriger
                $_SESSION = [];
                if (ini_get("session.use_cookies")) {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), '', time() - 42000,
                        $params['path'], $params['domain'], $params['secure'], $params['httponly']
                    );
                }
                @session_destroy();
                return;
            }

            // session valide : mettre à jour l'activité et rediriger selon le rôle
            $stmt = $pdo->prepare('UPDATE session SET initial = :time WHERE id_session = :sid');
            $stmt->execute([':time' => time(), ':sid' => session_id()]);

            $destinations=[
                'etudiant'     =>'accueil_etudiant.php',
                'etablissement'=>'accueil_etablissement.php',
            ];

            $role=$_SESSION['role'] ?? '';
            $url =$destinations[$role] ?? 'index.php';

            header("Location:$url");
            exit();
        }


    //messages flash
        //enregistre un message flash en session
        function set_flash(string $type, string $message):void
        {
            $_SESSION['flash']=['type'=>$type, 'message'=>$message];
        }

        //recuperation et suppression du message flash (retourne null si absent)
        function get_flash():?array
        {
            if (!isset($_SESSION['flash'])) {
                return null;
            }
            $flash=$_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }