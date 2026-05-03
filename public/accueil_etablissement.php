<?php
require_once "../config/bootstrap.php";
check_auth();
check_role("etablissement");

// Récupérer les infos de l'établissement
$stmt = $pdo->prepare("SELECT * FROM etablissement WHERE id_user = ?");
$stmt->execute([$_SESSION['id_user']]);
$etablissement = $stmt->fetch();

include '../app/views/layouts/header.php';
?>

<main class="flex-1 max-w-6xl mx-auto w-full px-4 py-16">
    <div class="bg-[#071d3b]/50 backdrop-blur-md border border-white/20 rounded-2xl p-8">
        <div class="flex flex-col items-center">
            <img src="../assets/img/school.webp"/>
            <h1 class="text-3xl font-bold text-white mb-2">
                <?= htmlspecialchars($etablissement['nom']) ?> 
            </h1>
            <p class="text-white/50 mb-8">
                Type : <span class="text-[#f1b456] font-bold"><?= htmlspecialchars($etablissement['type']) ?></span>
            </p>
        </div>
        <div class="grid md:grid-cols-3 gap-4">
            <div class="bg-[#071d3b]/50 border border-white/10 rounded-xl p-6">
                <h3 class="font-bold text-white mb-1"><img src="../assets/img/filiere.webp"/> Mes filières</h3>
                <p class="text-white/50 text-sm">Gérer vos formations</p>
            </div>
            <div class="bg-[#071d3b]/50 border border-white/10 rounded-xl p-6">
                <h3 class="font-bold text-white mb-1"><img src="../assets/img/candidature.webp"/> Candidatures</h3>
                <p class="text-white/50 text-sm">Consulter les demandes</p>
            </div>
            <div class="bg-[#071d3b]/50 border border-white/10 rounded-xl p-6">
                <h3 class="font-bold text-white mb-1"><img src="../assets/img/setting.webp"/> Mon profil</h3>
                <p class="text-white/50 text-sm">Modifier les informations</p>
            </div>
        </div>

    </div>
</main>

<?php include '../app/views/layouts/footer.php'; ?>