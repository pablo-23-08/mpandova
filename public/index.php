<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>mpandova</title>
        <link rel="stylesheet" href="style.css">
    </head>

    <body>
        <video src="../assets/img/video_loop.mp4" autoplay loop muted class="absolute -z-10 object-cover h-screen w-full"></video>
        
        <header class="bg-[#071d3b]/70 text-white/60 w-auto h-auto " >
            <section class="max-w-6xl mx-auto flex justify-between items-center p-4">
                <div class="flex items-center gap-2">
                    <img src="../assets/img/logo.webp" class="h-10">
                    <span class="font-bold text-xl">Mpandova</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class=" hover:text-[#f1b456] duration-500 hover:translate-y-0.5">
                    <a href="login.php" >Se connecter</a>
                    </div>
                    <div class=" bg-[#f1b456] text-[#071d3b]/60 px-4 py-2 rounded-lg hover:bg-[#f1b456]/75 font-bold duration-500 hover:translate-y-0.5 ">
                    <a href="register.php" >S'inscrire</a>
                    </div>
                </div>
            </section>
        </header>

        <section class="max-w-6xl mx-auto mt-16 px-4">
            <div class="grid md:grid-cols-2 gap-10 items-center">
                <div>
                    <h1 class="text-4xl font-bold text-[#071d3b] text-shadow-lg text-shadow-blue-300 mb-6">
                        Trouve ta voie académique facilement
                    </h1>
                    <p class="text-gray-600 mb-6">
                        Mpandova t’aide à choisir une filière et un établissement selon ton profil.
                        Plus de confusion après le bac.
                    </p>
                    <div class="flex gap-4">
                        <a href="register.php" class="shadow shadow-blue-300 bg-[#f1b456] text-[#071d3b] px-6 py-3 rounded-lg font-bold hover:bg-[#f1b456]/75 duration-500 hover:translate-y-0.5">
                            Commencer
                        </a>
                        <a href="login.php" class="shadow shadow-blue-300 border border-dashed text-[#071d3b] px-6 py-3 rounded-lg hover:opacity-75 duration-500 hover:translate-y-0.5">
                            Se connecter
                        </a>
                    </div>
                </div>
                <div>
                    <img src="../assets/img/logo.webp" class="w-64 mx-auto hidden md:block">
                </div>
            </div>
        </section>

        <section class="max-w-6xl mx-auto mt-20 px-4">
            <h2 class="text-2xl font-bold text-center text-[#071d3b] mb-10">
            Pourquoi utiliser Mpandova ?
            </h2>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-xl shadow">
                    <h3 class="font-bold mb-2"> Recommandations</h3>
                    <p class="text-gray-600">Suggestions adaptées à ton profil</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow">
                    <h3 class="font-bold mb-2"> Établissements</h3>
                    <p class="text-gray-600">Explore les écoles disponibles</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow">
                    <h3 class="font-bold mb-2"> Filières</h3>
                    <p class="text-gray-600">Découvre les débouchés</p>
                </div>
            </div>
        </section>

        <footer class="bg-[#071d3b]/70 text-white/60 w-auto mt-22 md:mt-46 lg:mt-60">
            <div class="max-w-6xl mx-auto p-6 text-center">
            <p>© 2026 Mpandova - Orientation académique</p>
            </div>
        </footer>
    </body>
</html>