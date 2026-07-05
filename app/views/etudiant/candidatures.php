<?php
// $candidatures : liste des candidatures de l'étudiant, triées par date DESC
?>
<main class="mx-auto w-full max-w-6xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">
        <div class="flex items-center gap-4 border-b border-slate-200 pb-6">
            <a href="index.php?route=etudiant/accueil" class="text-sm font-semibold text-[#071d3b] hover:underline">Retour</a>
            <h1 class="text-2xl font-extrabold text-[#071d3b] sm:text-3xl">Mes candidatures</h1>
        </div>

        <?php if (empty($candidatures)): ?>
            <div class="py-16 text-center text-slate-600">
                <p class="mb-3">Vous n'avez encore soumis aucune candidature.</p>
                <a href="index.php?route=etudiant/etablissements" class="font-semibold text-[#071d3b] hover:underline">
                    Explorer les filières disponibles
                </a>
            </div>
        <?php else: ?>
            <div class="mt-6 space-y-4">
                <?php foreach ($candidatures as $c): ?>
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#f1b456]/60">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-[#071d3b]"><?= htmlspecialchars($c['filiere_nom']) ?></h3>
                                <p class="text-sm font-medium text-slate-600"><?= htmlspecialchars($c['etablissement_nom']) ?></p>
                                <p class="mt-1 text-xs text-slate-500">Postulé le <?= date('d/m/Y à H:i', strtotime($c['date_candidature'])) ?></p>
                                <?php if ($c['date_traitement']): ?>
                                    <p class="text-xs text-slate-500">Traité le <?= date('d/m/Y', strtotime($c['date_traitement'])) ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="flex items-center gap-3">
                                <?php
                                $badges = [
                                    'en_attente' => ['bg-amber-100 text-amber-800 border-amber-200', 'En attente'],
                                    'acceptee'   => ['bg-emerald-100 text-emerald-800 border-emerald-200', 'Acceptée'],
                                    'refusee'    => ['bg-rose-100 text-rose-800 border-rose-200', 'Refusée'],
                                    'annulee'    => ['bg-slate-100 text-slate-700 border-slate-200', 'Annulée'],
                                ];
                                [$badgeClass, $badgeLabel] = $badges[$c['statut']] ?? ['bg-slate-100 text-slate-700 border-slate-200', $c['statut']];
                                ?>

                                <span class="rounded-full border px-3 py-1 text-xs font-semibold <?= $badgeClass ?>">
                                    <?= $badgeLabel ?>
                                </span>

                                <?php if ($c['statut'] === 'en_attente'): ?>
                                    <form method="POST" action="index.php?route=etudiant/candidature-annuler">
                                        <input type="hidden" name="id_candidature" value="<?= $c['id_candidature'] ?>">
                                        <button type="submit" onclick="return confirm('Annuler cette candidature ?')" class="rounded-lg border border-rose-200 px-3 py-1 text-sm font-medium text-rose-700 hover:bg-rose-50">
                                            Annuler
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>
