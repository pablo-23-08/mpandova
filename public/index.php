<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mpandova</title>
        <link rel="stylesheet" href="style.css">

        <!-- Tailwind CDN -->
        <!-- <script src="https://cdn.tailwindcss.com"></script> -->

        <!-- Couleurs personnalisées -->
        <!-- <script>
            tailwind.config = {
            theme: {
                extend: {
                        colors: {
                            [#071d3b]: '#071d3b',
                            [#f1b456]: '#f1b456'
                        }
                    }
                }
            }
        </script> -->

    </head>

    <body class="bg-gray-100">

        <!-- NAVBAR -->
        <header class="bg-[#071d3b] text-white">
            <div class="max-w-6xl mx-auto flex justify-between items-center p-4">

                <div class="flex items-center gap-2">
                    <img src="../assets/img/logo.webp" class="h-10">
                    <span class="font-bold text-xl">Mpandova</span>
                </div>

                <div class="space-x-4">
                    <?php if(isset($_SESSION['user'])){ ?>
                    <a href="dashboard.php" class="hover:text-[#f1b456]">Dashboard</a>
                    <a href="logo31ut.php" class="bg-[#f1b456] text-[#071d3b] px-4 py-2 rounded-lg">logo31ut</a>
                    <?php } else { ?>
                    <a href="login.php" class="hover:text-[#f1b456]">Login</a>
                    <a href="register.php" class="bg-[#f1b456] text-[#071d3b] px-4 py-2 rounded-lg">Register</a>
                    <?php } ?>
                </div>

            </div>
        </header>

        <!-- HERO SECTION -->
        <section class="max-w-6xl mx-auto mt-16 px-4">

            <div class="grid md:grid-cols-2 gap-10 items-center">

                <!-- TEXTE -->
                <div>
                    <h1 class="text-4xl font-bold text-[#071d3b] mb-6">
                        Trouve ta voie académique facilement
                    </h1>

                    <p class="text-gray-600 mb-6">
                        Mpandova t’aide à choisir une filière et un établissement selon ton profil.
                        Plus de confusion après le bac.
                    </p>

                    <div class="flex gap-4">
                        <a href="register.php" class="bg-[#f1b456] text-[#071d3b] px-6 py-3 rounded-lg font-bold">
                            Commencer
                        </a>

                        <a href="login.php" class="border border-[#071d3b] text-[#071d3b] px-6 py-3 rounded-lg">
                            Se connecter
                        </a>
                    </div>
                </div>

                <!-- IMAGE -->
                <div>
                    <img src="../assets/img/logo.webp" class="w-64 mx-auto">
                </div>

            </div>

        </section>

        <!-- FEATURES -->
        <section class="max-w-6xl mx-auto mt-20 px-4">

            <h2 class="text-2xl font-bold text-center text-[#071d3b] mb-10">
            Pourquoi utiliser Mpandova ?
            </h2>

            <div class="grid md:grid-cols-3 gap-6">

                <div class="bg-white p-6 rounded-xl shadow">
                    <h3 class="font-bold mb-2">🎯 Recommandations</h3>
                    <p class="text-gray-600">Suggestions adaptées à ton profil</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow">
                    <h3 class="font-bold mb-2">🏫 Établissements</h3>
                    <p class="text-gray-600">Explore les écoles disponibles</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow">
                    <h3 class="font-bold mb-2">📚 Filières</h3>
                    <p class="text-gray-600">Découvre les débouchés</p>
                </div>

            </div>

        </section>

        <!-- FOOTER -->
        <footer class="bg-[#071d3b] text-white mt-20">
            <div class="max-w-6xl mx-auto p-6 text-center">
            <p>© 2026 Mpandova - Orientation académique</p>
            </div>
        </footer>

    </body>
</html>