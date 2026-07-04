<?php
// La variable $etudiant est injectée par EtudiantController::render()
<<<<<<< HEAD
?>
<main class="mx-auto w-full max-w-6xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">
        <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <img src="assets/img/student.webp" alt="Photo étudiant" class="h-14 w-14 rounded-xl bg-slate-100 p-2 object-contain">
                <div>
                    <h1 class="text-2xl font-extrabold text-[#071d3b] sm:text-3xl">
                        Bienvenue, <?= htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']) ?>
                    </h1>
                    <p class="mt-1 text-sm text-slate-600">
                        Série bac : <span class="font-bold text-[#071d3b]"><?= htmlspecialchars($etudiant['serie'] ?? 'Non renseignée') ?></span>
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-3">
            <a href="index.php?route=etudiant/recommandations" class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-[#f1b456] hover:bg-[#f1b456]/10">
                <img src="assets/img/direction.webp" alt="Recommandations" class="h-10 w-10 object-contain">
                <h2 class="mt-4 text-base font-bold text-[#071d3b]">Recommandations</h2>
                <p class="mt-2 text-sm text-slate-600">Filières adaptées à ton profil.</p>
            </a>

            <a href="index.php?route=etudiant/etablissements" class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-[#f1b456] hover:bg-[#f1b456]/10">
                <img src="assets/img/school1.webp" alt="Établissements" class="h-10 w-10 object-contain">
                <h2 class="mt-4 text-base font-bold text-[#071d3b]">Établissements</h2>
                <p class="mt-2 text-sm text-slate-600">Explorer les écoles disponibles.</p>
            </a>

            <a href="index.php?route=etudiant/profil" class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-[#f1b456] hover:bg-[#f1b456]/10">
                <img src="assets/img/setting.webp" alt="Mon profil" class="h-10 w-10 object-contain">
                <h2 class="mt-4 text-base font-bold text-[#071d3b]">Mon profil</h2>
                <p class="mt-2 text-sm text-slate-600">Compléter et mettre à jour mes informations.</p>
            </a>
        </div>
    </section>
</main>
=======
// Elle contient : nom, prenom, serie, moyenne, mention, etc.
?>
<main class="flex-1 max-w-6xl mx-auto w-full px-4 py-16">
    <div class="bg-[#071d3b]/50 backdrop-blur-md border border-white/20 rounded-2xl p-8">

        <!-- En-tête : photo + nom + série bac -->
        <div class="flex flex-col items-center">
            <img src="../public/assets/img/student.webp" alt="étudiant"/>
            <h1 class="text-3xl font-bold text-white mb-2">
                <!-- htmlspecialchars() protège contre les injections XSS -->
                Bienvenue, <?= htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']) ?>
            </h1>
            <p class="text-white/50 mb-8">
                Série bac :
                <span class="text-[#f1b456] font-bold">
                    <?= htmlspecialchars($etudiant['serie'] ?? 'Non renseignée') ?>
                </span>
            </p>
        </div>

        <!-- Grille des 3 accès rapides -->
        <div class="grid md:grid-cols-3 gap-4">
            <!-- Route vers la page recommandations (à implémenter) -->
            <a href="index.php?route=etudiant/recommandations"
               class="bg-[#071d3b]/50 border border-white/10 rounded-xl p-6
                      hover:border-[#f1b456] hover:bg-[#f1b456]/10 duration-300 group">
                <h3 class="font-bold text-white mb-1">
                    <img src="../public/assets/img/direction.webp" alt=""/> Recommandations
                </h3>
                <p class="text-white/50 text-sm">Filières adaptées à ton profil</p>
            </a>

            <!-- Route vers la page établissements (à implémenter) -->
            <a href="index.php?route=etudiant/etablissements"
               class="bg-[#071d3b]/50 border border-white/10 rounded-xl p-6
                      hover:border-[#f1b456] hover:bg-[#f1b456]/10 duration-300 group">
                <h3 class="font-bold text-white mb-1">
                    <img src="../public/assets/img/school1.webp" alt=""/> Établissements
                </h3>
                <p class="text-white/50 text-sm">Explorer les écoles disponibles</p>
            </a>

            <!-- Route vers le profil étudiant -->
            <a href="index.php?route=etudiant/profil"
               class="bg-[#071d3b]/50 border border-white/10 rounded-xl p-6
                      hover:border-[#f1b456] hover:bg-[#f1b456]/10 duration-300 group">
                <h3 class="font-bold text-white mb-1">
                    <img src="../public/assets/img/setting.webp" alt=""/> Mon profil
                </h3>
                <p class="text-white/50 text-sm">Compléter mes informations</p>
            </a>
        </div>

    </div>
</main>
>>>>>>> 680f67e9609fecabd25b9ef923ff6d432c465405
