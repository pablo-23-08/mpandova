<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mpandova – Orientation académique</title>
    <link rel="stylesheet" href="../assets/css/output.css">
    <link rel="icon" type="image/webp" href="../assets/img/logo.webp">
</head>

<body class="min-h-screen flex flex-col">

    <video
        src="../assets/video/video_loop.mp4"
        autoplay loop muted playsinline
        class="absolute -z-10 object-cover h-screen w-full"
        aria-hidden="true"
    ></video>

    <header class="bg-[#071d3b]/70 text-white/60 w-auto h-auto backdrop-blur-sm">
        <section class="max-w-6xl mx-auto flex justify-between items-center p-4">

            <a href="index.php" class="flex items-center gap-2 hover:opacity-80 duration-300">
                <img src="../assets/img/logo.webp" class="h-10" alt="Logo Mpandova">
                <span class="font-bold text-xl text-white">Mpandova</span>
            </a>

            <nav class="flex items-center gap-4">
                <?php if (isset($_SESSION['id_user'])): ?>
                    <?php
                        $dashboard = match($_SESSION['role']) {
                            'etudiant'      => 'accueil_etudiant.php',
                            'etablissement' => 'accueil_etablissement.php',
                            'admin'         => 'accueil_admin.php',
                            default         => 'index.php',
                        };
                    ?>
                    <a href="<?= $dashboard ?>" class="hover:text-[#f1b456] duration-300">
                        Mon espace
                    </a>
                    <a
                        href="logout.php"
                        class="bg-red-500/80 text-white px-4 py-2 rounded-lg hover:bg-red-600 font-bold duration-300"
                    >
                        Déconnexion
                    </a>
                <?php else: ?>
                    <a href="login.php" class="hover:text-[#f1b456] duration-500 hover:translate-y-0.5 transition-transform">
                        Se connecter
                    </a>
                    <a
                        href="register.php"
                        class="bg-[#f1b456] text-[#071d3b] px-4 py-2 rounded-lg hover:bg-[#f1b456]/75 font-bold duration-500 hover:translate-y-0.5 transition-transform"
                    >
                        S'inscrire
                    </a>
                <?php endif; ?>
            </nav>

        </section>
    </header>

    <?php
    // Affichage du message flash s'il existe
    $flash = get_flash();
    if ($flash):
        $colors = [
            'success' => 'bg-green-500/90 text-white',
            'error'   => 'bg-red-500/90 text-white',
            'info'    => 'bg-blue-500/90 text-white',
        ];
        $class = $colors[$flash['type']] ?? 'bg-gray-500/90 text-white';
    ?>
        <div class="<?= $class ?> text-center px-4 py-3 text-sm font-medium" role="alert">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
    <?php endif; ?>