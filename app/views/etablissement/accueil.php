<?php
// $etablissement est injectée par EtablissementController::accueil()
?>
<<<<<<< HEAD
<main class="mx-auto w-full max-w-6xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">
        <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <img src="assets/img/school.webp" alt="Établissement" class="h-14 w-14 rounded-xl bg-slate-100 p-2 object-contain">
                <div>
                    <h1 class="text-2xl font-extrabold text-[#071d3b] sm:text-3xl"><?= htmlspecialchars($etablissement['nom']) ?></h1>
                    <p class="mt-1 text-sm text-slate-600">Type : <span class="font-bold text-[#071d3b]"><?= htmlspecialchars($etablissement['type']) ?></span></p>
                </div>
            </div>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-3">
            <a href="index.php?route=etablissement/filieres" class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-[#f1b456] hover:bg-[#f1b456]/10">
                <img src="assets/img/filiere.webp" alt="Filières" class="h-10 w-10 object-contain">
                <h2 class="mt-4 text-base font-bold text-[#071d3b]">Mes filières</h2>
                <p class="mt-2 text-sm text-slate-600">Gérer vos formations.</p>
            </a>

            <a href="index.php?route=etablissement/candidatures" class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-[#f1b456] hover:bg-[#f1b456]/10">
                <img src="assets/img/candidature.webp" alt="Candidatures" class="h-10 w-10 object-contain">
                <h2 class="mt-4 text-base font-bold text-[#071d3b]">Candidatures</h2>
                <p class="mt-2 text-sm text-slate-600">Consulter les demandes reçues.</p>
            </a>

            <a href="index.php?route=etablissement/profil" class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-[#f1b456] hover:bg-[#f1b456]/10">
                <img src="assets/img/setting.webp" alt="Profil" class="h-10 w-10 object-contain">
                <h2 class="mt-4 text-base font-bold text-[#071d3b]">Profil</h2>
                <p class="mt-2 text-sm text-slate-600">Modifier les informations de l’établissement.</p>
            </a>
        </div>
    </section>
</main>
=======
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
>>>>>>> 680f67e9609fecabd25b9ef923ff6d432c465405
