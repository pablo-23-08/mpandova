<?php
require_once "../config/bootstrap.php";
check_auth();
check_role("etablissement");

$stmt = $pdo->prepare("
    SELECT e.*, l.ville, l.adresse
    FROM etablissement e
    LEFT JOIN location l ON l.id_etablissement = e.id_etablissement
    WHERE e.id_user = ?
");
$stmt->execute([$_SESSION['id_user']]);
$etablissement = $stmt->fetch();

include '../app/views/layouts/header.php';
?>

<main class="flex-1 max-w-6xl mx-auto w-full px-4 py-16">
    <div class="bg-[#071d3b]/50 backdrop-blur-md border border-white/20 rounded-2xl p-8">

        <div class="flex items-center gap-4 mb-8">
            <a href="accueil_etablissement.php" class="text-white/70 hover:text-[#f1b456] duration-300 text-2xl">&lt;</a>
            <h1 class="text-2xl font-bold text-white">Mon profil établissement</h1>
        </div>

        <form method="POST" action="traitement_profil_etablissement.php" novalidate>
            <?php csrf_field(); ?>

            <h2 class="text-white font-bold mb-4">Informations générales</h2>
            <div class="grid md:grid-cols-2 gap-6 mb-6">

                <div class="relative">
                    <input type="text" id="nom" name="nom" required
                        value="<?= htmlspecialchars($etablissement['nom'] ?? '') ?>"
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                               focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" ">
                    <label for="nom" class="absolute left-4 top-1 text-[#f1b456] text-sm -mt-1">Nom de l'établissement</label>
                </div>

                <div class="relative">
                    <label for="type" class="absolute left-4 top-1 text-[#f1b456] text-sm -mt-1">Type</label>
                    <select id="type" name="type" required
                        class="w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                               focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]">
                        <?php
                        $types = [
                            'universite'     => 'Université publique',
                            'grande_ecole'   => 'Grande école',
                            'institut_prive' => 'Institut privé',
                            'lycee_technique'=> 'Lycée technique',
                            'autre'          => 'Autre',
                        ];
                        foreach ($types as $val => $label): ?>
                            <option value="<?= $val ?>" class="bg-[#071d3b]"
                                <?= ($etablissement['type'] ?? '') === $val ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="relative md:col-span-2">
                    <input type="url" id="site_web" name="site_web"
                        value="<?= htmlspecialchars($etablissement['site_web'] ?? '') ?>"
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                               focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" ">
                    <label for="site_web" class="absolute left-4 top-1 text-[#f1b456] text-sm -mt-1">Site web (https://…)</label>
                </div>
            </div>

            <div class="border-t border-white/10 my-6"></div>
            <h2 class="text-white font-bold mb-4">Localisation</h2>

            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div class="relative">
                    <input type="text" id="ville" name="ville"
                        value="<?= htmlspecialchars($etablissement['ville'] ?? '') ?>"
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                               focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" ">
                    <label for="ville" class="absolute left-4 top-1 text-[#f1b456] text-sm -mt-1">Ville</label>
                </div>

                <div class="relative">
                    <input type="text" id="adresse" name="adresse"
                        value="<?= htmlspecialchars($etablissement['adresse'] ?? '') ?>"
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                               focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" ">
                    <label for="adresse" class="absolute left-4 top-1 text-[#f1b456] text-sm -mt-1">Adresse</label>
                </div>
            </div>

            <div class="border-t border-white/10 my-6"></div>
            <h2 class="text-white font-bold mb-4">Modifier le mot de passe <span class="text-white/50 font-normal text-sm">(laisser vide pour ne pas changer)</span></h2>

            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div class="relative">
                    <input type="password" id="password" name="password"
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                               focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" ">
                    <label for="password" class="absolute left-4 top-1 text-[#f1b456] text-sm -mt-1">Nouveau mot de passe</label>
                </div>
                <div class="relative">
                    <input type="password" id="password_confirm" name="password_confirm"
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                               focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" ">
                    <label for="password_confirm" class="absolute left-4 top-1 text-[#f1b456] text-sm -mt-1">Confirmer le mot de passe</label>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-[#f1b456] text-[#071d3b] font-bold py-3 rounded-lg
                       hover:bg-[#f1b456]/80 duration-300 hover:translate-y-0.5 transition-transform">
                Enregistrer les modifications
            </button>
        </form>

    </div>
</main>

<?php include '../app/views/layouts/footer.php'; ?>