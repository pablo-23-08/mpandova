<?php
// $utilisateurs : tableau retourné par Admin::findAllUsers()
// Chaque ligne contient : id_utilisateur, email, role,
//   etudiant_nom, etudiant_prenom (si étudiant),
//   etablissement_nom (si établissement)
?>
<main class="mx-auto w-full max-w-6xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">

        <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-[#071d3b] sm:text-3xl">
                    Gestion des utilisateurs
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    <?= count($utilisateurs) ?> compte(s) enregistré(s) sur la plateforme
                </p>
            </div>
            <a href="index.php?route=admin/home"
               class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-[#071d3b] hover:border-[#f1b456]">
                Retour
            </a>
        </div>

        <?php if (empty($utilisateurs)): ?>

            <div class="py-16 text-center text-slate-600">
                <p>Aucun utilisateur enregistré.</p>
            </div>

        <?php else: ?>

            <div class="mt-6 space-y-3">
                <?php foreach ($utilisateurs as $u): ?>

                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#f1b456]/60">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                            <!-- Informations du compte -->
                            <div>
                                <div class="flex flex-wrap items-center gap-2">

                                    <!-- Badge de rôle -->
                                    <?php if ($u['role'] === 'etudiant'): ?>
                                        <span class="rounded-lg border border-sky-200 bg-sky-50 px-2 py-1 text-xs font-bold text-sky-800">
                                            Étudiant
                                        </span>
                                    <?php else: ?>
                                        <span class="rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-800">
                                            Établissement
                                        </span>
                                    <?php endif; ?>

                                    <!-- Nom de la personne ou de l'école -->
                                    <h3 class="text-base font-bold text-[#071d3b]">
                                        <?php if ($u['role'] === 'etudiant'): ?>
                                            <?= htmlspecialchars($u['etudiant_prenom'] . ' ' . $u['etudiant_nom']) ?>
                                        <?php else: ?>
                                            <?= htmlspecialchars($u['etablissement_nom'] ?? '—') ?>
                                        <?php endif; ?>
                                    </h3>

                                </div>

                                <p class="mt-1 text-sm text-slate-500">
                                    <?= htmlspecialchars($u['email']) ?>
                                </p>

                                <p class="text-xs text-slate-400">
                                    ID #<?= $u['id_utilisateur'] ?>
                                </p>
                            </div>

                            <!-- Bouton de suppression -->
                            <div class="flex shrink-0 gap-2">
                                <a href="index.php?route=admin/user/delete&id=<?= $u['id_utilisateur'] ?>"
                                   onclick="return confirm('Supprimer ce compte ? Toutes les données associées (candidatures, offres de filières, profil) seront aussi supprimées. Cette action est irréversible.')"
                                   class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-100">
                                    Supprimer
                                </a>
                            </div>

                        </div>
                    </article>

                <?php endforeach; ?>
            </div>

        <?php endif; ?>
    </section>
</main>