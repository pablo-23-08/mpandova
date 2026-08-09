<?php
// $offre : données de la formation à modifier
?>
<main class="mx-auto w-full max-w-2xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">

        <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-[#071d3b] sm:text-3xl">
                    Modifier la formation
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    <?= htmlspecialchars($offre['filiere_nom']) ?> — <?= htmlspecialchars($offre['etablissement_nom']) ?>
                </p>
            </div>
            <a href="index.php?route=admin/formation/view&id=<?= $offre['id_offre_filiere'] ?>"
               class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-[#071d3b] hover:border-[#f1b456]">
                Retour
            </a>
        </div>

        <div class="mt-6 rounded-2xl border border-sky-200 bg-sky-50 p-4 text-xs text-sky-800">
            <strong>ℹ️ Note :</strong> Les informations de base (filière, établissement) ne peuvent pas être modifiées ici.
            Pour modifier les conditions d'accès détaillées, veuillez contacter l'établissement directement.
        </div>

        <form method="POST" action="index.php?route=admin/formation/edit" novalidate class="mt-6 space-y-5">
            <input type="hidden" name="id_offre_filiere" value="<?= $offre['id_offre_filiere'] ?>">

            <!-- Frais de scolarité -->
            <div>
                <label for="frais_scolarite" class="mb-2 block text-sm font-semibold text-[#071d3b]">
                    Frais de scolarité (Ar/an)
                </label>
                <input
                    type="number"
                    id="frais_scolarite"
                    name="frais_scolarite"
                    min="0"
                    step="0.01"
                    value="<?= $offre['frais_scolarite'] ?? 0 ?>"
                    placeholder="0"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30"
                >
            </div>

            <!-- Places disponibles -->
            <div>
                <label for="place_disponible" class="mb-2 block text-sm font-semibold text-[#071d3b]">
                    Places disponibles
                </label>
                <input
                    type="number"
                    id="place_disponible"
                    name="place_disponible"
                    min="0"
                    value="<?= $offre['place_disponible'] ?? 0 ?>"
                    placeholder="0"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30"
                >
            </div>

            <!-- Durée de la formation -->
            <div>
                <label for="duree_formation" class="mb-2 block text-sm font-semibold text-[#071d3b]">
                    Durée de la formation
                </label>
                <input
                    type="text"
                    id="duree_formation"
                    name="duree_formation"
                    maxlength="100"
                    value="<?= htmlspecialchars($offre['duree_formation'] ?? '') ?>"
                    placeholder="Ex : 3 ans, Licence 3 ans, Master 2 ans…"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30"
                >
            </div>

            <div class="flex flex-wrap gap-3 pt-2">
                <button
                    type="submit"
                    class="rounded-lg bg-[#f1b456] px-6 py-3 text-sm font-bold text-[#071d3b] hover:bg-[#e4a744]"
                >
                    Enregistrer les modifications
                </button>
                <a href="index.php?route=admin/formation/view&id=<?= $offre['id_offre_filiere'] ?>"
                   class="rounded-lg border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 hover:border-[#f1b456]">
                    Annuler
                </a>
            </div>
        </form>

    </section>
</main>
