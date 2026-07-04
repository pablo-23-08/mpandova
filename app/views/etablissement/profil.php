<?php
// $etablissement est injectée par EtablissementController::profil()
?>
<main class="mx-auto w-full max-w-6xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">
        <div class="mb-8 flex items-center justify-between gap-4">
            <h1 class="text-2xl font-extrabold text-[#071d3b] sm:text-3xl">Profil établissement</h1>
            <a href="index.php?route=etablissement/accueil" class="text-sm font-semibold text-[#071d3b] hover:underline">Retour</a>
        </div>

        <form method="POST" action="index.php?route=etablissement/profil" novalidate class="space-y-8">
            <div>
                <h2 class="text-lg font-bold text-[#071d3b]">Informations générales</h2>
                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="nom" class="mb-2 block text-sm font-semibold text-[#071d3b]">Nom de l'établissement</label>
                        <input value="<?= htmlspecialchars($etablissement['nom'] ?? '') ?>" type="text" id="nom" name="nom" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
                    </div>

                    <div>
                        <label for="type" class="mb-2 block text-sm font-semibold text-[#071d3b]">Type</label>
                        <select id="type" name="type" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
                            <?php
                            $types = [
                                'universite_publique' => 'Université publique',
                                'universite_privee'   => 'Université privée',
                                'grande_ecole'        => 'Grande école',
                                'institut'            => 'Institut',
                                'autre'               => 'Autre',
                            ];
                            foreach ($types as $val => $label):
                            ?>
                                <option value="<?= $val ?>" <?= ($etablissement['type'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="site_web" class="mb-2 block text-sm font-semibold text-[#071d3b]">Site web</label>
                        <input value="<?= htmlspecialchars($etablissement['site_web'] ?? '') ?>" type="text" id="site_web" name="site_web" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30" placeholder="https://...">
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-lg font-bold text-[#071d3b]">Localisation</h2>
                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="ville" class="mb-2 block text-sm font-semibold text-[#071d3b]">Ville</label>
                        <input value="<?= htmlspecialchars($etablissement['ville'] ?? '') ?>" type="text" id="ville" name="ville" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
                    </div>
                    <div>
                        <label for="adresse" class="mb-2 block text-sm font-semibold text-[#071d3b]">Adresse</label>
                        <input value="<?= htmlspecialchars($etablissement['adresse'] ?? '') ?>" type="text" id="adresse" name="adresse" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
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
