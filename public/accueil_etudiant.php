<?php
require_once "../config/bootstrap.php";
check_auth();
check_role("etudiant");

// Récupérer les infos de l'étudiant
$stmt = $pdo->prepare("SELECT * FROM etudiant WHERE id_user = ?");
$stmt->execute([$_SESSION['id_user']]);
$etudiant = $stmt->fetch();

include '../app/views/layouts/header.php';
?>

<main class="flex-1 max-w-6xl mx-auto w-full px-4 py-16">
    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-8">
        <h1 class="text-3xl font-bold text-white mb-2">
            Bienvenue, <?= htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']) ?> 👋
        </h1>
        <p class="text-white/50 mb-8">Série bac : <span class="text-[#f1b456] font-bold"><?= htmlspecialchars($etudiant['serie_bac']) ?></span></p>

        <div class="grid md:grid-cols-3 gap-4">
            <div class="bg-white/10 border border-white/10 rounded-xl p-6">
                <h3 class="font-bold text-white mb-1">🎯 Recommandations</h3>
                <p class="text-white/50 text-sm">Filières adaptées à ton profil</p>
            </div>
            <div class="bg-white/10 border border-white/10 rounded-xl p-6">
                <h3 class="font-bold text-white mb-1">🏫 Établissements</h3>
                <p class="text-white/50 text-sm">Explorer les écoles disponibles</p>
            </div>
            <div class="bg-white/10 border border-white/10 rounded-xl p-6">
                <h3 class="font-bold text-white mb-1">📋 Mon profil</h3>
                <p class="text-white/50 text-sm">Compléter mes informations</p>
            </div>
        </div>

        <div class="mt-8">
            <a href="logout.php" class="text-red-400 hover:text-red-300 text-sm duration-300">
                Se déconnecter
            </a>
        </div>
    </div>
</main>

<?php include '../app/views/layouts/footer.php'; ?>