<?php
// $etablissements : tableau retourné par Admin::findAllInstitutions()
// Champs disponibles : id_etablissement, nom, type, site_web,
//                      ville, region, email, nb_offres
?>
<main class="mx-auto w-full max-w-6xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">

        <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-[#071d3b] sm:text-3xl">
                    Établissements
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    <?= count($etablissements) ?> établissement(s) sur la plateforme
                </p>
            </div>
            <a href="index.php?route=admin/home"
               class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-[#071d3b] hover:border-[#f1b456]">
                Retour
            </a>
        </div>

        <?php if (empty($etablissements)): ?>

            <div class="py-16 text-center text-slate-600">
                <p>Aucun établissement enregistré.</p>
            </div>

        <?php else: ?>

            <div class="mt-6 space-y-3">
                <?php foreach ($etablissements as $e): ?>

                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#f1b456]/60">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                            <div>
                                <h3 class="text-base font-bold text-[#071d3b]">
                                    <?= htmlspecialchars($e['nom']) ?>
                                </h3>

                                <p class="mt-1 text-sm text-slate-600">
                                    <!-- ucwords + str_replace : 'universite_publique' → 'Universite Publique' -->
                                    <?= htmlspecialchars(ucwords(str_replace('_', ' ', $e['type']))) ?>
                                    <?php if ($e['ville']): ?>
                                        · <?= htmlspecialchars($e['ville']) ?>
                                    <?php endif; ?>
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    <?= htmlspecialchars($e['email']) ?>
                                    · <?= $e['nb_offres'] ?> offre(s) publiée(s)
                                </p>
                            </div>

                            <?php if (!empty($e['site_web'])): ?>
                                <a href="<?= htmlspecialchars($e['site_web']) ?>"
                                   target="_blank"
                                   rel="noopener"
                                   class="shrink-0 rounded-lg border border-sky-200 bg-sky-50 px-4 py-2 text-sm font-semibold text-sky-800 hover:bg-sky-100">
                                    Visiter le site
                                </a>
                            <?php endif; ?>

                        </div>
                    </article>

                <?php endforeach; ?>
            </div>

        <?php endif; ?>
    </section>
</main>