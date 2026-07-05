<?php
// $etablissement injectée par FiliereController::index()
// $offres : tableau de toutes les offres avec leurs conditions d'accès
?>
<main class="mx-auto w-full max-w-6xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-200 pb-6">
            <div class="flex items-center gap-4">
                <a href="index.php?route=etablissement/accueil" class="text-sm font-semibold text-[#071d3b] hover:underline">Retour</a>
                <h1 class="text-2xl font-extrabold text-[#071d3b] sm:text-3xl">Mes filières</h1>
            </div>
            <a href="index.php?route=etablissement/filiere-ajouter" class="rounded-lg bg-[#f1b456] px-5 py-2 text-sm font-bold text-[#071d3b] hover:bg-[#e4a744]">
                Ajouter une filière
            </a>
        </div>

        <?php if (empty($offres)): ?>
            <div class="py-16 text-center text-slate-600">
                <p class="mb-3">Vous n'avez encore proposé aucune filière.</p>
                <a href="index.php?route=etablissement/filiere-ajouter" class="font-semibold text-[#071d3b] hover:underline">Ajouter votre première filière</a>
            </div>
        <?php else: ?>
            <div class="mt-6 space-y-4">
                <?php foreach ($offres as $offre): ?>
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#f1b456]/60">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-[#071d3b]"><?= htmlspecialchars($offre['filiere_nom']) ?></h3>
                                <div class="mt-2 flex flex-wrap gap-2 text-xs text-slate-600">
                                    <span class="rounded-full bg-slate-100 px-3 py-1">Frais : <?= number_format($offre['frais_scolarite'], 0, ',', ' ') ?> Ar/an</span>
                                    <span class="rounded-full bg-slate-100 px-3 py-1">Places : <?= $offre['place_disponible'] ?></span>
                                    <?php if ($offre['duree_formation']): ?>
                                        <span class="rounded-full bg-slate-100 px-3 py-1">Durée : <?= htmlspecialchars($offre['duree_formation']) ?></span>
                                    <?php endif; ?>
                                </div>

                                <?php if ($offre['serie_bac'] || $offre['moyenne_bac']): ?>
                                    <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                        <?php if ($offre['serie_bac']): ?>
                                            <span class="rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-sky-800">Série <?= htmlspecialchars($offre['serie_bac']) ?></span>
                                        <?php endif; ?>
                                        <?php if ($offre['moyenne_bac']): ?>
                                            <span class="rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-amber-800">Moyenne min. <?= htmlspecialchars($offre['moyenne_bac']) ?>/20</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="flex gap-2">
                                <a href="index.php?route=etablissement/filiere-modifier&id=<?= $offre['id_offre_filiere'] ?>" class="rounded-lg border border-[#f1b456]/50 bg-[#f1b456]/15 px-4 py-2 text-sm font-semibold text-[#8a5a10] hover:bg-[#f1b456]/25">
                                    Modifier
                                </a>
                                <a href="index.php?route=etablissement/filiere-supprimer&id=<?= $offre['id_offre_filiere'] ?>" onclick="return confirm('Supprimer cette filière ? Les candidatures associées seront aussi supprimées.')" class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-100">
                                    Supprimer
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>
