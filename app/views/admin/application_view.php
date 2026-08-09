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
                    ID #<?= $candidature['id_candidature'] ?> &middot;
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

            <!-- Etudiant -->
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-[#071d3b]">Etudiant</h2>
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
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-[#071d3b]">Formation demandée</h2>
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
            </div>

            <!-- Etablissement -->
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-[#071d3b]">Etablissement</h2>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="font-semibold text-slate-600">Nom :</dt>
                        <dd class="mt-1 text-slate-800"><?= htmlspecialchars($candidature['etablissement_nom']) ?></dd>
                    </div>
                    <?php if (!empty($candidature['etablissement_ville'])): ?>
                    <div>
                        <dt class="font-semibold text-slate-600">Ville :</dt>
                        <dd class="mt-1 text-slate-800"><?= htmlspecialchars($candidature['etablissement_ville']) ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($candidature['etablissement_email'])): ?>
                    <div>
                        <dt class="font-semibold text-slate-600">Email :</dt>
                        <dd class="mt-1 text-slate-800"><?= htmlspecialchars($candidature['etablissement_email']) ?></dd>
                    </div>
                    <?php endif; ?>
                </dl>
            </div>

            <!-- Statut et actions -->
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-[#071d3b]">Traitement</h2>
                <p class="mb-4 text-sm text-slate-600">
                    Statut actuel :
                    <span class="ml-1 inline-block rounded-lg border px-2 py-0.5 text-xs font-semibold <?= $badgeClass ?>">
                        <?= $badgeLabel ?>
                    </span>
                </p>
                <form method="POST" action="index.php?route=admin/application/update" class="space-y-3">
                    <input type="hidden" name="id_candidature" value="<?= $candidature['id_candidature'] ?>">
                    <div>
                        <label for="statut" class="mb-2 block text-sm font-semibold text-[#071d3b]">
                            Changer le statut
                        </label>
                        <select
                            id="statut"
                            name="statut"
                            class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30"
                        >
                            <option value="en_attente" <?= $candidature['statut'] === 'en_attente' ? 'selected' : '' ?>>En attente</option>
                            <option value="acceptee"   <?= $candidature['statut'] === 'acceptee'   ? 'selected' : '' ?>>Acceptée</option>
                            <option value="refusee"    <?= $candidature['statut'] === 'refusee'    ? 'selected' : '' ?>>Refusée</option>
                            <option value="annulee"    <?= $candidature['statut'] === 'annulee'    ? 'selected' : '' ?>>Annulée</option>
                        </select>
                    </div>
                    <button
                        type="submit"
                        class="rounded-lg bg-[#f1b456] px-5 py-2.5 text-sm font-bold text-[#071d3b] hover:bg-[#e4a744]"
                    >
                        Enregistrer le statut
                    </button>
                </form>
            </div>

        </div>

        <!-- Message de motivation -->
        <?php if (!empty($candidature['message_motivation'])): ?>
        <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-5">
            <h2 class="mb-3 text-sm font-bold uppercase tracking-wide text-[#071d3b]">Message de motivation</h2>
            <p class="text-sm leading-relaxed text-slate-700">
                <?= nl2br(htmlspecialchars($candidature['message_motivation'])) ?>
            </p>
        </div>
        <?php endif; ?>

    </section>
</main>
