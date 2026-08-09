<?php
// $candidature : données complètes de la candidature

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

$badgeClass = $badgeColors[$candidature['statut']] ?? 'bg-slate-100 text-slate-600 border-slate-200';
$badgeLabel = $badgeLabels[$candidature['statut']] ?? $candidature['statut'];
?>
<main class="mx-auto w-full max-w-4xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">

        <!-- En-tête -->
        <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-[#071d3b] sm:text-3xl">
                    Détail de la candidature
                </h1>
                <p class="mt-1 text-xs text-slate-400">
                    ID #<?= $candidature['id_candidature'] ?> ·
                    Soumise le <?= date('d/m/Y à H:i', strtotime($candidature['date_candidature'])) ?>
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <!-- Statut -->
                <span class="inline-block rounded-lg border px-3 py-1 text-sm font-bold <?= $badgeClass ?>">
                    <?= $badgeLabel ?>
                </span>
                <a href="index.php?route=admin/applications"
                   class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-[#071d3b] hover:border-[#f1b456]">
                    Retour
                </a>
            </div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">

            <!-- Étudiant -->
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-[#071d3b]">
                    👤 Étudiant
                </h2>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="font-semibold text-slate-600">Nom complet :</dt>
                        <dd class="mt-1 text-slate-800">
                            <?= htmlspecialchars($candidature['etudiant_prenom'] . ' ' . $candidature['etudiant_nom']) ?>
                        </dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-slate-600">Email :</dt>
                        <dd class="mt-1 text-slate-800"><?= htmlspecialchars($candidature['etudiant_email']) ?></dd>
                    </div>
                    <?php if (!empty($candidature['etudiant_telephone'])): ?>
                    <div>
                        <dt class="font-semibold text-slate-600">Téléphone :</dt>
                        <dd class="mt-1 text-slate-800"><?= htmlspecialchars($candidature['etudiant_telephone']) ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($candidature['date_de_naissance'])): ?>
                    <div>
                        <dt class="font-semibold text-slate-600">Date de naissance :</dt>
                        <dd class="mt-1 text-slate-800"><?= date('d/m/Y', strtotime($candidature['date_de_naissance'])) ?></dd>
                    </div>
                    <?php endif; ?>
                </dl>
            </div>

            <!-- Formation demandée -->
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-[#071d3b]">
                    📚 Formation demandée
                </h2>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="font-semibold text-slate-600">Filière :</dt>
                        <dd class="mt-1 text-slate-800"><?= htmlspecialchars($candidature['filiere_nom']) ?></dd>
                    </div>
                    <?php if (!empty($candidature['filiere_description'])): ?>
                    <div>
                        <dt class="font-semibold text-slate-600">Description :</dt>
                        <dd class="mt-1 text-xs leading-relaxed text-slate-700"><?= nl2br(htmlspecialchars($candidature['filiere_description'])) ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($candidature['duree_formation'])): ?>
                    <div>
                        <dt class="font-semibold text-slate-600">Durée :</dt>
                        <dd class="mt-1 text-slate-800"><?= htmlspecialchars($candidature['duree_formation']) ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if ($candidature['frais_scolarite'] > 0): ?>
                    <div>
                        <dt class="font-semibold text-slate-600">Frais de scolarité :</dt>
                        <dd class="mt-1 text-slate-800"><?= number_format($candidature['frais_scolarite'], 0, ',', ' ') ?> Ar/an</dd>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($candidature['diplome_requis'])): ?>
                    <div>
                        <dt class="font-semibold text-slate-600">Diplôme requis :</dt>
                        <dd class="mt-1 text-slate-800"><?= htmlspecialchars($candidature['diplome_requis']) ?></dd>
                    </div>
                    <?php endif; ?>
                </dl>
                <div class="mt-4 border-t border-slate-200 pt-3">
                    <a href="index.php?route=admin/formation/view&id=<?= $candidature['id_offre_filiere'] ?>"
                       class="text-xs font-semibold text-[#071d3b] hover:text-[#f1b456]">
                        → Voir la formation
                    </a>
                </div>
            </div>

            <!-- Établissement -->
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-[#071d3b]">
                    🏛️ Établissement
                </h2>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="font-semibold text-slate-600">Nom :</dt>
                        <dd class="mt-1 text-slate-800"><?= htmlspecialchars($candidature['etablissement_nom']) ?></dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-slate-600">Type :</dt>
                        <dd class="mt-1 text-slate-800"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $candidature['etablissement_type']))) ?></dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-slate-600">Email :</dt>
                        <dd class="mt-1 text-slate-800"><?= htmlspecialchars($candidature['etablissement_email']) ?></dd>
                    </div>
                </dl>
                <div class="mt-4 border-t border-slate-200 pt-3">
                    <a href="index.php?route=admin/institution/view&id=<?= $candidature['id_etablissement'] ?>"
                       class="text-xs font-semibold text-[#071d3b] hover:text-[#f1b456]">
                        → Voir l'établissement
                    </a>
                </div>
            </div>

            <!-- Statut et dates -->
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-[#071d3b]">
                    📋 Statut & Dates
                </h2>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="font-semibold text-slate-600">Statut actuel :</dt>
                        <dd class="mt-1">
                            <span class="inline-block rounded-lg border px-3 py-1 text-xs font-bold <?= $badgeClass ?>">
                                <?= $badgeLabel ?>
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-slate-600">Date de candidature :</dt>
                        <dd class="mt-1 text-slate-800">
                            <?= date('d/m/Y à H:i', strtotime($candidature['date_candidature'])) ?>
                        </dd>
                    </div>
                    <?php if (!empty($candidature['date_traitement'])): ?>
                    <div>
                        <dt class="font-semibold text-slate-600">Date de traitement :</dt>
                        <dd class="mt-1 text-slate-800">
                            <?= date('d/m/Y à H:i', strtotime($candidature['date_traitement'])) ?>
                        </dd>
                    </div>
                    <?php endif; ?>
                </dl>
                <div class="mt-4 rounded-xl border border-sky-200 bg-sky-50 p-3 text-xs text-sky-800">
                    <strong>ℹ️ Rôle de l'admin :</strong> L'administrateur supervise les candidatures.
                    Le traitement (acceptation/refus) est de la responsabilité de l'établissement.
                </div>
            </div>

        </div>

        <!-- Message de l'étudiant -->
        <?php if (!empty($candidature['message'])): ?>
        <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-5">
            <h2 class="mb-3 text-sm font-bold uppercase tracking-wide text-[#071d3b]">
                💬 Message de l'étudiant
            </h2>
            <div class="rounded-xl border border-slate-200 bg-white p-4 text-sm leading-relaxed text-slate-700">
                <?= nl2br(htmlspecialchars($candidature['message'])) ?>
            </div>
        </div>
        <?php else: ?>
        <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-5">
            <h2 class="mb-3 text-sm font-bold uppercase tracking-wide text-[#071d3b]">
                💬 Message de l'étudiant
            </h2>
            <p class="text-sm text-slate-500 italic">Aucun message joint à cette candidature.</p>
        </div>
        <?php endif; ?>

    </section>
</main>
