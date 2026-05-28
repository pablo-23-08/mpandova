<?php
    require_once "../config/bootstrap.php";
    check_auth();
    check_role("etudiant");

    //Recuperer les infos de l'etudiant
    $stmt=$pdo->prepare("
        SELECT e.*, d.annee_obtention, b.serie, b.moyenne, b.mention
        FROM etudiant e
        LEFT JOIN diplome d ON d.id_etudiant=e.id_etudiant
        LEFT JOIN bac b ON b.id_diplome=d.id_diplome
        WHERE e.id_utilisateur=?
    ");
    $stmt->execute([$_SESSION['id_utilisateur']]);
    $etudiant=$stmt->fetch();

    include '../app/views/layouts/header.php';
?>

<main class="flex-1 max-w-6xl mx-auto w-full px-4 py-16">
    <div class="bg-[#071d3b]/50 backdrop-blur-md border border-white/20 rounded-2xl p-8">

        <div class="flex items-center gap-4 mb-8">
            <a href="accueil_etudiant.php" class="text-white/70 hover:text-[#f1b456] duration-300 text-2xl">&lt;</a>
            <h1 class="text-2xl font-bold text-white">Mon profil</h1>
        </div>

        <form method="POST" action="traitement_profil_etudiant.php" novalidate>

            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <!-- Nom -->
                <div class="relative mb-5">
                    <input
                        value="<?= htmlspecialchars($etudiant['nom'] ?? '') ?>"
                        type="text"
                        id="nom"
                        name="nom"
                        required
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 
                        pb-2 focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" "
                    >
                    <label
                        for="nom"
                        class="absolute left-4 top-3 text-white/90 text-sm transition-all 
                        peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70
                        peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1"
                    >
                        Nom
                    </label>
                </div>

                <!-- Prenom -->
                <div class="relative mb-5">
                    <input
                        value="<?= htmlspecialchars($etudiant['prenom'] ?? '') ?>"
                        type="text"
                        id="prenom"
                        name="prenom"
                        required
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 
                        pb-2 focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" "
                    >
                    <label
                        for="prenom"
                        class="absolute left-4 top-3 text-white/90 text-sm transition-all 
                        peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70
                        peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1"
                    >
                        Prénom
                    </label>
                </div>

                <!-- Date de naissance -->
                <div class="relative mb-5">
                    <input
                        value="<?= htmlspecialchars($etudiant['date_de_naissance'] ?? '') ?>"
                        type="date"
                        id="date_de_naissance"
                        name="date_de_naissance"
                        required
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 
                        pb-2 focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" "
                    >
                    <label
                        for="date_de_naissance"
                        class="absolute left-4 top-3 text-white/90 text-sm transition-all 
                        peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70
                        peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1"
                    >
                        Date de naissance
                    </label>
                </div>
            </div>

            <!-- Séparateur -->
            <div class="border-t border-white/10 my-6"></div>
            <h2 class="text-white font-bold mb-4">Baccalauréat</h2>

            <div class="grid md:grid-cols-3 gap-6 mb-8">
                <!-- Serie bac -->
                <div class="relative mb-5">
                    <select
                        id="serie_bac" name="serie_bac" required
                        class="peer w-full border border-black/20 text-sm text-white rounded-lg px-4 pt-6 pb-2
                            focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                    >
                        <?php foreach (['A','C','D','L','OSE','S'] as $s): ?>
                            <option value="<?= $s ?>" class="bg-[#071d3b]"
                                <?= ($etudiant['serie'] ?? $etudiant['serie_bac'] ?? '') === $s ? 'selected' : '' ?>>
                                Série <?= $s ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="serie_bac" class="absolute left-4 top-3 text-white/90 text-sm transition-all 
                        peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1"
                    >
                        Série
                    </label>
                </div>

                <!-- Annee d'obtention -->
                <div class="relative mb-5">
                    <input
                        min="2000" max="<?= date('Y') ?>"
                        value="<?= htmlspecialchars($etudiant['annee_obtention'] ?? '') ?>"
                        type="number"
                        id="annee_bac"
                        name="annee_bac"
                        required
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 
                        pb-2 focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" "
                    >
                    <label
                        for="annee_bac"
                        class="absolute left-4 top-3 text-white/90 text-sm transition-all 
                        peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70
                        peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1"
                    >
                        Année d'obtention
                    </label>
                </div>

                <!-- Moyenne bac -->
                <div class="relative mb-5">
                    <input
                        min="0" max="20" step="0.01"
                        value="<?= htmlspecialchars($etudiant['moyenne'] ?? '') ?>"
                        type="number"
                        id="moyenne_bac"
                        name="moyenne_bac"
                        required
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 
                        pb-2 focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" "
                    >
                    <label
                        for="moyenne_bac"
                        class="absolute left-4 top-3 text-white/90 text-sm transition-all 
                        peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70
                        peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1"
                    >
                        Moyenne (sur 20)
                    </label>
                </div>
            </div>

            <!-- Separateur -->
            <div class="border-t border-white/10 my-6"></div>
            <h2 class="text-white font-bold mb-4">Modifier le mot de passe <span class="text-white/50 font-normal text-sm">(laisser vide pour ne pas changer)</span></h2>

            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div class="relative mb-5">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 
                        pb-2 focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" "
                    >
                    <label
                        for="password"
                        class="absolute left-4 top-3 text-white/90 text-sm transition-all 
                        peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70
                        peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1"
                    >
                        Nouveau mot de passe
                    </label>
                </div>
                <div class="relative mb-5">
                    <input
                        type="password"
                        id="password_confirm"
                        name="password_confirm"
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 
                        pb-2 focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" "
                    >
                    <label
                        for="password_confirm"
                        class="absolute left-4 top-3 text-white/90 text-sm transition-all 
                        peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70
                        peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1"
                    >
                        Confirmer le mot de passe
                    </label>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-[#f1b456] text-[#071d3b] font-bold py-3 rounded-lg
                       hover:bg-[#f1b456]/80 duration-500 hover:translate-y-0.5 transition-transform"
            >
                Enregistrer les modifications
            </button>
        </form>

    </div>
</main>

<?php include "../app/views/layouts/footer.php"; ?>