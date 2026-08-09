<?php
// $offre       : l'offre de filière concernée (offre_filiere + filiere)
// $debouches   : liste des débouchés déjà liés à cette filière (tables debouche + mener)
// $modeEdition : null (mode ajout) ou tableau du débouché en cours de modification

$formNom         = htmlspecialchars($modeEdition['nom'] ?? '');
$formDescription = htmlspecialchars($modeEdition['description'] ?? '');
$formNiveau      = htmlspecialchars($modeEdition['niveau_etude'] ?? '');

$actionUrl = $modeEdition
    ? "index.php?route=institution/program/outlets/edit&id={$offre['id_offre_filiere']}&id_debouche={$modeEdition['id_debouche']}"
    : "index.php?route=institution/program/outlets/store&id={$offre['id_offre_filiere']}";
?>

<main class="mx-auto w-full max-w-6xl flex-1 px-4 py-10 sm:px-6 lg:px-8">

    <section class="rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">

        <!-- ================================================= -->
        <!-- EN-TÊTE -->
        <!-- ================================================= -->
        <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-[#f1b456]">
                    Débouchés professionnels
                </p>

                <h1 class="mt-1 text-2xl font-extrabold text-[#071d3b] sm:text-3xl">
                    <?= htmlspecialchars($offre['filiere_nom']) ?>
                </h1>
            </div>

            <a href="javascript:history.back()"
               class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-[#071d3b] hover:border-[#f1b456]">
                Retour
            </a>

        </div>


        <!-- ================================================= -->
        <!-- CONTENU : GAUCHE + DROITE -->
        <!-- ================================================= -->
        <div class="mt-8 grid gap-8 lg:grid-cols-2">


            <!-- ================================================= -->
            <!-- GAUCHE : DÉBOUCHÉS -->
            <!-- ================================================= -->
            <div class="min-w-0">

                <!-- Titre -->
                <div class="mb-5 flex items-start justify-between gap-4">

                    <div>
                        <h2 class="text-lg font-bold text-[#071d3b]">
                            Débouchés disponibles
                        </h2>

                        <p class="mt-1 max-w-lg text-sm leading-5 text-slate-500">
                            Les métiers et opportunités professionnelles associés à cette filière.
                        </p>
                    </div>

                    <?php if (!empty($debouches)): ?>

                        <span class="shrink-0 rounded-lg bg-[#f1b456]/15 px-3 py-1 text-xs font-bold text-[#8a5a10]">
                            <?= count($debouches) ?>
                            débouché<?= count($debouches) > 1 ? 's' : '' ?>
                        </span>

                    <?php endif; ?>

                </div>


                <!-- Aucun débouché -->
                <?php if (empty($debouches)): ?>

                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">

                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-lg bg-white text-xl font-bold text-[#f1b456] shadow-sm">
                            +
                        </div>

                        <p class="mt-4 text-sm font-semibold text-[#071d3b]">
                            Aucun débouché professionnel
                        </p>

                        <p class="mx-auto mt-1 max-w-md text-xs leading-5 text-slate-500">
                            Aucun débouché professionnel n'a encore été renseigné pour cette filière.
                            Utilisez le formulaire pour en ajouter un.
                        </p>

                    </div>


                <?php else: ?>


                    <!-- ================================================= -->
                    <!-- CARTES DES DÉBOUCHÉS -->
                    <!-- ================================================= -->
                    <div class="grid gap-4 sm:grid-cols-2">

                        <?php foreach ($debouches as $d): ?>

                            <div class="flex min-w-0 flex-col rounded-2xl border border-slate-200 bg-slate-50 p-5 transition hover:border-[#f1b456]/50 hover:bg-white">

                                <!-- Contenu -->
                                <div class="min-w-0 flex-1">

                                    <h3 class="break-words text-sm font-bold leading-5 text-[#071d3b]">
                                        <?= htmlspecialchars($d['nom']) ?>
                                    </h3>

                                    <div class="mt-3">

                                        <span class="inline-block rounded-lg bg-[#f1b456]/15 px-3 py-1 text-xs font-bold text-[#8a5a10]">
                                            <?= htmlspecialchars($d['niveau_etude']) ?>
                                        </span>

                                    </div>

                                    <?php if (!empty($d['description'])): ?>

                                        <p class="mt-3 break-words text-xs leading-5 text-slate-600">
                                            <?= htmlspecialchars($d['description']) ?>
                                        </p>

                                    <?php endif; ?>

                                </div>


                                <!-- Actions -->
                                <div class="mt-5 flex flex-wrap gap-2 border-t border-slate-200 pt-4">

                                    <a href="index.php?route=institution/program/outlets/edit&id=<?= $offre['id_offre_filiere'] ?>&id_debouche=<?= $d['id_debouche'] ?>"
                                       class="rounded-lg border border-[#f1b456]/50 bg-[#f1b456]/15 px-3 py-1.5 text-xs font-semibold text-[#8a5a10] hover:bg-[#f1b456]/25">
                                        Modifier
                                    </a>

                                    <a href="index.php?route=institution/program/outlets/delete&id=<?= $offre['id_offre_filiere'] ?>&id_debouche=<?= $d['id_debouche'] ?>"
                                       onclick="return confirm('Supprimer ce débouché de la filière ?')"
                                       class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100">
                                        Supprimer
                                    </a>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </div>


            <!-- ================================================= -->
            <!-- DROITE : FORMULAIRE -->
            <!-- ================================================= -->
            <div class="min-w-0">

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">

                    <!-- En-tête formulaire -->
                    <div class="border-b border-slate-200 pb-4">

                        <p class="text-xs font-semibold uppercase tracking-wide text-[#f1b456]">
                            <?= $modeEdition ? 'Modification' : 'Nouveau débouché' ?>
                        </p>

                        <h2 class="mt-1 text-lg font-bold text-[#071d3b]">
                            <?= $modeEdition
                                ? 'Modifier le débouché'
                                : 'Ajouter un débouché'
                            ?>
                        </h2>

                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            <?= $modeEdition
                                ? 'Modifiez les informations du débouché sélectionné.'
                                : 'Ajoutez un métier ou une opportunité professionnelle liée à cette filière.'
                            ?>
                        </p>

                    </div>


                    <!-- Formulaire -->
                    <form method="POST"
                          action="<?= $actionUrl ?>"
                          novalidate
                          class="mt-5 space-y-5">


                        <!-- Nom -->
                        <div>

                            <label for="nom"
                                   class="mb-2 block text-sm font-semibold text-[#071d3b]">
                                Nom du débouché
                            </label>

                            <input type="text"
                                   id="nom"
                                   name="nom"
                                   required
                                   maxlength="150"
                                   value="<?= $formNom ?>"
                                   placeholder="Ex : Ingénieur logiciel"
                                   class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">

                        </div>


                        <!-- Niveau -->
                        <div>

                            <label for="niveau_etude"
                                   class="mb-2 block text-sm font-semibold text-[#071d3b]">
                                Niveau d'étude nécessaire
                            </label>

                            <input type="text"
                                   id="niveau_etude"
                                   name="niveau_etude"
                                   list="niveaux_etude"
                                   required
                                   maxlength="100"
                                   value="<?= $formNiveau ?>"
                                   placeholder="Ex : Bac+3, Bac+5, Doctorat..."
                                   class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">

                            <datalist id="niveaux_etude">
                                <option value="Bac">
                                <option value="Bac+2">
                                <option value="Bac+3">
                                <option value="Bac+4">
                                <option value="Bac+5">
                                <option value="Doctorat">
                            </datalist>

                        </div>


                        <!-- Description -->
                        <div>

                            <label for="description"
                                   class="mb-2 block text-sm font-semibold text-[#071d3b]">
                                Description
                            </label>

                            <textarea id="description"
                                      name="description"
                                      rows="5"
                                      placeholder="Décrire brièvement ce débouché professionnel"
                                      class="w-full resize-none rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30"><?= $formDescription ?></textarea>

                        </div>


                        <!-- Boutons -->
                        <div class="flex gap-2 pt-1">

                            <button type="submit"
                                    class="flex-1 rounded-lg bg-[#f1b456] px-5 py-3 text-sm font-bold text-[#071d3b] hover:bg-[#e4a744]">
                                <?= $modeEdition
                                    ? 'Enregistrer les modifications'
                                    : 'Ajouter'
                                ?>
                            </button>

                            <?php if ($modeEdition): ?>

                                <a href="index.php?route=institution/program/outlets&id=<?= $offre['id_offre_filiere'] ?>"
                                   class="rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#071d3b] hover:border-[#f1b456]">
                                    Annuler
                                </a>

                            <?php endif; ?>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </section>

</main>