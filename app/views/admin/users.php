<?php
// Variables disponibles :
// $utilisateurs : tableau des utilisateurs (avec recherche/filtre)
// $roleFiltre   : filtre de rôle actif ('tous', 'etudiant', 'etablissement', 'admin')
// $search       : terme de recherche
?>
<main class="mx-auto w-full max-w-6xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">

        <!-- En-tête -->
        <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-[#071d3b] sm:text-3xl">
                    Gestion des utilisateurs
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    <?= count($utilisateurs) ?> compte(s) trouvé(s)
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="index.php?route=admin/user/add"
                   class="rounded-lg bg-[#f1b456] px-4 py-2 text-sm font-bold text-[#071d3b] hover:bg-[#e4a744]">
                    + Ajouter un utilisateur
                </a>
                <a href="index.php?route=admin/home"
                   class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-[#071d3b] hover:border-[#f1b456]">
                    Retour
                </a>
            </div>
        </div>

        <!-- Barre de recherche et filtres -->
        <form method="GET" action="index.php" class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center">
            <input type="hidden" name="route" value="admin/users">
            <div class="flex-1">
                <input
                    type="text"
                    name="q"
                    value="<?= htmlspecialchars($search) ?>"
                    placeholder="Rechercher par nom, prénom ou email…"
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30"
                >
            </div>
            <div>
                <select name="role" class="rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-[#f1b456]">
                    <option value="tous"          <?= $roleFiltre === 'tous'          ? 'selected' : '' ?>>Tous les rôles</option>
                    <option value="etudiant"       <?= $roleFiltre === 'etudiant'       ? 'selected' : '' ?>>Étudiant</option>
                    <option value="etablissement"  <?= $roleFiltre === 'etablissement'  ? 'selected' : '' ?>>Établissement</option>
                    <option value="admin"          <?= $roleFiltre === 'admin'          ? 'selected' : '' ?>>Administrateur</option>
                </select>
            </div>
            <button type="submit"
                    class="rounded-lg bg-[#071d3b] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#0f2d54]">
                Rechercher
            </button>
            <?php if ($search || $roleFiltre !== 'tous'): ?>
                <a href="index.php?route=admin/users"
                   class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm text-slate-600 hover:border-rose-400 hover:text-rose-600">
                    Réinitialiser
                </a>
            <?php endif; ?>
        </form>

        <!-- Filtres rapides par rôle -->
        <div class="mt-4 flex flex-wrap gap-2">
            <?php
            $onglets = [
                'tous'         => ['label' => 'Tous', 'color' => 'bg-[#071d3b] text-white', 'border' => ''],
                'etudiant'     => ['label' => 'Étudiants', 'color' => 'bg-sky-100 text-sky-800', 'border' => 'border-sky-200'],
                'etablissement'=> ['label' => 'Établissements', 'color' => 'bg-emerald-100 text-emerald-800', 'border' => 'border-emerald-200'],
                'admin'        => ['label' => 'Admins', 'color' => 'bg-violet-100 text-violet-800', 'border' => 'border-violet-200'],
            ];
            foreach ($onglets as $val => $cfg):
                $isActif = $roleFiltre === $val;
                $baseUrl = "index.php?route=admin/users&role={$val}" . ($search ? "&q=" . urlencode($search) : '');
            ?>
                <a href="<?= $baseUrl ?>"
                   class="rounded-lg border px-3 py-1.5 text-xs font-semibold <?= $isActif
                        ? $cfg['color'] . ' ' . ($cfg['border'] ?: 'border-[#071d3b]')
                        : 'border-slate-200 bg-white text-slate-600 hover:border-[#f1b456]' ?>">
                    <?= $cfg['label'] ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Liste des utilisateurs -->
        <?php if (empty($utilisateurs)): ?>
            <div class="py-16 text-center text-slate-600">
                <p class="text-4xl">👤</p>
                <p class="mt-3 text-sm">Aucun utilisateur trouvé.</p>
                <?php if ($search || $roleFiltre !== 'tous'): ?>
                    <a href="index.php?route=admin/users" class="mt-2 inline-block text-sm font-semibold text-[#071d3b] hover:underline">
                        Voir tous les utilisateurs
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="mt-6 overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Nom / Organisme</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Rôle</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">ID</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <?php foreach ($utilisateurs as $u):
                            if ($u['role'] === 'etudiant') {
                                $nom        = htmlspecialchars(($u['etudiant_prenom'] ?? '') . ' ' . ($u['etudiant_nom'] ?? ''));
                                $badgeClass = 'bg-sky-100 text-sky-800 border-sky-200';
                                $badgeLabel = 'Étudiant';
                            } elseif ($u['role'] === 'etablissement') {
                                $nom        = htmlspecialchars($u['etablissement_nom'] ?? '—');
                                $badgeClass = 'bg-emerald-100 text-emerald-800 border-emerald-200';
                                $badgeLabel = 'Établissement';
                            } else {
                                $nom        = 'Administrateur';
                                $badgeClass = 'bg-violet-100 text-violet-800 border-violet-200';
                                $badgeLabel = 'Admin';
                            }
                        ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium text-[#071d3b]"><?= $nom ?></td>
                                <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($u['email']) ?></td>
                                <td class="px-4 py-3">
                                    <span class="inline-block rounded-lg border px-2 py-0.5 text-xs font-semibold <?= $badgeClass ?>">
                                        <?= $badgeLabel ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-400">#<?= $u['id_utilisateur'] ?></td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a href="index.php?route=admin/user/view&id=<?= $u['id_utilisateur'] ?>"
                                           class="rounded-lg border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-700 hover:border-[#f1b456]">
                                            Voir
                                        </a>
                                        <a href="index.php?route=admin/user/edit&id=<?= $u['id_utilisateur'] ?>"
                                           class="rounded-lg border border-[#f1b456]/50 bg-[#f1b456]/10 px-3 py-1 text-xs font-semibold text-[#8a5a10] hover:bg-[#f1b456]/25">
                                            Modifier
                                        </a>
                                        <?php if ($u['role'] !== 'admin'): ?>
                                            <a href="index.php?route=admin/user/delete&id=<?= $u['id_utilisateur'] ?>"
                                               onclick="return confirm('Supprimer le compte de <?= addslashes($nom) ?> ?\n\nToutes les données associées seront supprimées. Cette action est irréversible.')"
                                               class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-100">
                                                Supprimer
                                            </a>
                                        <?php endif; ?>
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
