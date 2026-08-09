<?php
// $offre       : détails complets de l'offre (filiere + etablissement + localisation + condition_acces)
// $debouches   : débouchés professionnels liés à la filière (tables debouche + mener)
// $candidature : candidature existante de l'étudiant pour cette offre, ou false si aucune
?>
<main class="mx-auto w-full max-w-4xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">
        <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-[#f1b456]">Fiche filière</p>
                <h1 class="mt-1 text-2xl font-extrabold text-[#071d3b] sm:text-3xl">
                    <?= htmlspecialchars($offre['filiere_nom']) ?>
                </h1>
                <p class="mt-1 text-sm font-medium text-slate-600">
                    <?= htmlspecialchars($offre['etablissement_nom']) ?>
                    <?php if (!empty($offre['ville'])): ?>
                        - <?= htmlspecialchars($offre['ville']) ?>
                    <?php endif; ?>
                </p>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-2 sm:gap-3">
                <a href="javascript:history.back()"
                   class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-[#071d3b] hover:border-[#f1b456]">
                    Retour
                </a>
            </div>
        </div>

        <?php if (!empty($offre['filiere_description'])): ?>
            <p class="mt-6 text-sm leading-relaxed text-slate-700">
                <?= nl2br(htmlspecialchars($offre['filiere_description'])) ?>
            </p>
        <?php endif; ?>

        <div class="mt-6 grid gap-4 sm:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <h2 class="text-sm font-bold uppercase tracking-wide text-[#071d3b]">Établissement</h2>
                <p class="mt-2 text-sm text-slate-700"><?= htmlspecialchars($offre['etablissement_nom']) ?></p>
                <p class="text-xs text-slate-500"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $offre['etablissement_type']))) ?></p>

                <?php if (!empty($offre['adresse']) || !empty($offre['ville']) || !empty($offre['region'])): ?>
                    <p class="mt-2 text-xs text-slate-500">
                        <?= htmlspecialchars(trim(
                            (!empty($offre['adresse']) ? $offre['adresse'] . ', ' : '') .
                            ($offre['ville'] ?? '') .
                            (!empty($offre['region']) ? ' (' . $offre['region'] . ')' : '')
                        )) ?>
                    </p>
                <?php endif; ?>

                <?php if (!empty($offre['site_web'])): ?>
                    <a href="<?= htmlspecialchars($offre['site_web']) ?>" target="_blank" rel="noopener" class="mt-2 inline-block text-xs font-semibold text-sky-700 hover:underline">
                        Visiter le site web
                    </a>
                <?php endif; ?>

                <?php if (!empty($offre['etablissement_description'])): ?>
                    <p class="mt-3 text-xs leading-relaxed text-slate-600">
                        <?= nl2br(htmlspecialchars($offre['etablissement_description'])) ?>
                    </p>
                <?php endif; ?>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <h2 class="text-sm font-bold uppercase tracking-wide text-[#071d3b]">Modalités de la formation</h2>
                <div class="mt-2 flex flex-wrap gap-2 text-xs text-slate-600">
                    <span class="rounded-full bg-white px-3 py-1 shadow-sm">Frais : <?= number_format($offre['frais_scolarite'], 0, ',', ' ') ?> Ar/an</span>
                    <span class="rounded-full bg-white px-3 py-1 shadow-sm">Places : <?= $offre['place_disponible'] ?></span>
                    <?php if (!empty($offre['duree_formation'])): ?>
                        <span class="rounded-full bg-white px-3 py-1 shadow-sm">Durée : <?= htmlspecialchars($offre['duree_formation']) ?></span>
                    <?php endif; ?>
                </div>

                <h3 class="mt-4 text-xs font-bold uppercase tracking-wide text-slate-500">Conditions d'accès</h3>
                <?php if (!empty($offre['serie_bac']) || !empty($offre['moyenne_bac']) || !empty($offre['age_max']) || !empty($offre['diplome_requis'])): ?>
                    <ul class="mt-2 space-y-1 text-xs text-slate-600">
                        <?php if (!empty($offre['serie_bac'])): ?>
                            <li>Série de bac requise : <strong><?= htmlspecialchars($offre['serie_bac']) ?></strong></li>
                        <?php endif; ?>
                        <?php if (!empty($offre['moyenne_bac'])): ?>
                            <li>Moyenne minimale : <strong><?= htmlspecialchars($offre['moyenne_bac']) ?>/20</strong></li>
                        <?php endif; ?>
                        <?php if (!empty($offre['age_max'])): ?>
                            <li>Âge maximum : <strong><?= (int) $offre['age_max'] ?> ans</strong></li>
                        <?php endif; ?>
                        <?php if (!empty($offre['diplome_requis'])): ?>
                            <li>Diplôme requis : <strong><?= htmlspecialchars($offre['diplome_requis']) ?></strong></li>
                        <?php endif; ?>
                    </ul>
                <?php else: ?>
                    <p class="mt-2 text-xs font-semibold text-emerald-700">Ouvert à tous, sans condition particulière.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="text-sm font-bold uppercase tracking-wide text-[#071d3b]">Débouchés professionnels</h2>
            <?php if (empty($debouches)): ?>
                <p class="mt-2 text-sm text-slate-500">Aucun débouché renseigné pour cette filière pour le moment.</p>
            <?php else: ?>
                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                    <?php foreach ($debouches as $d): ?>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm font-semibold text-[#071d3b]"><?= htmlspecialchars($d['nom']) ?></p>
                            <p class="text-xs font-medium text-[#8a5a10]">Niveau : <?= htmlspecialchars($d['niveau_etude']) ?></p>
                            <?php if (!empty($d['description'])): ?>
                                <p class="mt-1 text-xs text-slate-600"><?= htmlspecialchars($d['description']) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-6 rounded-2xl border border-[#f1b456]/40 bg-[#f1b456]/10 p-5">
            <?php if ($candidature): ?>
                <?php
                    $badges = [
                        'en_attente' => ['bg-amber-100 text-amber-800 border-amber-200', 'En attente'],
                        'acceptee'   => ['bg-emerald-100 text-emerald-800 border-emerald-200', 'Acceptée'],
                        'refusee'    => ['bg-rose-100 text-rose-800 border-rose-200', 'Refusée'],
                        'annulee'    => ['bg-slate-100 text-slate-700 border-slate-200', 'Annulée'],
                    ];
                    [$badgeClass, $badgeLabel] = $badges[$candidature['statut']] ?? ['bg-slate-100 text-slate-700 border-slate-200', $candidature['statut']];
                ?>
                <h2 class="text-sm font-bold uppercase tracking-wide text-[#071d3b]">Votre candidature</h2>
                <span class="mt-2 inline-block rounded-full border px-3 py-1 text-xs font-semibold <?= $badgeClass ?>">
                    <?= $badgeLabel ?>
                </span>
        

                <?php if (!empty($candidature['message'])): ?>
                    <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                            Votre message envoyé à l'établissement
                        </p>

                        <p class="mt-2 text-sm leading-relaxed text-slate-700">
                            <?= nl2br(htmlspecialchars($candidature['message'])) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <div class="mt-4 flex w-full justify-end">
                    <a href="index.php?route=student/applications"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-[#071d3b] hover:border-[#f1b456]">
                        Voir toutes mes candidatures
                    </a>
                </div>
            <?php else: ?>
                <h2 class="text-sm font-bold uppercase tracking-wide text-[#071d3b]">Postuler à cette filière</h2>
                <p class="mt-1 text-xs text-slate-600">Ajoutez un message pour vous présenter à l'établissement (optionnel).</p>

                <form method="POST" action="index.php?route=student/application/submit" class="mt-3 space-y-3">
                    <input type="hidden" name="id_offre_filiere" value="<?= $offre['id_offre_filiere'] ?>">
                    <textarea
                        name="message"
                        rows="4"
                        maxlength="1000"
                        placeholder="Ex : Je suis très motivé(e) par cette formation car..."
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm text-slate-700 outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30"
                    ></textarea>
                    <button type="submit" class="rounded-lg bg-[#f1b456] px-6 py-3 text-sm font-bold text-[#071d3b] hover:bg-[#e4a744]">
                        Envoyer ma candidature
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </section>
</main>
