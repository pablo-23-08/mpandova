<main class="mx-auto flex w-full max-w-6xl flex-1 items-center px-4 py-10 sm:px-6 lg:px-8">
    <section class="w-full rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">
        <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <p class="inline-flex rounded-full bg-[#071d3b]/10 px-3 py-1 text-xs font-bold uppercase tracking-wide text-[#071d3b]">Inscription établissement</p>
                <h1 class="mt-4 text-2xl font-extrabold text-[#071d3b] sm:text-3xl">Créer un compte établissement</h1>
                <p class="mt-2 text-sm text-slate-600">Référence ton école et prépare la gestion de tes filières.</p>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-2 sm:gap-3">
                <a href="index.php?route=auth/register" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-[#071d3b] hover:border-[#f1b456]">
                    Retour
                </a>
            </div>
        </div>

        <form method="POST" action="index.php?route=auth/register-etablissement" novalidate class="grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="nom" class="mb-2 block text-sm font-semibold text-[#071d3b]">Nom de l'établissement</label>
                <input type="text" id="nom" name="nom" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
            </div>

            <div class="sm:col-span-2">
                <label for="type" class="mb-2 block text-sm font-semibold text-[#071d3b]">Type d'établissement</label>
                <select id="type" name="type" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
                    <option value="" disabled selected>Choisir un type</option>
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
                        <option value="<?= $val ?>"><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="sm:col-span-2">
                <label for="email" class="mb-2 block text-sm font-semibold text-[#071d3b]">E-mail</label>
                <input type="email" id="email" name="email" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30" placeholder="">
            </div>

            <div>
                <label for="password" class="mb-2 block text-sm font-semibold text-[#071d3b]">Mot de passe</label>
                <input type="password" id="password" name="password" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
            </div>

            <div>
                <label for="password_confirm" class="mb-2 block text-sm font-semibold text-[#071d3b]">Confirmer le mot de passe</label>
                <input type="password" id="password_confirm" name="password_confirm" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
            </div>

            <div class="sm:col-span-2">
                <button type="submit" class="w-full rounded-lg bg-[#f1b456] px-5 py-3 text-sm font-bold text-[#071d3b] hover:bg-[#e4a744]">
                    Inscrire l'établissement
                </button>
            </div>
        </form>

        <p class="mt-5 text-center text-sm text-slate-600">
            Déjà inscrit ?
            <a href="index.php?route=auth/login" class="font-semibold text-[#071d3b] hover:underline">Se connecter</a>
        </p>
    </section>
</main>
