<?php
// $utilisateur : données complètes de l'utilisateur
?>
<main class="mx-auto w-full max-w-4xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">

        <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-[#071d3b] sm:text-3xl">
                    Détail de l'utilisateur
                </h1>
                <p class="mt-1 text-xs text-slate-400">ID #<?= $utilisateur['id_utilisateur'] ?></p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="index.php?route=admin/user/edit&id=<?= $utilisateur['id_utilisateur'] ?>"
                   class="rounded-lg bg-[#f1b456] px-4 py-2 text-sm font-bold text-[#071d3b] hover:bg-[#e4a744]">
                    Modifier
                </a>
                <a href="index.php?route=admin/users"
                   class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-[#071d3b] hover:border-[#f1b456]">
                    Retour
                </a>
            </div>
        </div>

        <!-- Badge de rôle -->
        <div class="mt-6">
            <?php
            if ($utilisateur['role'] === 'etudiant') {
                $badgeClass = 'bg-sky-100 text-sky-800 border-sky-200';
                $badgeLabel = 'Étudiant';
            } elseif ($utilisateur['role'] === 'etablissement') {
                $badgeClass = 'bg-emerald-100 text-emerald-800 border-emerald-200';
                $badgeLabel = 'Établissement';
            } else {
                $badgeClass = 'bg-violet-100 text-violet-800 border-violet-200';
                $badgeLabel = 'Administrateur';
            }
            ?>
            <span class="inline-block rounded-lg border px-3 py-1 text-sm font-bold <?= $badgeClass ?>">
                <?= $badgeLabel ?>
            </span>
        </div>

        <!-- Informations générales -->
        <div class="mt-6 grid gap-6 sm:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-[#071d3b]">Informations du compte</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="font-semibold text-slate-600">Email :</dt>
                        <dd class="text-slate-800"><?= htmlspecialchars($utilisateur['email']) ?></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="font-semibold text-slate-600">Rôle :</dt>
                        <dd class="text-slate-800"><?= $badgeLabel ?></dd>
                    </div>
                </dl>
            </div>

            <?php if ($utilisateur['role'] === 'etudiant'): ?>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-[#071d3b]">Profil étudiant</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="font-semibold text-slate-600">Nom :</dt>
                        <dd class="text-slate-800"><?= htmlspecialchars($utilisateur['etudiant_nom'] ?? '—') ?></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="font-semibold text-slate-600">Prénom :</dt>
                        <dd class="text-slate-800"><?= htmlspecialchars($utilisateur['etudiant_prenom'] ?? '—') ?></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="font-semibold text-slate-600">Téléphone :</dt>
                        <dd class="text-slate-800"><?= htmlspecialchars($utilisateur['etudiant_telephone'] ?? '—') ?></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="font-semibold text-slate-600">Date de naissance :</dt>
                        <dd class="text-slate-800">
                            <?= $utilisateur['date_de_naissance']
                                ? date('d/m/Y', strtotime($utilisateur['date_de_naissance']))
                                : '—' ?>
                        </dd>
                    </div>
                </dl>
            </div>

            <?php elseif ($utilisateur['role'] === 'etablissement'): ?>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-[#071d3b]">Profil établissement</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="font-semibold text-slate-600">Nom :</dt>
                        <dd class="text-slate-800"><?= htmlspecialchars($utilisateur['etablissement_nom'] ?? '—') ?></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="font-semibold text-slate-600">Type :</dt>
                        <dd class="text-slate-800"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $utilisateur['etablissement_type'] ?? '—'))) ?></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="font-semibold text-slate-600">Site web :</dt>
                        <dd class="text-slate-800">
                            <?php if (!empty($utilisateur['site_web'])): ?>
                                <a href="<?= htmlspecialchars($utilisateur['site_web']) ?>" target="_blank" rel="noopener"
                                   class="text-sky-600 hover:underline">
                                    <?= htmlspecialchars($utilisateur['site_web']) ?>
                                </a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="font-semibold text-slate-600">Ville :</dt>
                        <dd class="text-slate-800"><?= htmlspecialchars($utilisateur['ville'] ?? '—') ?></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="font-semibold text-slate-600">Région :</dt>
                        <dd class="text-slate-800"><?= htmlspecialchars($utilisateur['region'] ?? '—') ?></dd>
                    </div>
                </dl>
            </div>
            <?php endif; ?>
        </div>

        <!-- Actions -->
        <?php if ($utilisateur['role'] !== 'admin'): ?>
        <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 p-5">
            <h2 class="mb-3 text-sm font-bold text-rose-800">Zone dangereuse</h2>
            <p class="mb-4 text-xs text-rose-700">
                La suppression de ce compte est irréversible. Toutes les données associées (candidatures, profil, formations) seront supprimées.
            </p>
            <a href="index.php?route=admin/user/delete&id=<?= $utilisateur['id_utilisateur'] ?>"
               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce compte ?\n\nCette action est IRRÉVERSIBLE.')"
               class="inline-block rounded-lg border border-rose-300 bg-white px-4 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-100">
                Supprimer le compte
            </a>
        </div>
        <?php endif; ?>

    </section>
</main>
