<?php
// $offres              : liste des offres de formation
// $institutions        : liste des établissements (pour le filtre)
// $search              : terme de recherche
// $etablissementFiltre : ID de l'établissement filtré
// $niveauFiltre        : niveau filtré
?>
<main class="mx-auto w-full max-w-6xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">

        <!-- En-tête -->
        <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-[#071d3b] sm:text-3xl">
                    Gestion des formations
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    <?= count($offres) ?> <?= count($offres) > 1 ? 'formations' : 'formation' ?> 
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="index.php?route=admin/programs"
                   class="rounded-lg border border-[#f1b456]/50 bg-[#f1b456]/10 px-4 py-2 text-sm font-semibold text-[#8a5a10] hover:bg-[#f1b456]/25">
                    Gérer le catalogue de filières
                </a>
                <a href="index.php?route=admin/home"
                   class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-[#071d3b] hover:border-[#f1b456]">
                    Retour
                </a>
            </div>
        </div>

        <!-- Filtres -->
        <form method="GET" action="index.php" class="mt-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
            <input type="hidden" name="route" value="admin/formations">
            <div class="flex-1 min-w-48">
                <input
                    type="text"
                    name="q"
                    value="<?= htmlspecialchars($search) ?>"
                    placeholder="Rechercher une formation ou un établissement…"
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30"
                >
            </div>
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
            <div>
                <select name="niveau" class="rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-[#f1b456]">
                    <option value="">Tous les niveaux</option>
                    <option value="Licence" <?= $niveauFiltre === 'Licence' ? 'selected' : '' ?>>Licence</option>
                    <option value="Master"  <?= $niveauFiltre === 'Master'  ? 'selected' : '' ?>>Master</option>
                    <option value="Doctorat"<?= $niveauFiltre === 'Doctorat'? 'selected' : '' ?>>Doctorat</option>
                    <option value="BTS"     <?= $niveauFiltre === 'BTS'     ? 'selected' : '' ?>>BTS</option>
                    <option value="DUT"     <?= $niveauFiltre === 'DUT'     ? 'selected' : '' ?>>DUT</option>
                    <option value="Bacc"    <?= $niveauFiltre === 'Bacc'    ? 'selected' : '' ?>>Bacc+2</option>
                </select>
            </div>
            <button type="submit"
                    class="rounded-lg bg-[#f1b456] px-4 py-2 text-sm font-bold text-[#071d3b] hover:bg-[#e4a744]">
                Filtrer
            </button>
            <?php if ($search || $etablissementFiltre || $niveauFiltre): ?>
                <a href="index.php?route=admin/formations"
                   class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm text-slate-600 hover:border-rose-400 hover:text-rose-600">
                    Réinitialiser
                </a>
            <?php endif; ?>
        </form>

        <!-- Liste des formations -->
        <?php if (empty($offres)): ?>
            <div class="py-16 text-center text-slate-600">
                <p class="text-4xl">📚</p>
                <p class="mt-3 text-sm">Aucune formation trouvée.</p>
            </div>
        <?php else: ?>
            <div class="mt-6 overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Formation</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Établissement</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Durée</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Diplôme requis</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Candidatures</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <?php foreach ($offres as $o): ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-[#071d3b]"><?= htmlspecialchars($o['filiere_nom']) ?></div>
                                    <?php if ($o['frais_scolarite'] > 0): ?>
                                        <div class="text-xs text-slate-400"><?= number_format($o['frais_scolarite'], 0, ',', ' ') ?> Ar/an</div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($o['etablissement_nom']) ?></td>
                                <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($o['duree_formation'] ?? '—') ?></td>
                                <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($o['diplome_requis'] ?? '—') ?></td>
                                <td class="px-4 py-3">
                                    <span class="inline-block rounded-lg border border-slate-200 bg-slate-50 px-2 py-0.5 text-xs font-semibold text-slate-700">
                                        <?= $o['nb_candidatures'] ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a href="index.php?route=admin/formation/view&id=<?= $o['id_offre_filiere'] ?>"
                                           class="rounded-lg border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-700 hover:border-[#f1b456]">
                                            Voir
                                        </a>
                                        <a href="index.php?route=admin/formation/edit&id=<?= $o['id_offre_filiere'] ?>"
                                           class="rounded-lg border border-[#f1b456]/50 bg-[#f1b456]/10 px-3 py-1 text-xs font-semibold text-[#8a5a10] hover:bg-[#f1b456]/25">
                                            Modifier
                                        </a>
                                        <a href="index.php?route=admin/formation/delete&id=<?= $o['id_offre_filiere'] ?>"
                                           onclick="return confirm('Supprimer cette formation « <?= addslashes(htmlspecialchars($o['filiere_nom'])) ?> » de <?= addslashes(htmlspecialchars($o['etablissement_nom'])) ?> ?\n\nToutes les candidatures associées seront supprimées. Cette action est irréversible.')"
                                           class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-100">
                                            Supprimer
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
