<?php
require_once "../config/bootstrap.php";
redirect_if_logged();
include '../app/views/layouts/header.php';
?>

<main class="flex-1 flex items-center justify-center px-4 py-16">
    <div class="bg-[#071d3b]/50 backdrop-blur-md border border-black/20 rounded-2xl shadow-2xl p-8 w-full max-w-md text-center">

        <a href="index.php" class="text-4xl text-white/70 hover:text-[#f1b456] mr-90">&lt; </a>

        <h2 class="text-xl font-bold text-white text-center -mt-8 mb-10 ">Créer un compte</h2>
        <p class="text-white/70 text-sm mb-10">Quel type de compte souhaitez-vous créer ?</p>

        <div class="grid grid-cols-2 gap-4">
            <a
                href="register_etudiant.php"
                class="flex flex-col items-center gap-3 bg-black/10 border border-black/20 rounded-xl p-6 hover:border-[#f1b456] hover:bg-[#f1b456]/10 duration-300 group"
            >
                <span class="text-4xl"><img src="../assets/img/student.webp"/></span>
                <span class="text-white font-bold group-hover:text-[#f1b456] duration-300">Étudiant</span>
                <span class="text-white/70 text-xs">Tu cherches une filière</span>
            </a>
            <a
                href="register_etablissement.php"
                class="flex flex-col items-center gap-3 bg-black/10 border border-black/20 rounded-xl p-6 hover:border-[#f1b456] hover:bg-[#f1b456]/10 duration-300 group"
            >
                <span class="text-4xl"><img src="../assets/img/school.webp"/></span>
                <span class="text-white font-bold group-hover:text-[#f1b456] duration-300">Établissement</span>
                <span class="text-white/70 text-xs">Tu proposes des filières</span>
            </a>
        </div>

        <p class="text-center text-white/70 text-sm mt-8">
            Déjà un compte ?
            <a href="login.php" class="text-[#f1b456] hover:underline font-medium">Se connecter</a>
        </p>

    </div>
</main>

<?php include '../app/views/layouts/footer.php'; ?>