<?php
// $etudiant est injectée par EtudiantController::profil()
?>
<main class="mx-auto w-full max-w-6xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">
        <div class="mb-8 flex items-center justify-between gap-4">
            <h1 class="text-2xl font-extrabold text-[#071d3b] sm:text-3xl">Mon profil étudiant</h1>
            <a href="index.php?route=etudiant/accueil" class="text-sm font-semibold text-[#071d3b] hover:underline">Retour</a>
        </div>

        <form method="POST" action="index.php?route=etudiant/profil" novalidate class="space-y-8">
            <div>
                <h2 class="text-lg font-bold text-[#071d3b]">Informations personnelles</h2>
                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="nom" class="mb-2 block text-sm font-semibold text-[#071d3b]">Nom</label>
                        <input value="<?= htmlspecialchars($etudiant['nom'] ?? '') ?>" type="text" id="nom" name="nom" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
                    </div>
                    <div>
                        <label for="prenom" class="mb-2 block text-sm font-semibold text-[#071d3b]">Prénom</label>
                        <input value="<?= htmlspecialchars($etudiant['prenom'] ?? '') ?>" type="text" id="prenom" name="prenom" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="date_de_naissance" class="mb-2 block text-sm font-semibold text-[#071d3b]">Date de naissance</label>
                        <input value="<?= htmlspecialchars($etudiant['date_de_naissance'] ?? '') ?>" type="date" id="date_de_naissance" name="date_de_naissance" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-lg font-bold text-[#071d3b]">Baccalauréat</h2>
                <div class="mt-4 grid gap-5 sm:grid-cols-3">
                    <div>
                        <label for="serie_bac" class="mb-2 block text-sm font-semibold text-[#071d3b]">Série</label>
                        <select id="serie_bac" name="serie_bac" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
                            <?php foreach (['A', 'C', 'D', 'L', 'OSE', 'S'] as $s): ?>
                                <option value="<?= $s ?>" <?= ($etudiant['serie'] ?? '') === $s ? 'selected' : '' ?>>Série <?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="annee_bac" class="mb-2 block text-sm font-semibold text-[#071d3b]">Année d'obtention</label>
                        <input min="2000" max="<?= date('Y') ?>" value="<?= htmlspecialchars($etudiant['annee_obtention'] ?? '') ?>" type="number" id="annee_bac" name="annee_bac" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
                    </div>
                    <div>
                        <label for="moyenne_bac" class="mb-2 block text-sm font-semibold text-[#071d3b]">Moyenne (sur 20)</label>
                        <input min="0" max="20" step="0.01" value="<?= htmlspecialchars($etudiant['moyenne'] ?? '') ?>" type="number" id="moyenne_bac" name="moyenne_bac" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-lg font-bold text-[#071d3b]">Modifier le mot de passe</h2>
                <p class="mt-1 text-sm text-slate-500">Laisser vide pour conserver le mot de passe actuel.</p>
                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="password" class="mb-2 block text-sm font-semibold text-[#071d3b]">Nouveau mot de passe</label>
                        <input type="password" id="password" name="password" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
                    </div>
                    <div>
                        <label for="password_confirm" class="mb-2 block text-sm font-semibold text-[#071d3b]">Confirmer le mot de passe</label>
                        <input type="password" id="password_confirm" name="password_confirm" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full rounded-lg bg-[#f1b456] px-5 py-3 text-sm font-bold text-[#071d3b] hover:bg-[#e4a744]">
                Enregistrer les modifications
            </button>
        </form>
    </section>
</main>
