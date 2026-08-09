<?php
// $etablissement : données complètes de l'établissement
// $formations    : offres de formation de l'établissement
?>
<main class="mx-auto w-full max-w-4xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
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

        <!-- Informations -->
        <div class="mt-6 grid gap-6 sm:grid-cols-2">

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-[#071d3b]">Informations générales</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="font-semibold text-slate-600">Email :</dt>
                        <dd class="text-slate-800"><?= htmlspecialchars($etablissement['email']) ?></dd>
                    </div>
                    <?php if (!empty($etablissement['site_web'])): ?>
                    <div class="flex justify-between">
                        <dt class="font-semibold text-slate-600">Site web :</dt>
                        <dd>
                            <a href="<?= htmlspecialchars($etablissement['site_web']) ?>"
                               target="_blank"
                               class="text-[#071d3b] hover:underline">
                                <?= htmlspecialchars($etablissement['site_web']) ?>
                            </a>
                        </dd>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($etablissement['ville'])): ?>
                    <div class="flex justify-between">
                        <dt class="font-semibold text-slate-600">Ville :</dt>
                        <dd class="text-slate-800"><?= htmlspecialchars($etablissement['ville']) ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($etablissement['region'])): ?>
                    <div class="flex justify-between">
                        <dt class="font-semibold text-slate-600">Région :</dt>
                        <dd class="text-slate-800"><?= htmlspecialchars($etablissement['region']) ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($etablissement['adresse'])): ?>
                    <div class="flex justify-between">
                        <dt class="font-semibold text-slate-600">Adresse :</dt>
                        <dd class="text-slate-800"><?= htmlspecialchars($etablissement['adresse']) ?></dd>
                    </div>
                    <?php endif; ?>
                </dl>
            </div>

            <?php if (!empty($etablissement['description'])): ?>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-[#071d3b]">Description</h2>
                <p class="text-sm leading-relaxed text-slate-700">
                    <?= nl2br(htmlspecialchars($etablissement['description'])) ?>
                </p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Formations proposées -->
        <div class="mt-8">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold uppercase tracking-wide text-[#071d3b]">Formations proposées</h2>
                <span class="text-xs text-slate-500"><?= count($formations ?? []) ?> <?= count($formations ?? []) > 1 ? 'formations' : 'formation' ?></span>
            </div>

            <?php if (empty($formations)): ?>
                <p class="mt-4 text-sm text-slate-500">Cet établissement n'a pas encore de formations publiées.</p>
            <?php else: ?>
                <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-200">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Filière</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Durée</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Places</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Frais (Ar/an)</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <?php foreach ($formations as $f): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 font-medium text-[#071d3b]"><?= htmlspecialchars($f['filiere_nom']) ?></td>
                                    <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($f['duree_formation'] ?? '—') ?></td>
                                    <td class="px-4 py-3 text-slate-600"><?= $f['place_disponible'] ?? '—' ?></td>
                                    <td class="px-4 py-3 text-slate-600">
                                        <?= $f['frais_scolarite'] > 0 ? number_format($f['frais_scolarite'], 0, ',', ' ') : '—' ?>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-end gap-2">
                                            <a href="index.php?route=admin/formation/view&id=<?= $f['id_offre_filiere'] ?>"
                                               class="rounded-lg border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-700 hover:border-[#f1b456]">
                                                Voir
                                            </a>
                                            <a href="index.php?route=admin/formation/edit&id=<?= $f['id_offre_filiere'] ?>"
                                               class="rounded-lg border border-[#f1b456]/50 bg-[#f1b456]/15 px-3 py-1 text-xs font-semibold text-[#8a5a10] hover:bg-[#f1b456]/25">
                                                Modifier
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Zone dangereuse -->
        <div class="mt-10 rounded-2xl border border-rose-200 bg-rose-50 p-5">
            <h3 class="text-sm font-bold text-rose-800">Zone dangereuse</h3>
            <p class="mt-1 text-xs text-rose-700">
                La suppression de cet établissement supprimera toutes ses formations, toutes les candidatures associées
                et son compte utilisateur. Cette action est irréversible.
            </p>
            <a href="index.php?route=admin/institution/delete&id=<?= $etablissement['id_etablissement'] ?>"
               onclick="return confirm('Supprimer définitivement cet établissement ? Cette action est irréversible.')"
               class="mt-4 inline-block rounded-lg border border-rose-300 bg-white px-4 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-100">
                Supprimer l'établissement
            </a>
        </div>

    </section>
</main>
