<?php
// $offres    : toutes les offres (filtrées ou non)
// $recherche : terme de recherche actuel (pour pré-remplir le champ)
?>
<main class="mx-auto w-full max-w-6xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">
        <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-[#071d3b] sm:text-3xl">Catalogue des filières</h1>
                <p class="mt-1 text-sm text-slate-500">Listes des filières disponibles.</p>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-2 sm:gap-3">
                <a href="index.php?route=etudiant/accueil" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-[#071d3b] hover:border-[#f1b456]">
                    Retour
                </a>
            </div>
        </div>

        <form method="GET" action="index.php" class="mt-6">
            <input type="hidden" name="route" value="etudiant/etablissements">
            <div class="flex flex-col gap-3 sm:flex-row">
                <input
                    type="text"
                    name="q"
                    value="<?= htmlspecialchars($recherche) ?>"
                    placeholder="Rechercher une filière, un établissement, une ville"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm text-slate-700 outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30"
                >
                <button type="submit" class="rounded-lg bg-[#f1b456] px-6 py-3 text-sm font-bold text-[#071d3b] hover:bg-[#e4a744]">
                    Rechercher
                </button>
                <?php if (!empty($recherche)): ?>
                    <a href="index.php?route=etudiant/etablissements" class="rounded-lg border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-600 hover:border-[#f1b456] hover:text-[#071d3b]">
                        Effacer
                    </a>
                <?php endif; ?>
            </div>
        </form>

        <p class="mt-5 text-sm text-slate-600">
            <?= count($offres) ?> offre(s) trouvée(s)
            <?= !empty($recherche) ? 'pour « ' . htmlspecialchars($recherche) . ' »' : '' ?>
        </p>

        <?php if (empty($offres)): ?>
            <div class="py-16 text-center text-slate-600">
                <p>Aucune offre ne correspond à votre recherche.</p>
            </div>
        <?php else: ?>
            <div class="mt-6 grid gap-4 md:grid-cols-2">
                <?php foreach ($offres as $offre): ?>
                    <article class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#f1b456]/60">
                        <div>
                            <h3 class="text-lg font-bold text-[#071d3b]">
                                <?= htmlspecialchars($offre['filiere_nom']) ?>
                            </h3>
                            <p class="text-sm font-medium text-slate-600">
                                <?= htmlspecialchars($offre['etablissement_nom']) ?>
                            </p>
                            <?php if ($offre['ville']): ?>
                                <p class="mt-1 text-xs text-slate-500">
                                    <?= htmlspecialchars($offre['ville']) ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <div class="flex flex-wrap gap-2 text-xs text-slate-600">
                            <span class="rounded-full bg-slate-100 px-3 py-1">Frais : <?= number_format($offre['frais_scolarite'], 0, ',', ' ') ?> Ar/an</span>
                            <span class="rounded-full bg-slate-100 px-3 py-1">Places : <?= $offre['place_disponible'] ?></span>
                            <?php if ($offre['duree_formation']): ?>
                                <span class="rounded-full bg-slate-100 px-3 py-1">Durée : <?= htmlspecialchars($offre['duree_formation']) ?></span>
                            <?php endif; ?>
                        </div>

                        <?php if ($offre['serie_bac'] || $offre['moyenne_bac']): ?>
                            <div class="flex flex-wrap gap-2 text-xs">
                                <?php if ($offre['serie_bac']): ?>
                                    <span class="rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-sky-800">
                                        Série <?= htmlspecialchars($offre['serie_bac']) ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ($offre['moyenne_bac']): ?>
                                    <span class="rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-amber-800">
                                        Moyenne min. <?= htmlspecialchars($offre['moyenne_bac']) ?>/20
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <span class="text-xs font-semibold text-emerald-700">Ouvert à tous</span>
                        <?php endif; ?>

                        <form method="POST" action="index.php?route=etudiant/candidature-soumettre" class="mt-auto">
                            <input type="hidden" name="id_offre_filiere" value="<?= $offre['id_offre_filiere'] ?>">
                            <button type="submit" class="w-full rounded-lg bg-[#f1b456] py-2 text-sm font-bold text-[#071d3b] hover:bg-[#e4a744]">
                                Postuler
                            </button>
                        </form>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>
