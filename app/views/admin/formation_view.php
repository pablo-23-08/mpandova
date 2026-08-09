<?php
// $offre     : données complètes de la formation
// $debouches : débouchés associés à la filière
?>
<main class="mx-auto w-full max-w-4xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">

        <!-- En-tête -->
        <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-[#071d3b] sm:text-3xl">
                    <?= htmlspecialchars($offre['filiere_nom']) ?>
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    <?= htmlspecialchars($offre['etablissement_nom']) ?>
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="index.php?route=admin/formation/edit&id=<?= $offre['id_offre_filiere'] ?>"
                   class="rounded-lg bg-[#f1b456] px-4 py-2 text-sm font-bold text-[#071d3b] hover:bg-[#e4a744]">
                    Modifier
                </a>
                <a href="index.php?route=admin/formations"
                   class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-[#071d3b] hover:border-[#f1b456]">
                    Retour
                </a>
            </div>
        </div>

        <!-- Compteurs -->
        <div class="mt-6 grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm">
                <p class="text-2xl font-extrabold text-[#071d3b]"><?= $offre['nb_candidatures'] ?></p>
                <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-500">Candidatures</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm">
                <p class="text-2xl font-extrabold text-[#071d3b]"><?= $offre['place_disponible'] ?? '—' ?></p>
                <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-500">Places disponibles</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm">
                <p class="text-2xl font-extrabold text-[#071d3b]">
                    <?= $offre['frais_scolarite'] > 0 ? number_format($offre['frais_scolarite'], 0, ',', ' ') . ' Ar' : 'Gratuit' ?>
                </p>
                <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-500">Frais de scolarité/an</p>
            </div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">

            <!-- Infos de la formation -->
            <div class="space-y-6">

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-[#071d3b]">Formation</h2>
                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="font-semibold text-slate-600">Filière :</dt>
                            <dd class="mt-1 text-slate-800"><?= htmlspecialchars($offre['filiere_nom']) ?></dd>
                        </div>
                        <?php if (!empty($offre['filiere_description'])): ?>
                        <div>
                            <dt class="font-semibold text-slate-600">Description :</dt>
                            <dd class="mt-1 text-xs leading-relaxed text-slate-700"><?= nl2br(htmlspecialchars($offre['filiere_description'])) ?></dd>
                        </div>
                        <?php endif; ?>
                        <div>
                            <dt class="font-semibold text-slate-600">Durée :</dt>
                            <dd class="mt-1 text-slate-800"><?= htmlspecialchars($offre['duree_formation'] ?? '—') ?></dd>
                        </div>
                    </dl>
                </div>

                <!-- Conditions d'admission -->
                <?php if (!empty($offre['id_condition_acces'])): ?>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-[#071d3b]">Conditions d'admission</h2>
                    <dl class="space-y-3 text-sm">
                        <?php if (!empty($offre['diplome_requis'])): ?>
                        <div>
                            <dt class="font-semibold text-slate-600">Diplôme requis :</dt>
                            <dd class="mt-1 text-slate-800"><?= htmlspecialchars($offre['diplome_requis']) ?></dd>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($offre['serie_bac'])): ?>
                        <div>
                            <dt class="font-semibold text-slate-600">Série BAC :</dt>
                            <dd class="mt-1 text-slate-800"><?= htmlspecialchars($offre['serie_bac']) ?></dd>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($offre['moyenne_bac'])): ?>
                        <div>
                            <dt class="font-semibold text-slate-600">Moyenne BAC minimale :</dt>
                            <dd class="mt-1 text-slate-800"><?= $offre['moyenne_bac'] ?>/20</dd>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($offre['age_max'])): ?>
                        <div>
                            <dt class="font-semibold text-slate-600">Âge maximum :</dt>
                            <dd class="mt-1 text-slate-800"><?= $offre['age_max'] ?> ans</dd>
                        </div>
                        <?php endif; ?>
                    </dl>
                </div>
                <?php endif; ?>

            </div>

            <div class="space-y-6">

                <!-- Établissement -->
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-[#071d3b]">Établissement</h2>
                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="font-semibold text-slate-600">Nom :</dt>
                            <dd class="mt-1 text-slate-800"><?= htmlspecialchars($offre['etablissement_nom']) ?></dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-slate-600">Type :</dt>
                            <dd class="mt-1 text-slate-800"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $offre['etablissement_type']))) ?></dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-slate-600">Email :</dt>
                            <dd class="mt-1 text-slate-800"><?= htmlspecialchars($offre['etablissement_email']) ?></dd>
                        </div>
                    </dl>
                    <div class="mt-4 border-t border-slate-200 pt-4 flex gap-2">
                        <a href="index.php?route=admin/institution/view&id=<?= $offre['id_etablissement'] ?>"
                           class="text-xs font-semibold text-[#071d3b] hover:text-[#f1b456]">
                            → Voir l'établissement
                        </a>
                        <span class="text-slate-300">|</span>
                        <a href="index.php?route=admin/applications&offre=<?= $offre['id_offre_filiere'] ?>"
                           class="text-xs font-semibold text-[#071d3b] hover:text-[#f1b456]">
                            → Voir les candidatures
                        </a>
                    </div>
                </div>

                <!-- Débouchés -->
                <?php if (!empty($debouches)): ?>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-[#071d3b]">
                        Débouchés (<?= count($debouches) ?>)
                    </h2>
                    <ul class="space-y-2">
                        <?php foreach ($debouches as $d): ?>
                            <li class="flex items-start gap-2 text-sm">
                                <span class="mt-0.5 text-[#f1b456]">▸</span>
                                <div>
                                    <span class="font-medium text-[#071d3b]"><?= htmlspecialchars($d['nom']) ?></span>
                                    <?php if (!empty($d['niveau_etude'])): ?>
                                        <span class="text-xs text-slate-400"> · <?= htmlspecialchars($d['niveau_etude']) ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($d['description'])): ?>
                                        <p class="text-xs text-slate-500"><?= htmlspecialchars($d['description']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- Zone dangereuse -->
        <div class="mt-8 rounded-2xl border border-rose-200 bg-rose-50 p-5">
            <h2 class="mb-3 text-sm font-bold text-rose-800">Zone dangereuse</h2>
            <p class="mb-4 text-xs text-rose-700">
                La suppression de cette formation entraîne la suppression de toutes les candidatures associées. Cette action est irréversible.
            </p>
            <a href="index.php?route=admin/formation/delete&id=<?= $offre['id_offre_filiere'] ?>"
               onclick="return confirm('Supprimer la formation « <?= addslashes(htmlspecialchars($offre['filiere_nom'])) ?> » ?\n\nToutes les candidatures associées seront supprimées. Cette action est IRRÉVERSIBLE.')"
               class="inline-block rounded-lg border border-rose-300 bg-white px-4 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-100">
                Supprimer la formation
            </a>
        </div>

    </section>
</main>
