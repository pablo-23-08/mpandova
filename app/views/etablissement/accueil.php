<?php
// $etablissement est injectée par EtablissementController::accueil()
?>
<main class="flex-1 max-w-6xl mx-auto w-full px-4 py-16">
    <div class="bg-[#071d3b]/50 backdrop-blur-md border border-white/20 rounded-2xl p-8">

        <div class="flex flex-col items-center">
            <img src="../assets/img/school.webp" alt="école"/>
            <h1 class="text-3xl font-bold text-white mb-2">
                <?= htmlspecialchars($etablissement['nom']) ?>
            </h1>
            <p class="text-white/50 mb-8">
                Type :
                <span class="text-[#f1b456] font-bold">
                    <?= htmlspecialchars($etablissement['type']) ?>
                </span>
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-4">
            <a href="index.php?route=etablissement/filieres"
               class="bg-[#071d3b]/50 border border-white/10 rounded-xl p-6
                      hover:border-[#f1b456] hover:bg-[#f1b456]/10 duration-300 group">
                <h3 class="font-bold text-white mb-1">
                    <img src="../assets/img/filiere.webp" alt=""/> Mes filières
                </h3>
                <p class="text-white/50 text-sm">Gérer vos formations</p>
            </a>
            <a href="index.php?route=etablissement/candidatures"
               class="bg-[#071d3b]/50 border border-white/10 rounded-xl p-6
                      hover:border-[#f1b456] hover:bg-[#f1b456]/10 duration-300 group">
                <h3 class="font-bold text-white mb-1">
                    <img src="../assets/img/candidature.webp" alt=""/> Candidatures
                </h3>
                <p class="text-white/50 text-sm">Consulter les demandes</p>
            </a>
            <a href="index.php?route=etablissement/profil"
               class="bg-[#071d3b]/50 border border-white/10 rounded-xl p-6
                      hover:border-[#f1b456] hover:bg-[#f1b456]/10 duration-300 group">
                <h3 class="font-bold text-white mb-1">
                    <img src="../assets/img/setting.webp" alt=""/> Profil
                </h3>
                <p class="text-white/50 text-sm">Modifier les informations</p>
            </a>
        </div>
    </div>
</main>