<?php
// $etablissement est injectée par EtablissementController::profil()
<<<<<<< HEAD
?>
<main class="mx-auto w-full max-w-6xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">
        <div class="mb-8 flex items-center justify-between gap-4">
            <h1 class="text-2xl font-extrabold text-[#071d3b] sm:text-3xl">Profil établissement</h1>
            <a href="index.php?route=etablissement/accueil" class="text-sm font-semibold text-[#071d3b] hover:underline">Retour</a>
        </div>

        <form method="POST" action="index.php?route=etablissement/profil" novalidate class="space-y-8">
            <div>
                <h2 class="text-lg font-bold text-[#071d3b]">Informations générales</h2>
                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="nom" class="mb-2 block text-sm font-semibold text-[#071d3b]">Nom de l'établissement</label>
                        <input value="<?= htmlspecialchars($etablissement['nom'] ?? '') ?>" type="text" id="nom" name="nom" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
                    </div>

                    <div>
                        <label for="type" class="mb-2 block text-sm font-semibold text-[#071d3b]">Type</label>
                        <select id="type" name="type" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
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
                                <option value="<?= $val ?>" <?= ($etablissement['type'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="site_web" class="mb-2 block text-sm font-semibold text-[#071d3b]">Site web</label>
                        <input value="<?= htmlspecialchars($etablissement['site_web'] ?? '') ?>" type="text" id="site_web" name="site_web" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30" placeholder="https://...">
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-lg font-bold text-[#071d3b]">Localisation</h2>
                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="ville" class="mb-2 block text-sm font-semibold text-[#071d3b]">Ville</label>
                        <input value="<?= htmlspecialchars($etablissement['ville'] ?? '') ?>" type="text" id="ville" name="ville" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
                    </div>
                    <div>
                        <label for="adresse" class="mb-2 block text-sm font-semibold text-[#071d3b]">Adresse</label>
                        <input value="<?= htmlspecialchars($etablissement['adresse'] ?? '') ?>" type="text" id="adresse" name="adresse" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-lg font-bold text-[#071d3b]">Modifier le mot de passe</h2>
                <p class="mt-1 text-sm text-slate-500">Laisser vide pour conserver le mot de passe actuel.</p>
                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="password" class="mb-2 block text-sm font-semibold text-[#071d3b]">Nouveau mot de passe</label>
                        <input type="password" id="password" name="password" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
                    </div>
                    <div>
                        <label for="password_confirm" class="mb-2 block text-sm font-semibold text-[#071d3b]">Confirmer le mot de passe</label>
                        <input type="password" id="password_confirm" name="password_confirm" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full rounded-lg bg-[#f1b456] px-5 py-3 text-sm font-bold text-[#071d3b] hover:bg-[#e4a744]">
                Enregistrer les modifications
            </button>
        </form>
    </section>
</main>
=======
// Contient les colonnes des tables etablissement + localisation (LEFT JOIN)
?>
<main class="flex-1 max-w-6xl mx-auto w-full px-4 py-16">
    <div class="bg-[#071d3b]/50 backdrop-blur-md border border-white/20 rounded-2xl p-8">

        <div class="flex items-center gap-4 mb-8">
            <a href="index.php?route=etablissement/accueil"
               class="text-white/70 hover:text-[#f1b456] duration-300 text-2xl">&lt;</a>
            <h1 class="text-2xl font-bold text-white">Profil de l'établissement</h1>
        </div>

        <form method="POST" action="index.php?route=etablissement/profil" novalidate>

            <h2 class="text-white font-bold mb-4">Informations générales</h2>
            <div class="grid md:grid-cols-2 gap-6 mb-6">

                <!-- Nom de l'établissement -->
                <div class="relative mb-5">
                    <input
                        value="<?= htmlspecialchars($etablissement['nom'] ?? '') ?>"
                        type="text" id="nom" name="nom" required
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                        focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" ">
                    <label for="nom"
                        class="absolute left-4 top-3 text-white/90 text-sm transition-all
                        peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70
                        peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1">
                        Nom de l'établissement
                    </label>
                </div>

                <!-- Type (sélectionné selon la valeur en base) -->
                <div class="relative mb-5">
                    <select id="type" name="type" required
                        class="peer w-full border border-black/20 text-sm text-white rounded-lg px-4 pt-6 pb-2
                        focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]">
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
                            <option value="<?= $val ?>" class="bg-[#071d3b]/50"
                                <?= ($etablissement['type'] ?? '') === $val ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="type"
                        class="absolute left-4 top-3 text-white/90 text-sm peer-focus:text-[#f1b456] -mt-1">
                        Type
                    </label>
                </div>

                <!-- Site web -->
                <div class="relative mb-5 md:col-span-2">
                    <input
                        value="<?= htmlspecialchars($etablissement['site_web'] ?? '') ?>"
                        type="text" id="site_web" name="site_web"
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                        focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" ">
                    <label for="site_web"
                        class="absolute left-4 top-3 text-white/90 text-sm transition-all
                        peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70
                        peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1">
                        Site web
                    </label>
                </div>

            </div>

            <div class="border-t border-white/10 my-6"></div>
            <h2 class="text-white font-bold mb-4">Localisation</h2>

            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <!-- Ville -->
                <div class="relative mb-5">
                    <input
                        value="<?= htmlspecialchars($etablissement['ville'] ?? '') ?>"
                        type="text" id="ville" name="ville"
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                        focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" ">
                    <label for="ville"
                        class="absolute left-4 top-3 text-white/90 text-sm transition-all
                        peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70
                        peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1">
                        Ville
                    </label>
                </div>

                <!-- Adresse -->
                <div class="relative mb-5">
                    <input
                        value="<?= htmlspecialchars($etablissement['adresse'] ?? '') ?>"
                        type="text" id="adresse" name="adresse"
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                        focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" ">
                    <label for="adresse"
                        class="absolute left-4 top-3 text-white/90 text-sm transition-all
                        peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70
                        peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1">
                        Adresse
                    </label>
                </div>
            </div>

            <div class="border-t border-white/10 my-6"></div>
            <h2 class="text-white font-bold mb-4">
                Modifier le mot de passe
                <span class="text-white/50 font-normal text-sm">(laisser vide pour ne pas changer)</span>
            </h2>

            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div class="relative mb-5">
                    <input type="password" id="password" name="password"
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                        focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" ">
                    <label for="password"
                        class="absolute left-4 top-3 text-white/90 text-sm transition-all
                        peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70
                        peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1">
                        Nouveau mot de passe
                    </label>
                </div>
                <div class="relative mb-5">
                    <input type="password" id="password_confirm" name="password_confirm"
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                        focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" ">
                    <label for="password_confirm"
                        class="absolute left-4 top-3 text-white/90 text-sm transition-all
                        peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70
                        peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1">
                        Confirmer le mot de passe
                    </label>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-[#f1b456] text-[#071d3b] font-bold py-3 rounded-lg
                hover:bg-[#f1b456]/80 duration-500 hover:translate-y-0.5 transition-transform">
                Enregistrer les modifications
            </button>
        </form>

    </div>
</main>
>>>>>>> 680f67e9609fecabd25b9ef923ff6d432c465405
