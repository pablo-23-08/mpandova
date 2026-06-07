<?php
// $etablissement est injectée par EtablissementController::profil()
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