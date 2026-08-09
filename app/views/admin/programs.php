<?php
// $filieres    : toutes les filières du catalogue (avec nb_offres)
// $modeEdition : null (formulaire d'ajout) | tableau (filière à modifier)
// $search      : terme de recherche

$formNom         = htmlspecialchars($modeEdition['nom']         ?? '');
$formDescription = htmlspecialchars($modeEdition['description'] ?? '');

$actionUrl = $modeEdition
    ? "index.php?route=admin/program/edit"
    : "index.php?route=admin/program/create";
?>
<main class="mx-auto w-full max-w-6xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">

        <!-- En-tête -->
        <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-[#071d3b] sm:text-3xl">
                    Catalogue des filières
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    <?= count($filieres) ?> filière(s) dans le catalogue de référence
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="index.php?route=admin/formations"
                   class="rounded-lg border border-[#f1b456]/50 bg-[#f1b456]/10 px-4 py-2 text-sm font-semibold text-[#8a5a10] hover:bg-[#f1b456]/25">
                    Voir les formations
                </a>
                <a href="index.php?route=admin/home"
                   class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-[#071d3b] hover:border-[#f1b456]">
                    Retour
                </a>
            </div>
        </div>

        <!-- Barre de recherche -->
        <form method="GET" action="index.php" class="mt-6 flex gap-3">
            <input type="hidden" name="route" value="admin/programs">
            <div class="flex-1">
                <input
                    type="text"
                    name="q"
                    value="<?= htmlspecialchars($search) ?>"
                    placeholder="Rechercher une filière…"
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30"
                >
            </div>
            <button type="submit"
                    class="rounded-lg bg-[#071d3b] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#0f2d54]">
                Rechercher
            </button>
            <?php if ($search): ?>
                <a href="index.php?route=admin/programs"
                   class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm text-slate-600 hover:border-rose-400 hover:text-rose-600">
                    ×
                </a>
            <?php endif; ?>
        </form>

        <div class="mt-8 grid gap-8 lg:grid-cols-2">

            <!-- ── LISTE DES FILIÈRES ── -->
            <div>
                <h2 class="text-base font-bold text-[#071d3b]">Filières existantes</h2>

                <?php if (empty($filieres)): ?>
                    <p class="mt-4 text-sm text-slate-500">
                        Aucune filière dans le catalogue. Commencez par en ajouter une.
                    </p>
                <?php else: ?>
                    <div class="mt-4 space-y-3">
                        <?php foreach ($filieres as $f): ?>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-[#f1b456]/60">
                                <div class="flex items-start justify-between gap-3">

                                    <div class="min-w-0 flex-1">
                                        <h3 class="text-sm font-bold text-[#071d3b]">
                                            <?= htmlspecialchars($f['nom']) ?>
                                        </h3>

                                        <?php if (!empty($f['description'])): ?>
                                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                                <?= htmlspecialchars($f['description']) ?>
                                            </p>
                                        <?php endif; ?>

                                        <div class="mt-1 flex flex-wrap gap-3">
                                            <span class="text-xs text-slate-400">
                                                <?= $f['nb_offres'] ?> offre(s) publiée(s)
                                            </span>
                                            <?php if ($f['nb_debouches'] > 0): ?>
                                                <span class="text-xs text-slate-400">
                                                    · <?= $f['nb_debouches'] ?> débouché(s)
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="flex shrink-0 flex-col gap-1.5">
                                        <a href="index.php?route=admin/programs&edit=<?= $f['id_filiere'] ?><?= $search ? '&q=' . urlencode($search) : '' ?>"
                                           class="rounded-lg border border-[#f1b456]/50 bg-[#f1b456]/15 px-3 py-1 text-xs font-semibold text-[#8a5a10] hover:bg-[#f1b456]/25 text-center">
                                            Modifier
                                        </a>

                                        <a href="index.php?route=admin/program/delete&id=<?= $f['id_filiere'] ?>"
                                           onclick="return confirm('Supprimer la filière « <?= htmlspecialchars(addslashes($f['nom'])) ?> » ?\n\nAttention : les offres et candidatures liées seront aussi supprimées.')"
                                           class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-100 text-center">
                                            Supprimer
                                        </a>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ── FORMULAIRE (ajout OU modification) ── -->
            <div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">

                    <h2 class="text-base font-bold text-[#071d3b]">
                        <?= $modeEdition ? 'Modifier la filière' : 'Ajouter une filière' ?>
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        <?= $modeEdition
                            ? 'Modifiez le nom ou la description de cette filière.'
                            : 'Ajoutez une nouvelle filière au catalogue de référence.' ?>
                    </p>

                    <form method="POST" action="<?= $actionUrl ?>" novalidate class="mt-4 space-y-4">

                        <?php if ($modeEdition): ?>
                            <input type="hidden" name="id_filiere" value="<?= $modeEdition['id_filiere'] ?>">
                        <?php endif; ?>

                        <div>
                            <label for="nom" class="mb-2 block text-sm font-semibold text-[#071d3b]">
                                Nom de la filière <span class="text-rose-500">*</span>
                            </label>
                            <input type="text"
                                   id="nom"
                                   name="nom"
                                   required
                                   maxlength="150"
                                   value="<?= $formNom ?>"
                                   placeholder="Ex : Informatique"
                                   class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30"
                            >
                        </div>

                        <div>
                            <label for="description" class="mb-2 block text-sm font-semibold text-[#071d3b]">
                                Description <span class="text-xs font-normal text-slate-400">(optionnel)</span>
                            </label>
                            <textarea id="description"
                                      name="description"
                                      rows="4"
                                      placeholder="Description de la filière…"
                                      class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30"
                            ><?= $formDescription ?></textarea>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <button type="submit"
                                    class="rounded-lg bg-[#f1b456] px-5 py-2.5 text-sm font-bold text-[#071d3b] hover:bg-[#e4a744]">
                                <?= $modeEdition ? 'Enregistrer' : 'Ajouter la filière' ?>
                            </button>

                            <?php if ($modeEdition): ?>
                                <a href="index.php?route=admin/programs<?= $search ? '?q=' . urlencode($search) : '' ?>"
                                   class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:border-[#f1b456]">
                                    Annuler
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>

                    <?php if ($modeEdition): ?>
                        <div class="mt-6 border-t border-slate-200 pt-4">
                            <p class="text-xs text-slate-500">
                                Édition de : <strong><?= htmlspecialchars($modeEdition['nom']) ?></strong>
                                (ID #<?= $modeEdition['id_filiere'] ?>)
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>
