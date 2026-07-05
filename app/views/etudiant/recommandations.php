<?php
// $etudiant        : données de l'étudiant connecté
// $recommandations : tableau trié par score DESC
?>
<main class="mx-auto w-full max-w-6xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">
        <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-[#071d3b] sm:text-3xl">Mes recommandations</h1>
                <p class="mt-1 text-sm text-slate-500">Offres classées par compatibilité avec votre profil.</p>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-2 sm:gap-3">
                <form method="POST" action="index.php?route=etudiant/recommandations">
                    <button type="submit" class="rounded-lg bg-[#f1b456] px-5 py-2 text-sm font-bold text-[#071d3b] hover:bg-[#e4a744]">
                        Générer mes recommandations
                    </button>
                </form>
                <a href="index.php?route=etudiant/accueil" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-[#071d3b] hover:border-[#f1b456]">
                    Retour
                </a>
            </div>
        </div>

        <?php if (!$etudiant['serie'] || !$etudiant['moyenne']): ?>
            <div class="mt-6 rounded-xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-800">
                Votre profil est incomplet.
                <a href="index.php?route=etudiant/profil" class="font-semibold underline">
                    Renseignez votre série et moyenne de bac
                </a>
                pour obtenir des recommandations précises.
            </div>
        <?php endif; ?>

        <?php if (empty($recommandations)): ?>
            <div class="py-16 text-center text-slate-600">
                <p class="mb-3">Aucune recommandation pour le moment.</p>
                <p class="text-sm">Cliquez sur "Générer mes recommandations" pour analyser votre profil.</p>
            </div>
        <?php else: ?>
            <p class="mt-6 text-sm text-slate-600">
                <?= count($recommandations) ?> offre(s) compatible(s) trouvée(s), triées par score de compatibilité.
            </p>

            <div class="mt-5 space-y-4">
                <?php foreach ($recommandations as $i => $reco): ?>
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#f1b456]/60">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between px-2">
                            <div class="flex-1">
                                <div class="mb-3 flex items-start gap-3">
                                    <span class="inline-flex rounded-lg bg-[#f1b456]/20 px-2 py-1 text-xs font-bold text-[#8a5a10]">#<?= $i + 1 ?></span>
                                    <div>
                                        <h3 class="text-lg font-bold text-[#071d3b]"><?= htmlspecialchars($reco['filiere_nom']) ?></h3>
                                        <p class="text-sm font-medium text-slate-600">
                                            <?= htmlspecialchars($reco['etablissement_nom']) ?>
                                            <?php if ($reco['ville']): ?>
                                                — <?= htmlspecialchars($reco['ville']) ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="mb-1 flex justify-between text-xs text-slate-500">
                                        <span>Compatibilité</span>
                                        <span class="font-bold text-[#071d3b]"><?= $reco['score'] ?>/100</span>
                                    </div>
                                    <div class="h-2 w-full rounded-full bg-slate-200">
                                        <div class="h-2 rounded-full bg-[#f1b456] transition-all duration-500" style="width: <?= $reco['score'] ?>%"></div>
                                    </div>
                                </div>

                                <p class="text-sm italic text-slate-600"><?= htmlspecialchars($reco['justification']) ?></p>

                                <div class="mt-3 flex flex-wrap gap-2 text-xs text-slate-600">
                                    <span class="rounded-full bg-slate-100 px-3 py-1">Frais : <?= number_format($reco['frais_scolarite'], 0, ',', ' ') ?> Ar/an</span>
                                    <span class="rounded-full bg-slate-100 px-3 py-1">Places : <?= $reco['place_disponible'] ?></span>
                                    <?php if ($reco['duree_formation']): ?>
                                        <span class="rounded-full bg-slate-100 px-3 py-1">Durée : <?= htmlspecialchars($reco['duree_formation']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <form method="POST" action="index.php?route=etudiant/candidature-soumettre" class="shrink-0">
                                <input type="hidden" name="id_offre_filiere" value="<?= $reco['id_offre_filiere'] ?>">
                                <button type="submit" class="rounded-lg bg-[#f1b456] px-5 py-2 text-sm font-bold text-[#071d3b] hover:bg-[#e4a744]">
                                    Postuler
                                </button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>
