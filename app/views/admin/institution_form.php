<?php
// $etablissement : données de l'établissement à modifier
?>
<main class="mx-auto w-full max-w-2xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">

        <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-[#071d3b] sm:text-3xl">
                    Modifier l'établissement
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    <?= htmlspecialchars($etablissement['nom']) ?>
                </p>
            </div>
            <a href="index.php?route=admin/institution/view&id=<?= $etablissement['id_etablissement'] ?>"
               class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-[#071d3b] hover:border-[#f1b456]">
                Retour
            </a>
        </div>

        <form method="POST" action="index.php?route=admin/institution/edit" novalidate class="mt-6 space-y-5">
            <input type="hidden" name="id_etablissement" value="<?= $etablissement['id_etablissement'] ?>">

            <!-- Nom -->
            <div>
                <label for="nom" class="mb-2 block text-sm font-semibold text-[#071d3b]">
                    Nom de l'établissement <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    id="nom"
                    name="nom"
                    required
                    maxlength="150"
                    value="<?= htmlspecialchars($etablissement['nom']) ?>"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30"
                >
            </div>

            <!-- Type -->
            <div>
                <label for="type" class="mb-2 block text-sm font-semibold text-[#071d3b]">
                    Type <span class="text-rose-500">*</span>
                </label>
                <select
                    id="type"
                    name="type"
                    required
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30"
                >
                    <?php
                    $types = [
                        'universite_publique' => 'Université publique',
                        'universite_privee'   => 'Université privée',
                        'grande_ecole'        => 'Grande école',
                        'institut'            => 'Institut',
                        'autre'               => 'Autre',
                    ];
                    foreach ($types as $val => $label):
                    ?>
                        <option value="<?= $val ?>" <?= $etablissement['type'] === $val ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Site web -->
            <div>
                <label for="site_web" class="mb-2 block text-sm font-semibold text-[#071d3b]">
                    Site web <span class="text-xs font-normal text-slate-400">(optionnel)</span>
                </label>
                <input
                    type="url"
                    id="site_web"
                    name="site_web"
                    value="<?= htmlspecialchars($etablissement['site_web'] ?? '') ?>"
                    placeholder="https://..."
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30"
                >
            </div>

            <!-- Localisation -->
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="ville" class="mb-2 block text-sm font-semibold text-[#071d3b]">Ville</label>
                    <input
                        type="text"
                        id="ville"
                        name="ville"
                        maxlength="100"
                        value="<?= htmlspecialchars($etablissement['ville'] ?? '') ?>"
                        placeholder="Ex : Antananarivo"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30"
                    >
                </div>
                <div>
                    <label for="region" class="mb-2 block text-sm font-semibold text-[#071d3b]">Région</label>
                    <input
                        type="text"
                        id="region"
                        name="region"
                        maxlength="100"
                        value="<?= htmlspecialchars($etablissement['region'] ?? '') ?>"
                        placeholder="Ex : Analamanga"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30"
                    >
                </div>
            </div>

            <div>
                <label for="adresse" class="mb-2 block text-sm font-semibold text-[#071d3b]">Adresse complète</label>
                <input
                    type="text"
                    id="adresse"
                    name="adresse"
                    maxlength="255"
                    value="<?= htmlspecialchars($etablissement['adresse'] ?? '') ?>"
                    placeholder="Ex : Lot II J 165, Ambohijatovo"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30"
                >
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="mb-2 block text-sm font-semibold text-[#071d3b]">
                    Description <span class="text-xs font-normal text-slate-400">(optionnel)</span>
                </label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    placeholder="Présentation de l'établissement…"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30"
                ><?= htmlspecialchars($etablissement['description'] ?? '') ?></textarea>
            </div>

            <div class="flex flex-wrap gap-3 pt-2">
                <button
                    type="submit"
                    class="rounded-lg bg-[#f1b456] px-6 py-3 text-sm font-bold text-[#071d3b] hover:bg-[#e4a744]"
                >
                    Enregistrer les modifications
                </button>
                <a href="index.php?route=admin/institution/view&id=<?= $etablissement['id_etablissement'] ?>"
                   class="rounded-lg border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 hover:border-[#f1b456]">
                    Annuler
                </a>
            </div>
        </form>

    </section>
</main>
