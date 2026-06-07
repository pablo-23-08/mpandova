<?php
// La variable $etudiant est injectée par EtudiantController::render()
// Elle contient : nom, prenom, serie, moyenne, mention, etc.
?>
<main class="flex-1 max-w-6xl mx-auto w-full px-4 py-16">
    <div class="bg-[#071d3b]/50 backdrop-blur-md border border-white/20 rounded-2xl p-8">

        <!-- En-tête : photo + nom + série bac -->
        <div class="flex flex-col items-center">
            <img src="../assets/img/student.webp" alt="étudiant"/>
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
                    <img src="../assets/img/direction.webp" alt=""/> Recommandations
                </h3>
                <p class="text-white/50 text-sm">Filières adaptées à ton profil</p>
            </a>

            <!-- Route vers la page établissements (à implémenter) -->
            <a href="index.php?route=etudiant/etablissements"
               class="bg-[#071d3b]/50 border border-white/10 rounded-xl p-6
                      hover:border-[#f1b456] hover:bg-[#f1b456]/10 duration-300 group">
                <h3 class="font-bold text-white mb-1">
                    <img src="../assets/img/school1.webp" alt=""/> Établissements
                </h3>
                <p class="text-white/50 text-sm">Explorer les écoles disponibles</p>
            </a>

            <!-- Route vers le profil étudiant -->
            <a href="index.php?route=etudiant/profil"
               class="bg-[#071d3b]/50 border border-white/10 rounded-xl p-6
                      hover:border-[#f1b456] hover:bg-[#f1b456]/10 duration-300 group">
                <h3 class="font-bold text-white mb-1">
                    <img src="../assets/img/setting.webp" alt=""/> Mon profil
                </h3>
                <p class="text-white/50 text-sm">Compléter mes informations</p>
            </a>
        </div>

    </div>
</main>