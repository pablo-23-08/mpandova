<?php
// $candidatures : liste des candidatures reçues
// $statut       : filtre actif ('tous', 'en_attente', 'acceptee', 'refusee')
?>
<main class="mx-auto w-full max-w-6xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">
        <div class="flex items-center gap-4 border-b border-slate-200 pb-6">
            <a href="index.php?route=etablissement/accueil" class="text-sm font-semibold text-[#071d3b] hover:underline">Retour</a>
            <h1 class="text-2xl font-extrabold text-[#071d3b] sm:text-3xl">Candidatures reçues</h1>
        </div>

        <div class="mt-6 flex flex-wrap gap-2">
            <?php
            $onglets = [
                'tous'       => 'Toutes',
                'en_attente' => 'En attente',
                'acceptee'   => 'Acceptées',
                'refusee'    => 'Refusées',
            ];
            foreach ($onglets as $val => $label):
                $isActif = $statut === $val;
            ?>
                <a href="index.php?route=etablissement/candidatures&statut=<?= $val ?>" class="rounded-lg px-4 py-2 text-sm font-semibold <?= $isActif ? 'bg-[#f1b456] text-[#071d3b]' : 'border border-slate-300 bg-white text-slate-600 hover:border-[#f1b456]' ?>">
                    <?= $label ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($candidatures)): ?>
            <div class="py-16 text-center text-slate-600">
                <p>Aucune candidature pour ce filtre.</p>
            </div>
        <?php else: ?>
            <div class="mt-6 space-y-3">
                <?php foreach ($candidatures as $c): ?>
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#f1b456]/60">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-[#071d3b]">
                                    <?= htmlspecialchars($c['etudiant_prenom'] . ' ' . $c['etudiant_nom']) ?>
                                </h3>
                                <p class="text-sm font-medium text-slate-600"><?= htmlspecialchars($c['filiere_nom']) ?></p>
                                <div class="mt-1 flex flex-wrap gap-2 text-xs text-slate-500">
                                    <?php if ($c['serie']): ?>
                                        <span class="rounded-full bg-slate-100 px-2 py-1">Série <?= htmlspecialchars($c['serie']) ?></span>
                                    <?php endif; ?>
                                    <?php if ($c['moyenne']): ?>
                                        <span class="rounded-full bg-slate-100 px-2 py-1">Moyenne <?= htmlspecialchars($c['moyenne']) ?>/20</span>
                                    <?php endif; ?>
                                </div>
                                <p class="mt-1 text-xs text-slate-500">Soumis le <?= date('d/m/Y', strtotime($c['date_candidature'])) ?></p>
                            </div>

                            <div class="flex items-center gap-2">
                                <?php if ($c['statut'] === 'en_attente'): ?>
                                    <form method="POST" action="index.php?route=etablissement/candidature-traiter" class="flex gap-2">
                                        <input type="hidden" name="id_candidature" value="<?= $c['id_candidature'] ?>">
                                        <button type="submit" name="statut" value="acceptee" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100">
                                            Accepter
                                        </button>
                                        <button type="submit" name="statut" value="refusee" class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-100">
                                            Refuser
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <?php
                                    $badges = [
                                        'acceptee' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'refusee'  => 'bg-rose-100 text-rose-800 border-rose-200',
                                        'annulee'  => 'bg-slate-100 text-slate-700 border-slate-200',
                                    ];
                                    $labels = [
                                        'acceptee' => 'Acceptée',
                                        'refusee'  => 'Refusée',
                                        'annulee'  => 'Annulée par l\'étudiant',
                                    ];
                                    $badgeClass = $badges[$c['statut']] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                    $badgeLabel = $labels[$c['statut']] ?? $c['statut'];
                                    ?>
                                    <span class="rounded-full border px-3 py-1 text-xs font-semibold <?= $badgeClass ?>">
                                        <?= $badgeLabel ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>
