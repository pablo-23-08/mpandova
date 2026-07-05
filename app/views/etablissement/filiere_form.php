<?php
// $filieres : toutes les filières disponibles (pour le <select>)
// $offre    : null si ajout, tableau si modification (valeurs pré-remplies)
// $mode     : 'ajouter' | 'modifier'

// Construire l'URL d'action selon le mode
$actionUrl = ($mode === 'modifier')
    ? "index.php?route=etablissement/filiere-modifier&id={$offre['id_offre_filiere']}"
    : "index.php?route=etablissement/filiere-ajouter";
?>
<main class="flex-1 max-w-6xl mx-auto w-full px-4 py-16">
    <div class="bg-[#071d3b]/50 backdrop-blur-md border border-white/20 rounded-2xl p-8">

        <div class="flex items-center gap-4 mb-8">
            <a href="index.php?route=etablissement/filieres"
               class="text-white/70 hover:text-[#f1b456] duration-300 text-2xl">&lt;</a>
            <h1 class="text-2xl font-bold text-white">
                <?= $mode === 'modifier' ? 'Modifier la filière' : 'Ajouter une filière' ?>
            </h1>
        </div>

        <?php if (empty($filieres)): ?>
            <!-- Cas où la table filiere est vide -->
            <div class="text-center py-8 text-white/50">
                <p>Aucune filière n'est disponible dans la base de données.</p>
                <p class="text-sm mt-2">Veuillez importer des filières dans la table <code>filiere</code>.</p>
            </div>
        <?php else: ?>

        <form method="POST" action="<?= $actionUrl ?>" novalidate>

            <!-- ── Section 1 : Informations générales ── -->
            <h2 class="text-white font-bold mb-4">Informations générales</h2>
            <div class="grid md:grid-cols-2 gap-6 mb-6">

                <!-- Sélection de la filière -->
                <div class="relative mb-5 md:col-span-2">
                    <select id="id_filiere" name="id_filiere" required
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-6 pb-2
                        focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]">
                        <option value="" disabled <?= !$offre ? 'selected' : '' ?> hidden>
                            Choisir une filière…
                        </option>
                        <?php foreach ($filieres as $f): ?>
                            <option value="<?= $f['id_filiere'] ?>" class="bg-[#071d3b]"
                                <?= ($offre && $offre['id_filiere'] === $f['id_filiere']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($f['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="id_filiere"
                        class="absolute left-4 top-3 text-white/90 text-sm peer-focus:text-[#f1b456] -mt-1">
                        Filière
                    </label>
                </div>

                <!-- Frais de scolarité -->
                <div class="relative mb-5">
                    <input type="number" id="frais_scolarite" name="frais_scolarite"
                        min="0" step="1000"
                        value="<?= htmlspecialchars($offre['frais_scolarite'] ?? '0') ?>"
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                        focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" ">
                    <label for="frais_scolarite"
                        class="absolute left-4 top-3 text-white/90 text-sm transition-all
                        peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70
                        peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1">
                        Frais de scolarité (Ar/an)
                    </label>
                </div>

                <!-- Places disponibles -->
                <div class="relative mb-5">
                    <input type="number" id="place_disponible" name="place_disponible"
                        min="0" step="1"
                        value="<?= htmlspecialchars($offre['place_disponible'] ?? '0') ?>"
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                        focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" ">
                    <label for="place_disponible"
                        class="absolute left-4 top-3 text-white/90 text-sm transition-all
                        peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70
                        peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1">
                        Places disponibles
                    </label>
                </div>

                <!-- Durée de la formation -->
                <div class="relative mb-5 md:col-span-2">
                    <input type="text" id="duree_formation" name="duree_formation"
                        value="<?= htmlspecialchars($offre['duree_formation'] ?? '') ?>"
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                        focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" ">
                    <label for="duree_formation"
                        class="absolute left-4 top-3 text-white/90 text-sm transition-all
                        peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70
                        peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1">
                        Durée de la formation (ex : 3 ans, 5 ans…)
                    </label>
                </div>
            </div>

            <!-- ── Section 2 : Conditions d'accès (toutes optionnelles) ── -->
            <div class="border-t border-white/10 my-6"></div>
            <h2 class="text-white font-bold mb-2">Conditions d'accès
                <span class="text-white/40 font-normal text-sm">(optionnelles — laisser vide = aucune restriction)</span>
            </h2>
            <div class="grid md:grid-cols-3 gap-6 mb-8">

                <!-- Série de bac requise -->
                <div class="relative mb-5">
                    <select id="serie_bac" name="serie_bac"
                        class="peer w-full border border-black/20 text-sm text-white rounded-lg px-4 pt-6 pb-2
                        focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]">
                        <option value="" class="bg-[#071d3b]"
                            <?= !($offre['serie_bac'] ?? null) ? 'selected' : '' ?>>
                            Toutes séries
                        </option>
                        <?php foreach (['A', 'C', 'D', 'L', 'OSE', 'S'] as $s): ?>
                            <option value="<?= $s ?>" class="bg-[#071d3b]"
                                <?= ($offre['serie_bac'] ?? '') === $s ? 'selected' : '' ?>>
                                Série <?= $s ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="serie_bac"
                        class="absolute left-4 top-3 text-white/90 text-sm peer-focus:text-[#f1b456] -mt-1">
                        Série bac requise
                    </label>
                </div>

                <!-- Moyenne minimale requise -->
                <div class="relative mb-5">
                    <input type="number" id="moyenne_bac" name="moyenne_bac"
                        min="0" max="20" step="0.01"
                        value="<?= htmlspecialchars($offre['moyenne_bac'] ?? '') ?>"
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                        focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" ">
                    <label for="moyenne_bac"
                        class="absolute left-4 top-3 text-white/90 text-sm transition-all
                        peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70
                        peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1">
                        Moyenne minimale (/20)
                    </label>
                </div>

                <!-- Âge maximum -->
                <div class="relative mb-5">
                    <input type="number" id="age_max" name="age_max"
                        min="0" max="99" step="1"
                        value="<?= htmlspecialchars($offre['age_max'] ?? '') ?>"
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                        focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" ">
                    <label for="age_max"
                        class="absolute left-4 top-3 text-white/90 text-sm transition-all
                        peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70
                        peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1">
                        Âge maximum
                    </label>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-[#f1b456] text-[#071d3b] font-bold py-3 rounded-lg
                hover:bg-[#f1b456]/80 duration-500 hover:translate-y-0.5 transition-transform">
                <?= $mode === 'modifier' ? 'Enregistrer les modifications' : 'Ajouter la filière' ?>
            </button>
        </form>
        <?php endif; ?>

    </div>
</main>