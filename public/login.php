<?php
require_once "../config/bootstrap.php";
redirect_if_logged();
include '../app/views/layouts/header.php';
?>

<main class="flex-1 flex items-center justify-center px-4 py-16">
    <div class="bg-[#071d3b]/50 backdrop-blur-md border border-black/20 rounded-2xl shadow-2xl p-8 w-full max-w-md">

        <a href="index.php" class="text-4xl text-white/70 hover:text-[#f1b456]">&lt; </a>
        <h2 class="text-xl font-bold text-white text-center -mt-8 mb-10 ">Se connecter à Mpandova</h2>

        <form action="traitement_login.php" method="POST" novalidate>
            <?php csrf_field(); ?>

            <div class="relative mb-5">
                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                    class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 
                    pb-2 focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                    placeholder=" "
                >
                <label
                    for="email"
                    class="absolute left-4 top-3 text-white/90 text-sm transition-all 
                    peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70
                    peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1"
                >
                    E-mail
                </label>
            </div>

            <div class="relative mb-8">
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 
                    pb-2 focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                >
                <label
                    for="password"
                    class="absolute left-4 top-3 text-white/90 text-sm transition-all 
                    peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70
                    peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1"
                >
                    Mot de passe
                </label>
            </div>

            <button
                type="submit"
                class="w-full bg-[#f1b456] text-[#071d3b] font-bold py-3 rounded-lg 
                hover:bg-[#f1b456]/80 duration-300 hover:translate-y-0.5 transition-transform"
            >
                Se connecter
            </button>
        </form>

        <p class="text-center text-white/50 text-sm mt-6">
            Pas encore de compte ?
            <a href="register.php" class="text-[#f1b456] hover:underline font-medium">Créer un compte</a>
        </p>

    </div>
</main>

<?php include '../app/views/layouts/footer.php'; ?>