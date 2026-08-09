<?php
// Variables disponibles :
// $candidatures        : liste des candidatures filtrées
// $statut              : filtre de statut actif
// $appStats            : statistiques par statut
// $institutions        : liste des établissements (pour le filtre)
// $etablissementFiltre : filtre établissement actif
// $offreFiltre         : filtre offre actif
// $search              : terme de recherche
// $dateFrom / $dateTo  : plage de dates

$badgeColors = [
    'en_attente' => 'bg-amber-100 text-amber-800 border-amber-200',
    'acceptee'   => 'bg-emerald-100 text-emerald-800 border-emerald-200',
    'refusee'    => 'bg-rose-100 text-rose-800 border-rose-200',
    'annulee'    => 'bg-slate-100 text-slate-600 border-slate-200',
];
$badgeLabels = [
    'en_attente' => 'En attente',
    'acceptee'   => 'Acceptée',
    'refusee'    => 'Refusée',
    'annulee'    => 'Annulée',
];
?>
<main class="mx-auto w-full max-w-6xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">

        <!-- En-tête -->
        <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-[#071d3b] sm:text-3xl">
                    Gestion des candidatures
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    <?= count($candidatures) ?> <?= count($candidatures) > 1 ? 'candidatures trouvées' : 'candidature trouvée' ?>
                </p>
            </div>
            <a href="index.php?route=admin/home"
               class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-[#071d3b] hover:border-[#f1b456]">
                Retour
            </a>
        </div>

        <!-- Statistiques rapides -->
        <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-center">
                <p class="text-2xl font-extrabold text-amber-800"><?= $appStats['en_attente'] ?></p>
                <p class="mt-1 text-xs font-semibold text-amber-700">En attente</p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-center">
                <p class="text-2xl font-extrabold text-emerald-800"><?= $appStats['acceptee'] ?></p>
                <p class="mt-1 text-xs font-semibold text-emerald-700">Acceptées</p>
            </div>
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-center">
                <p class="text-2xl font-extrabold text-rose-800"><?= $appStats['refusee'] ?></p>
                <p class="mt-1 text-xs font-semibold text-rose-700">Refusées</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-center">
                <p class="text-2xl font-extrabold text-slate-700"><?= $appStats['annulee'] ?></p>
                <p class="mt-1 text-xs font-semibold text-slate-600">Annulées</p>
            </div>
        </div>

        <!-- Filtres -->
        <form method="GET" action="index.php" class="mt-6 space-y-3">
            <input type="hidden" name="route" value="admin/applications">

            <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                <!-- Recherche -->
                <div class="flex-1 min-w-48">
                    <input
                        type="text"
                        name="q"
                        value="<?= htmlspecialchars($search) ?>"
                        placeholder="Rechercher par étudiant, formation, établissement…"
                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30"
                    >
                </div>

                <!-- Établissement -->
                <div>
                    <select name="etablissement" class="rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-[#f1b456]">
                        <option value="">Tous les établissements</option>
                        <?php foreach ($institutions as $inst): ?>
                            <option value="<?= $inst['id_etablissement'] ?>"
                                    <?= $etablissementFiltre == $inst['id_etablissement'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($inst['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit"
                        class="rounded-lg bg-[#f1b456] px-6 py-3 text-sm font-bold text-[#071d3b] hover:bg-[#e4a744]">
                    Filtrer
                </button>

                <?php if ($search || $etablissementFiltre || $statut !== 'tous' || $dateFrom || $dateTo): ?>
                    <a href="index.php?route=admin/applications"
                       class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm text-slate-600 hover:border-rose-400 hover:text-rose-600">
                        Réinitialiser
                    </a>
                <?php endif; ?>
            </div>

            <!-- Dates -->
            <div class="flex flex-wrap gap-3 items-center">
                <span class="text-sm font-semibold text-slate-600">Date :</span>
                <input
                    type="date"
                    name="date_debut"
                    value="<?= htmlspecialchars($dateFrom) ?>"
                    class="rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-[#f1b456]"
                >
                <span class="text-slate-400">à</span>
                <input
                    type="date"
                    name="date_fin"
                    value="<?= htmlspecialchars($dateTo) ?>"
                    class="rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-[#f1b456]"
                >
            </div>
        </form>

        <!-- Onglets de statut -->
        <div class="mt-4 flex flex-wrap gap-2">
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
                $params = http_build_query(array_filter([
                    'route'         => 'admin/applications',
                    'statut'        => $val,
                    'q'             => $search,
                    'etablissement' => $etablissementFiltre ?: '',
                    'date_debut'    => $dateFrom,
                    'date_fin'      => $dateTo,
                ]));
            ?>
                <a href="index.php?<?= $params ?>"
                   class="rounded-lg px-4 py-2 text-sm font-semibold <?= $isActif
                        ? 'bg-[#f1b456] text-[#071d3b]'
                        : 'border border-slate-300 bg-white text-slate-600 hover:border-[#f1b456]' ?>">
                    <?= $label ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Liste des candidatures -->
        <?php if (empty($candidatures)): ?>
            <div class="py-16 text-center text-slate-600">
                <p class="text-4xl">📝</p>
                <p class="mt-3 text-sm">Aucune candidature pour ce filtre.</p>
                <?php if ($statut !== 'tous' || $search || $etablissementFiltre): ?>
                    <a href="index.php?route=admin/applications" class="mt-2 inline-block text-sm font-semibold text-[#071d3b] hover:underline">
                        Voir toutes les candidatures
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="mt-6 overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Étudiant</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Formation</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Établissement</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Statut</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <?php foreach ($candidatures as $c):
                            $badgeClass = $badgeColors[$c['statut']] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                            $badgeLabel = $badgeLabels[$c['statut']] ?? $c['statut'];
                        ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-[#071d3b]">
                                        <?= htmlspecialchars($c['etudiant_prenom'] . ' ' . $c['etudiant_nom']) ?>
                                    </div>
                                    <div class="text-xs text-slate-400"><?= htmlspecialchars($c['etudiant_email']) ?></div>
                                </td>
                                <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($c['filiere_nom']) ?></td>
                                <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($c['etablissement_nom']) ?></td>
                                <td class="px-4 py-3 text-xs text-slate-500">
                                    <?= date('d/m/Y', strtotime($c['date_candidature'])) ?>
                                    <?php if ($c['date_traitement']): ?>
                                        <br><span class="text-slate-400">Traité le <?= date('d/m/Y', strtotime($c['date_traitement'])) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-block rounded-lg border px-2 py-0.5 text-xs font-semibold <?= $badgeClass ?>">
                                        <?= $badgeLabel ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="index.php?route=admin/application/view&id=<?= $c['id_candidature'] ?>"
                                       class="rounded-lg border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-700 hover:border-[#f1b456]">
                                        Voir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </section>
</main>
