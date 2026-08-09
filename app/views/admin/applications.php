<?php
// $candidatures : toutes les candidatures (filtrées ou non)
// $statut       : filtre actif ('tous' | 'en_attente' | 'acceptee' | 'refusee' | 'annulee')
?>
<main class="mx-auto w-full max-w-6xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">

        <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-[#071d3b] sm:text-3xl">
                    Toutes les candidatures
                </h1>
            </div>
            <a href="index.php?route=admin/home"
               class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-[#071d3b] hover:border-[#f1b456]">
                Retour
            </a>
        </div>

        <!-- Onglets de filtrage (identique au filtre de l'établissement,
             mais avec 5 statuts au lieu de 4 car l'admin voit aussi 'annulee') -->
        <div class="mt-6 flex flex-wrap gap-2">
            <?php
            $onglets = [
                'tous'       => 'Toutes',
                'en_attente' => 'En attente',
                'acceptee'   => 'Acceptées',
                'refusee'    => 'Refusées',
                'annulee'    => 'Annulées',
            ];
            foreach ($onglets as $val => $label):
                $isActif = $statut === $val;
            ?>
                <a href="index.php?route=admin/applications&statut=<?= $val ?>"
                   class="rounded-lg px-4 py-2 text-sm font-semibold <?= $isActif
                        ? 'bg-[#f1b456] text-[#071d3b]'
                        : 'border border-slate-300 bg-white text-slate-600 hover:border-[#f1b456]' ?>">
                    <?= $label ?>
                </a>
            <?php endforeach; ?>
        </div>

        <p class="mt-4 text-sm text-slate-500">
            <?= count($candidatures) ?> candidature(s)
        </p>

        <?php if (empty($candidatures)): ?>

            <div class="py-16 text-center text-slate-600">
                <p>Aucune candidature pour ce filtre.</p>
            </div>

        <?php else: ?>

            <div class="mt-4 space-y-3">
                <?php foreach ($candidatures as $c):
                    // Même tableau de badges que dans les autres vues
                    $badges = [
                        'en_attente' => ['bg-amber-100 text-amber-800 border-amber-200',   'En attente'],
                        'acceptee'   => ['bg-emerald-100 text-emerald-800 border-emerald-200', 'Acceptée'],
                        'refusee'    => ['bg-rose-100 text-rose-800 border-rose-200',       'Refusée'],
                        'annulee'    => ['bg-slate-100 text-slate-700 border-slate-200',    'Annulée'],
                    ];
                    [$badgeClass, $badgeLabel] = $badges[$c['statut']]
                        ?? ['bg-slate-100 text-slate-700 border-slate-200', $c['statut']];
                ?>

                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">

                            <div>
                                <!-- Étudiant → Filière (Établissement) -->
                                <h3 class="text-sm font-bold text-[#071d3b]">
                                    <?= htmlspecialchars($c['etudiant_prenom'] . ' ' . $c['etudiant_nom']) ?>
                                    <span class="font-normal text-slate-400">→</span>
                                    <?= htmlspecialchars($c['filiere_nom']) ?>
                                </h3>

                                <p class="mt-1 text-xs text-slate-500">
                                    <?= htmlspecialchars($c['etablissement_nom']) ?>
                                    · Soumis le <?= date('d/m/Y', strtotime($c['date_candidature'])) ?>
                                    <?php if ($c['date_traitement']): ?>
                                        · Traité le <?= date('d/m/Y', strtotime($c['date_traitement'])) ?>
                                    <?php endif; ?>
                                </p>
                            </div>

                            <!-- Badge de statut (lecture seule : l'admin n'agit pas sur les candidatures) -->
                            <span class="inline-block shrink-0 rounded-lg border px-3 py-1 text-xs font-semibold <?= $badgeClass ?>">
                                <?= $badgeLabel ?>
                            </span>

                        </div>
                    </article>

                <?php endforeach; ?>
            </div>

        <?php endif; ?>

    </section>
</main>