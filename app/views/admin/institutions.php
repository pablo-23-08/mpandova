<?php
// $etablissements : liste des établissements
// $search         : terme de recherche
?>
<main class="mx-auto w-full max-w-6xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">

        <!-- En-tête -->
        <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-[#071d3b] sm:text-3xl">
                    Gestion des établissements
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    <?= count($etablissements) ?> <?= count($etablissements) > 1 ? 'établissements' : 'établissement' ?> sur la plateforme
                </p>
            </div>
            <a href="index.php?route=admin/home"
               class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-[#071d3b] hover:border-[#f1b456]">
                Retour
            </a>
        </div>

        <!-- Barre de recherche -->
        <form method="GET" action="index.php" class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center">
            <input type="hidden" name="route" value="admin/institutions">
            <div class="flex-1">
                <input
                    type="text"
                    name="q"
                    value="<?= htmlspecialchars($search) ?>"
                    placeholder="Rechercher un établissement par nom, email ou ville..."
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30"
                >
            </div>
            <button type="submit"
                    class="rounded-lg bg-[#f1b456] px-4 py-2 text-sm font-bold text-[#071d3b] hover:bg-[#e4a744]">
                Rechercher
            </button>
            <?php if ($search): ?>
                <a href="index.php?route=admin/institutions"
                   class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm text-slate-600 hover:border-rose-400 hover:text-rose-600">
                    Réinitialiser
                </a>
            <?php endif; ?>
        </form>

        <!-- Liste des établissements -->
        <?php if (empty($etablissements)): ?>
            <div class="py-16 text-center text-slate-600">
                <p class="text-sm font-semibold">Aucun établissement trouvé.</p>
                <?php if ($search): ?>
                    <a href="index.php?route=admin/institutions"
                       class="mt-2 inline-block text-sm font-semibold text-[#071d3b] hover:underline">
                        Voir tous les établissements
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="mt-6 overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Etablissement</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Ville</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Formations</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <?php foreach ($etablissements as $e): ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-[#071d3b]"><?= htmlspecialchars($e['nom']) ?></div>
                                    <div class="text-xs text-slate-400"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $e['type']))) ?></div>
                                </td>
                                <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($e['email']) ?></td>
                                <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($e['ville'] ?? '—') ?></td>
                                <td class="px-4 py-3">
                                    <span class="inline-block rounded-lg border border-slate-200 bg-slate-50 px-2 py-0.5 text-xs font-semibold text-slate-700">
                                        <?= $e['nb_offres'] ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a href="index.php?route=admin/institution/view&id=<?= $e['id_etablissement'] ?>"
                                           class="rounded-lg border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-700 hover:border-[#f1b456]">
                                            Voir
                                        </a>
                                        <a href="index.php?route=admin/institution/edit&id=<?= $e['id_etablissement'] ?>"
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

    </section>
</main>
