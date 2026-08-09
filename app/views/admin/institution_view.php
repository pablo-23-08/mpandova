<?php
// $etablissement : données complètes de l'établissement
// $programmes    : formations proposées
// $candidatures  : dernières candidatures reçues

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
<main class="mx-auto w-full max-w-5xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">

        <!-- En-tête -->
        <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-[#071d3b] sm:text-3xl">
                    <?= htmlspecialchars($etablissement['nom']) ?>
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    <?= htmlspecialchars(ucwords(str_replace('_', ' ', $etablissement['type']))) ?>
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="index.php?route=admin/institution/edit&id=<?= $etablissement['id_etablissement'] ?>"
                   class="rounded-lg bg-[#f1b456] px-4 py-2 text-sm font-bold text-[#071d3b] hover:bg-[#e4a744]">
                    Modifier
                </a>
                <a href="index.php?route=admin/institutions"
                   class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-[#071d3b] hover:border-[#f1b456]">
                    Retour
                </a>
            </div>
        </div>

        <!-- Compteurs rapides -->
        <div class="mt-6 grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm">
                <p class="text-3xl font-extrabold text-[#071d3b]"><?= count($programmes) ?></p>
                <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-500">Formations</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm">
                <p class="text-3xl font-extrabold text-[#071d3b]"><?= $etablissement['nb_candidatures'] ?></p>
                <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-500">Candidatures reçues</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm">
                <p class="text-3xl font-extrabold text-[#071d3b]">
                    <?= !empty($etablissement['ville']) ? htmlspecialchars($etablissement['ville']) : '—' ?>
                </p>
                <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-500">Ville</p>
            </div>
        </div>

        <!-- Informations détaillées -->
        <div class="mt-8 grid gap-6 lg:grid-cols-2">

            <!-- Infos générales -->
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-[#071d3b]">Informations</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex flex-col gap-1">
                        <dt class="font-semibold text-slate-600">Email :</dt>
                        <dd class="text-slate-800"><?= htmlspecialchars($etablissement['email']) ?></dd>
                    </div>
                    <div class="flex flex-col gap-1">
                        <dt class="font-semibold text-slate-600">Adresse :</dt>
                        <dd class="text-slate-800"><?= htmlspecialchars($etablissement['adresse'] ?? '—') ?></dd>
                    </div>
                    <div class="flex flex-col gap-1">
                        <dt class="font-semibold text-slate-600">Ville :</dt>
                        <dd class="text-slate-800"><?= htmlspecialchars($etablissement['ville'] ?? '—') ?></dd>
                    </div>
                    <div class="flex flex-col gap-1">
                        <dt class="font-semibold text-slate-600">Région :</dt>
                        <dd class="text-slate-800"><?= htmlspecialchars($etablissement['region'] ?? '—') ?></dd>
                    </div>
                    <?php if (!empty($etablissement['site_web'])): ?>
                    <div class="flex flex-col gap-1">
                        <dt class="font-semibold text-slate-600">Site web :</dt>
                        <dd>
                            <a href="<?= htmlspecialchars($etablissement['site_web']) ?>"
                               target="_blank" rel="noopener"
                               class="text-sky-600 hover:underline text-xs">
                                <?= htmlspecialchars($etablissement['site_web']) ?>
                            </a>
                        </dd>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($etablissement['description'])): ?>
                    <div class="flex flex-col gap-1">
                        <dt class="font-semibold text-slate-600">Description :</dt>
                        <dd class="text-slate-700 text-xs leading-relaxed"><?= nl2br(htmlspecialchars($etablissement['description'])) ?></dd>
                    </div>
                    <?php endif; ?>
                </dl>

                <div class="mt-4 border-t border-slate-200 pt-4">
                    <a href="index.php?route=admin/user/view&id=<?= $etablissement['id_utilisateur'] ?>"
                       class="text-xs font-semibold text-[#071d3b] hover:text-[#f1b456]">
                        → Voir le compte utilisateur associé
                    </a>
                </div>
            </div>

            <!-- Formations -->
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-[#071d3b]">
                    Formations proposées (<?= count($programmes) ?>)
                </h2>
                <?php if (empty($programmes)): ?>
                    <p class="text-sm text-slate-500">Aucune formation publiée.</p>
                <?php else: ?>
                    <div class="space-y-2">
                        <?php foreach ($programmes as $p): ?>
                            <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs">
                                <div>
                                    <span class="font-semibold text-[#071d3b]"><?= htmlspecialchars($p['filiere_nom']) ?></span>
                                    <?php if ($p['duree_formation']): ?>
                                        <span class="ml-2 text-slate-400">· <?= htmlspecialchars($p['duree_formation']) ?></span>
                                    <?php endif; ?>
                                    <span class="ml-2 text-slate-400">· <?= $p['nb_candidatures'] ?> candidature(s)</span>
                                </div>
                                <a href="index.php?route=admin/formation/view&id=<?= $p['id_offre_filiere'] ?>"
                                   class="ml-3 shrink-0 font-semibold text-[#071d3b] hover:text-[#f1b456]">→</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- Dernières candidatures reçues -->
        <?php if (!empty($candidatures)): ?>
        <div class="mt-8">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold uppercase tracking-wide text-[#071d3b]">
                    Candidatures récentes
                </h2>
                <a href="index.php?route=admin/applications&etablissement=<?= $etablissement['id_etablissement'] ?>"
                   class="text-xs font-semibold text-[#071d3b] hover:text-[#f1b456]">
                    Voir toutes →
                </a>
            </div>
            <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Étudiant</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Formation</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <?php foreach ($candidatures as $c):
                            $badgeClass = $badgeColors[$c['statut']] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                            $badgeLabel = $badgeLabels[$c['statut']] ?? $c['statut'];
                        ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium text-[#071d3b]">
                                    <?= htmlspecialchars($c['etudiant_prenom'] . ' ' . $c['etudiant_nom']) ?>
                                </td>
                                <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($c['filiere_nom']) ?></td>
                                <td class="px-4 py-3 text-xs text-slate-500">
                                    <?= date('d/m/Y', strtotime($c['date_candidature'])) ?>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-block rounded-lg border px-2 py-0.5 text-xs font-semibold <?= $badgeClass ?>">
                                        <?= $badgeLabel ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Zone dangereuse -->
        <div class="mt-8 rounded-2xl border border-rose-200 bg-rose-50 p-5">
            <h2 class="mb-3 text-sm font-bold text-rose-800">Zone dangereuse</h2>
            <p class="mb-4 text-xs text-rose-700">
                La suppression de cet établissement supprimera toutes ses formations, toutes les candidatures associées
                et son compte utilisateur. Cette action est irréversible.
            </p>
            <a href="index.php?route=admin/institution/delete&id=<?= $etablissement['id_etablissement'] ?>"
               onclick="return confirm('Êtes-vous sûr de vouloir supprimer l\'établissement « <?= addslashes(htmlspecialchars($etablissement['nom'])) ?> » ?\n\nCette action est IRRÉVERSIBLE.')"
               class="inline-block rounded-lg border border-rose-300 bg-white px-4 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-100">
                Supprimer l'établissement
            </a>
        </div>

    </section>
</main>
