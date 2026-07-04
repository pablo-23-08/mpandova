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
        <link rel="stylesheet" href="assets/css/output.css">
        <link rel="icon" type="image/webp" href="assets/img/logo.webp">
    </head>
<body class="min-h-screen flex flex-col bg-slate-50 text-slate-900">

<div class="fixed inset-0 -z-10">
    <img src="assets/img/bg.webp" alt="" class="h-full w-full object-cover" aria-hidden="true">
    <!-- <div class="absolute inset-0 bg-[#071d3b]/65"></div> -->
</div>

<header class="border-b border-white/15 bg-[#071d3b]/80 text-white backdrop-blur-md">
    <section class="mx-auto flex w-full max-w-6xl flex-col gap-4 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
        <a href="index.php" class="flex items-center gap-3">
            <img src="assets/img/logo.webp" class="h-10 w-10 rounded-lg object-cover" alt="Logo Mpandova">
            <span class="text-xl font-extrabold tracking-tight">Mpandova</span>
        </a>

        <nav class="flex flex-wrap items-center gap-2 sm:gap-3">
            <?php if (isset($_SESSION['id_utilisateur'])): ?>
                <?php
                    $dashboard = match($_SESSION['role']) {
                        'etudiant'      => 'index.php?route=etudiant/accueil',
                        'etablissement' => 'index.php?route=etablissement/accueil',
                        default         => 'index.php',
                    };
                ?>
                <a href="<?= $dashboard ?>" class="rounded-lg border border-white/25 px-4 py-2 text-sm font-semibold text-white hover:border-[#f1b456] hover:text-[#f1b456]">
                    Mon espace
                </a>
                <a href="index.php?route=auth/logout" class="rounded-lg bg-[#f1b456] px-4 py-2 text-sm font-bold text-[#071d3b] hover:bg-[#e4a744]">
                    Déconnexion
                </a>
            <?php else: ?>
                <a href="index.php?route=auth/login" class="rounded-lg border border-white/25 px-4 py-2 text-sm font-semibold text-white hover:border-[#f1b456] hover:text-[#f1b456]">
                    Se connecter
                </a>
                <a href="index.php?route=auth/register" class="rounded-lg bg-[#f1b456] px-4 py-2 text-sm font-bold text-[#071d3b] hover:bg-[#e4a744]">
                    S'inscrire
                </a>
            <?php endif; ?>
        </nav>
    </section>
</header>

<?php
    $flash = get_flash();
    if ($flash):
        $colors = [
            'success' => 'bg-emerald-500/95 text-white',
            'error'   => 'bg-rose-500/95 text-white',
            'info'    => 'bg-sky-500/95 text-white',
        ];
        $class = $colors[$flash['type']] ?? 'bg-slate-700/95 text-white';
?>
    <div class="<?= $class ?> border-b border-white/20 px-4 py-3 text-center text-sm font-semibold" role="alert">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
<?php endif; ?>
