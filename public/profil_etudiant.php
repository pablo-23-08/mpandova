<?php
    require_once "../config/bootstrap.php";
    check_auth();
    check_role("etudiant");

    // Récupérer les infos de l'étudiant
    $stmt = $pdo->prepare("
        SELECT e.*, b.serie, b.moyenne, d.annee
        FROM etudiant e
        LEFT JOIN diplome d ON d.id_etudiant = e.id_etudiant
        LEFT JOIN bac b ON b.id_bac = d.id_bac
        WHERE e.id_user = ?
    ");
    $stmt->execute([$_SESSION['id_user']]);
    $etudiant = $stmt->fetch();

    include '../app/views/layouts/header.php';
?>

<main class="flex-1 max-w-6xl mx-auto w-full px-4 py-16">
    <div class="bg-[#071d3b]/50 backdrop-blur-md border border-white/20 rounded-2xl p-8">

        <div class="flex items-center gap-4 mb-8">
            <a href="accueil_etudiant.php" class="text-white/70 hover:text-[#f1b456] duration-300 text-2xl">&lt;</a>
            <h1 class="text-2xl font-bold text-white">Mon profil</h1>
        </div>

        <form method="POST" action="traitement_profil_etudiant.php" novalidate>
            <?php csrf_field(); ?>

            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <!-- Nom -->
                <div class="relative">
                    <input
                        type="text" id="nom" name="nom" required
                        value="<?= htmlspecialchars($etudiant['nom'] ?? '') ?>"
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                               focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" "
                    >
                    <label for="nom" class="absolute left-4 top-1 text-[#f1b456] text-sm -mt-1">Nom</label>
                </div>

                <!-- Prénom -->
                <div class="relative">
                    <input
                        type="text" id="prenom" name="prenom" required
                        value="<?= htmlspecialchars($etudiant['prenom'] ?? '') ?>"
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                               focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" "
                    >
                    <label for="prenom" class="absolute left-4 top-1 text-[#f1b456] text-sm -mt-1">Prénom</label>
                </div>

                <!-- Date de naissance -->
                <div class="relative">
                    <input
                        type="date" id="date_de_naissance" name="date_de_naissance"
                        value="<?= htmlspecialchars($etudiant['date_de_naissance'] ?? '') ?>"
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                               focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                    >
                    <label for="date_de_naissance" class="absolute left-4 top-1 text-[#f1b456] text-sm -mt-1">Date de naissance</label>
                </div>
            </div>

            <!-- Séparateur -->
            <div class="border-t border-white/10 my-6"></div>
            <h2 class="text-white font-bold mb-4">Baccalauréat</h2>

            <div class="grid md:grid-cols-3 gap-6 mb-8">
                <!-- Série bac -->
                <div class="relative">
                    <label for="serie_bac" class="absolute left-4 top-1 text-[#f1b456] text-sm -mt-1">Série</label>
                    <select id="serie_bac" name="serie_bac" required
                        class="w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                               focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                    >
                        <?php foreach (['A','C','D','L','OSE','S'] as $s): ?>
                            <option value="<?= $s ?>" class="bg-[#071d3b]"
                                <?= ($etudiant['serie'] ?? $etudiant['serie_bac'] ?? '') === $s ? 'selected' : '' ?>>
                                Série <?= $s ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Année d'obtention -->
                <div class="relative">
                    <input
                        type="number" id="annee_bac" name="annee_bac"
                        min="2000" max="<?= date('Y') ?>"
                        value="<?= htmlspecialchars($etudiant['annee'] ?? '') ?>"
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                               focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" "
                    >
                    <label for="annee_bac" class="absolute left-4 top-1 text-[#f1b456] text-sm -mt-1">Année d'obtention</label>
                </div>

                <!-- Moyenne bac -->
                <div class="relative">
                    <input
                        type="number" id="moyenne_bac" name="moyenne_bac"
                        min="0" max="20" step="0.01"
                        value="<?= htmlspecialchars($etudiant['moyenne'] ?? '') ?>"
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                               focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" "
                    >
                    <label for="moyenne_bac" class="absolute left-4 top-1 text-[#f1b456] text-sm -mt-1">Moyenne (sur 20)</label>
                </div>
            </div>

            <!-- Séparateur -->
            <div class="border-t border-white/10 my-6"></div>
            <h2 class="text-white font-bold mb-4">Modifier le mot de passe <span class="text-white/50 font-normal text-sm">(laisser vide pour ne pas changer)</span></h2>

            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div class="relative">
                    <input
                        type="password" id="password" name="password"
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                               focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" "
                    >
                    <label for="password" class="absolute left-4 top-1 text-[#f1b456] text-sm -mt-1">Nouveau mot de passe</label>
                </div>
                <div class="relative">
                    <input
                        type="password" id="password_confirm" name="password_confirm"
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                               focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" "
                    >
                    <label for="password_confirm" class="absolute left-4 top-1 text-[#f1b456] text-sm -mt-1">Confirmer le mot de passe</label>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-[#f1b456] text-[#071d3b] font-bold py-3 rounded-lg
                       hover:bg-[#f1b456]/80 duration-300 hover:translate-y-0.5 transition-transform"
            >
                Enregistrer les modifications
            </button>
        </form>

    </div>
</main>

<?php include '../app/views/layouts/footer.php'; ?>