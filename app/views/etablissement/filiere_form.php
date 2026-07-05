<?php
// $filieres : toutes les filières disponibles (pour le <select>)
// $offre    : null si ajout, tableau si modification (valeurs pré-remplies)
// $mode     : 'ajouter' | 'modifier'

$actionUrl = ($mode === 'modifier')
    ? "index.php?route=etablissement/filiere-modifier&id={$offre['id_offre_filiere']}"
    : "index.php?route=etablissement/filiere-ajouter";
?>
<main class="mx-auto w-full max-w-6xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">
        <div class="mb-8 flex items-center gap-4 border-b border-slate-200 pb-6">
            <a href="index.php?route=etablissement/filieres" class="text-sm font-semibold text-[#071d3b] hover:underline">Retour</a>
            <h1 class="text-2xl font-extrabold text-[#071d3b] sm:text-3xl">
                <?= $mode === 'modifier' ? 'Modifier la filière' : 'Ajouter une filière' ?>
            </h1>
        </div>

        <?php if (empty($filieres)): ?>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-6 text-slate-600">
                <p>Aucune filière n'est disponible dans la base de données.</p>
                <p class="mt-2 text-sm">Veuillez importer des filières dans la table <code>filiere</code>.</p>
            </div>
        <?php else: ?>
            <form method="POST" action="<?= $actionUrl ?>" novalidate class="space-y-8">
                <div>
                    <h2 class="text-lg font-bold text-[#071d3b]">Informations générales</h2>
                    <div class="mt-4 grid gap-5 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label for="id_filiere" class="mb-2 block text-sm font-semibold text-[#071d3b]">Filière</label>
                            <select id="id_filiere" name="id_filiere" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
                                <option value="" disabled <?= !$offre ? 'selected' : '' ?>>Choisir une filière</option>
                                <?php foreach ($filieres as $f): ?>
                                    <option value="<?= $f['id_filiere'] ?>" <?= ($offre && $offre['id_filiere'] === $f['id_filiere']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($f['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="frais_scolarite" class="mb-2 block text-sm font-semibold text-[#071d3b]">Frais de scolarité (Ar/an)</label>
                            <input type="number" id="frais_scolarite" name="frais_scolarite" min="0" step="1000" value="<?= htmlspecialchars($offre['frais_scolarite'] ?? '0') ?>" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
                        </div>

                        <div>
                            <label for="place_disponible" class="mb-2 block text-sm font-semibold text-[#071d3b]">Places disponibles</label>
                            <input type="number" id="place_disponible" name="place_disponible" min="0" step="1" value="<?= htmlspecialchars($offre['place_disponible'] ?? '0') ?>" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
                        </div>

                        <div class="md:col-span-2">
                            <label for="duree_formation" class="mb-2 block text-sm font-semibold text-[#071d3b]">Durée de la formation</label>
                            <input type="text" id="duree_formation" name="duree_formation" value="<?= htmlspecialchars($offre['duree_formation'] ?? '') ?>" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30" placeholder="Ex : 3 ans, 5 ans">
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-lg font-bold text-[#071d3b]">Conditions d'accès</h2>
                    <p class="mt-1 text-sm text-slate-500">Optionnelles — laisser vide pour ne pas imposer de restriction.</p>
                    <div class="mt-4 grid gap-5 md:grid-cols-3">
                        <div>
                            <label for="serie_bac" class="mb-2 block text-sm font-semibold text-[#071d3b]">Série bac requise</label>
                            <select id="serie_bac" name="serie_bac" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
                                <option value="" <?= !($offre['serie_bac'] ?? null) ? 'selected' : '' ?>>Toutes séries</option>
                                <?php foreach (['A', 'C', 'D', 'L', 'OSE', 'S'] as $s): ?>
                                    <option value="<?= $s ?>" <?= ($offre['serie_bac'] ?? '') === $s ? 'selected' : '' ?>>Série <?= $s ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="moyenne_bac" class="mb-2 block text-sm font-semibold text-[#071d3b]">Moyenne minimale (/20)</label>
                            <input type="number" id="moyenne_bac" name="moyenne_bac" min="0" max="20" step="0.01" value="<?= htmlspecialchars($offre['moyenne_bac'] ?? '') ?>" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
                        </div>

                        <div>
                            <label for="age_max" class="mb-2 block text-sm font-semibold text-[#071d3b]">Âge maximum</label>
                            <input type="number" id="age_max" name="age_max" min="0" max="99" step="1" value="<?= htmlspecialchars($offre['age_max'] ?? '') ?>" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full rounded-lg bg-[#f1b456] px-5 py-3 text-sm font-bold text-[#071d3b] hover:bg-[#e4a744]">
                    <?= $mode === 'modifier' ? 'Enregistrer les modifications' : 'Ajouter la filière' ?>
                </button>
            </form>
        <?php endif; ?>
    </section>
</main>
