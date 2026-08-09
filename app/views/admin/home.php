<?php
// Variables disponibles :
// $stats               : tableau de statistiques globales
// $recentApplications  : 5 dernières candidatures
// $recentInstitutions  : 5 derniers établissements

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
            <div class="flex items-center gap-4">
                <img src="assets/img/admin.svg" alt="Établissement" class="h-14 w-14 rounded-xl bg-slate-100 p-2 object-contain">
                <div>
                    <h1 class="text-2xl font-extrabold text-[#071d3b] sm:text-3xl">Administrateur</h1>
                    <p class="mt-1 text-sm text-slate-600"><span class="font-bold text-[#071d3b]">Vue d'ensemble de la plateforme</span></p>
                </div>
            </div>
        </div>

        <!-- ── Statistiques globales ── -->
        <h2 class="mt-8 text-base font-bold text-[#071d3b] uppercase tracking-wide">Statistiques générales</h2>
        <div class="mt-4 grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Utilisateurs totaux</p>
                <p class="mt-2 text-3xl font-extrabold text-[#071d3b]"><?= $stats['nb_utilisateurs'] ?></p>
            </div>

            <div class="rounded-2xl border border-sky-200 bg-sky-50 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-sky-700">Étudiants</p>
                <p class="mt-2 text-3xl font-extrabold text-sky-900"><?= $stats['nb_etudiants'] ?></p>
            </div>

            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Établissements</p>
                <p class="mt-2 text-3xl font-extrabold text-emerald-900"><?= $stats['nb_etablissements'] ?></p>
            </div>

            <div class="rounded-2xl border border-violet-200 bg-violet-50 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-violet-700">Administrateurs</p>
                <p class="mt-2 text-3xl font-extrabold text-violet-900"><?= $stats['nb_admins'] ?></p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Filières (catalogue)</p>
                <p class="mt-2 text-3xl font-extrabold text-[#071d3b]"><?= $stats['nb_filieres'] ?></p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Formations publiées</p>
                <p class="mt-2 text-3xl font-extrabold text-[#071d3b]"><?= $stats['nb_offres'] ?></p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Candidatures totales</p>
                <p class="mt-2 text-3xl font-extrabold text-[#071d3b]"><?= $stats['nb_candidatures'] ?></p>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">En attente</p>
                <p class="mt-2 text-3xl font-extrabold text-amber-800"><?= $stats['nb_en_attente'] ?></p>
            </div>

        </div>

        <!-- ── Raccourcis rapides ── -->
        <h2 class="mt-10 text-base font-bold text-[#071d3b] uppercase tracking-wide">Accès rapide</h2>
        <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

            <a href="index.php?route=admin/users"
               class="group flex flex-col gap-2 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#f1b456] hover:shadow-md">
                <span class="text-2xl"><img src="assets/img/user.svg" alt="Utilisateur" class="h-14 w-14 rounded-xl bg-slate-100 p-2 object-contain"></span>
                <h3 class="text-sm font-bold text-[#071d3b]">Utilisateurs</h3>
                <p class="text-xs text-slate-500">Gérer les comptes étudiants, établissements et admins.</p>
            </a>

            <a href="index.php?route=admin/institutions"
               class="group flex flex-col gap-2 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#f1b456] hover:shadow-md">
                <span class="text-2xl"><img src="assets/img/school.webp" alt="Établissement" class="h-14 w-14 rounded-xl bg-slate-100 p-2 object-contain"></span>
                <h3 class="text-sm font-bold text-[#071d3b]">Établissements</h3>
                <p class="text-xs text-slate-500">Superviser les établissements inscrits.</p>
            </a>

            <a href="index.php?route=admin/formations"
               class="group flex flex-col gap-2 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#f1b456] hover:shadow-md">
                <span class="text-2xl"><img src="assets/img/formation.svg" alt="Formation" class="h-14 w-14 rounded-xl bg-slate-100 p-2 object-contain"></span>
                <h3 class="text-sm font-bold text-[#071d3b]">Formations</h3>
                <p class="text-xs text-slate-500">Consulter et gérer les offres de formation.</p>
            </a>

            <a href="index.php?route=admin/programs"
               class="group flex flex-col gap-2 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#f1b456] hover:shadow-md">
                <span class="text-2xl"><img src="assets/img/filiere.webp" alt="Filière" class="h-14 w-14 rounded-xl bg-slate-100 p-2 object-contain"></span>
                <h3 class="text-sm font-bold text-[#071d3b]">Filières</h3>
                <p class="text-xs text-slate-500">Gérer le catalogue de référence des filières.</p>
            </a>

            <a href="index.php?route=admin/applications"
               class="group flex flex-col gap-2 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#f1b456] hover:shadow-md sm:col-span-2 lg:col-span-4">
                <span class="text-2xl"><img src="assets/img/candidature.webp" alt="Candidature" class="h-14 w-14 rounded-xl bg-slate-100 p-2 object-contain"></span>
                <h3 class="text-sm font-bold text-[#071d3b]">Candidatures</h3>
                <p class="text-xs text-slate-500">Superviser l'ensemble des candidatures | <?= $stats['nb_en_attente'] ?> en attente de traitement.</p>
            </a>

        </div>

        <!-- ── Candidatures récentes ── -->
        <?php if (!empty($recentApplications)): ?>
        <div class="mt-10">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-[#071d3b] uppercase tracking-wide">Candidatures récentes</h2>
                <a href="index.php?route=admin/applications"
                   class="text-xs font-semibold text-[#071d3b] hover:text-[#f1b456]">
                    Voir tout 
                </a>
            </div>

            <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Étudiant</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Formation</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Établissement</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <?php foreach ($recentApplications as $c):
                            $badgeClass = $badgeColors[$c['statut']] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                            $badgeLabel = $badgeLabels[$c['statut']] ?? $c['statut'];
                        ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium text-[#071d3b]">
                                    <?= htmlspecialchars($c['etudiant_prenom'] . ' ' . $c['etudiant_nom']) ?>
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    <?= htmlspecialchars($c['filiere_nom']) ?>
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    <?= htmlspecialchars($c['etablissement_nom']) ?>
                                </td>
                                <td class="px-4 py-3 text-slate-500">
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

        <!-- ── Établissements récents ── -->
        <?php if (!empty($recentInstitutions)): ?>
        <div class="mt-10">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-[#071d3b] uppercase tracking-wide">Derniers établissements inscrits</h2>
                <a href="index.php?route=admin/institutions"
                   class="text-xs font-semibold text-[#071d3b] hover:text-[#f1b456]">
                    Voir tout 
                </a>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($recentInstitutions as $e): ?>
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <h3 class="text-sm font-bold text-[#071d3b]">
                            <?= htmlspecialchars($e['nom']) ?>
                        </h3>
                        <p class="mt-1 text-xs text-slate-500">
                            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $e['type']))) ?>
                        </p>
                        <p class="mt-1 text-xs text-slate-400">
                            <?= htmlspecialchars($e['email']) ?> · <?= $e['nb_offres'] ?> <?= $e['nb_offres'] > 1 ? 'formations' : 'formation' ?>
                        </p>
                        <a href="index.php?route=admin/institution/view&id=<?= $e['id_etablissement'] ?>"
                           class="mt-2 inline-block text-xs font-semibold text-[#071d3b] hover:text-[#f1b456]">
                            Voir
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </section>
</main>
