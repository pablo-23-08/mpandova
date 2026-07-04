<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Mpandova est une application web d’orientation académique à Madagascar.">
        <meta name="keywords" content="mpandova, madagascar, plateforme, service">

        <meta name="google-site-verification" content="g-ohB8jFR5f-B-mW6SJFFeEVLVoJO5X4OFFI8F60wsU" />

        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Mpandova</title>
        <!-- Le CSS est dans assets/css/output.css, à la racine du projet -->
        <link rel="stylesheet" href="../public/assets/css/output.css">
        <link rel="icon" type="image/webp" href="../public/assets/img/logo.png">
    </head>

    <body class="min-h-screen flex flex-col">

        <!-- Image de fond fixe, derrière tout le contenu (-z-10) -->
        <img src="../public/assets/img/bg.webp" alt=""
            class="fixed top-0 left-0 w-full h-full object-cover -z-10"
            aria-hidden="true"
        />

        <header class="bg-[#071d3b]/70 text-white/60 w-auto h-auto backdrop-blur-sm">
            <section class="max-w-6xl mx-auto flex justify-between items-center p-4">

                <!-- Logo et nom du site -->
                <a href="index.php" class="flex items-center gap-2 hover:opacity-80 duration-300">
                    <img src="../public/assets/img/logo.webp" class="h-10" alt="logo Mpandova">
                    <span class="font-bold text-xl text-white">Mpandova</span>
                </a>

                <!-- Navigation : différente selon l'état de connexion -->
                <nav class="flex items-center gap-4">
                    <?php if (isset($_SESSION['id_utilisateur'])): ?>
                        <?php
                            // match() PHP 8 : comme un switch mais plus concis, retourne une valeur
                            $dashboard = match($_SESSION['role']) {
                                'etudiant'      => 'index.php?route=etudiant/accueil',
                                'etablissement' => 'index.php?route=etablissement/accueil',
                                default         => 'index.php',
                            };
                        ?>
                        <a href="<?= $dashboard ?>" class="hover:text-[#f1b456] duration-300">
                            Mon espace
                        </a>
                        <!-- Lien de déconnexion → route auth/logout -->
                        <a href="index.php?route=auth/logout"
                           class="bg-[#f1b456] text-[#071d3b] px-4 py-2 rounded-lg hover:bg-[#f1b456]/75 font-bold duration-500 hover:translate-y-0.5 transition-transform">
                            Déconnexion
                        </a>
                    <?php else: ?>
                        <a href="index.php?route=auth/login"
                           class="hover:text-[#f1b456] duration-500 hover:translate-y-0.5 transition-transform">
                            Se connecter
                        </a>
                        <a href="index.php?route=auth/register"
                           class="bg-[#f1b456] text-[#071d3b] px-4 py-2 rounded-lg hover:bg-[#f1b456]/75 font-bold duration-500 hover:translate-y-0.5 transition-transform">
                            S'inscrire
                        </a>
                    <?php endif; ?>
                </nav>

            </section>
        </header>

        <?php
            // Afficher le message flash s'il en existe un
            $flash = get_flash(); // Récupère ET supprime le message de la session
            if ($flash):
                $colors = [
                    'success' => 'bg-green-500/90 text-white',
                    'error'   => 'bg-red-500/90 text-white',
                    'info'    => 'bg-blue-500/90 text-white',
                ];
                // ?? : si le type n'est pas dans le tableau, utiliser gris par défaut
                $class = $colors[$flash['type']] ?? 'bg-gray-500/90 text-white';
        ?>
            <!-- role="alert" : les lecteurs d'écran annoncent ce message immédiatement -->
            <div class="<?= $class ?> text-center px-4 py-3 text-sm font-medium" role="alert">
                <!-- htmlspecialchars() protège contre les injections XSS -->
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>