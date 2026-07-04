<main class="mx-auto flex w-full max-w-6xl flex-1 items-center px-4 py-10 sm:px-6 lg:px-8">
    <section class="w-full rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">
        <div class="mb-8 flex items-start justify-between gap-4">
            <div>
                <p class="inline-flex rounded-full bg-[#071d3b]/10 px-3 py-1 text-xs font-bold uppercase tracking-wide text-[#071d3b]">Inscription étudiant</p>
                <h1 class="mt-4 text-2xl font-extrabold text-[#071d3b] sm:text-3xl">Créer ton profil étudiant</h1>
                <p class="mt-2 text-sm text-slate-600">Renseigne les informations nécessaires pour démarrer ton orientation.</p>
            </div>
            <a href="index.php?route=auth/register" class="text-sm font-semibold text-[#071d3b] hover:underline">Retour</a>
        </div>

        <form method="POST" action="index.php?route=auth/register-etudiant" novalidate class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="nom" class="mb-2 block text-sm font-semibold text-[#071d3b]">Nom</label>
                <input type="text" id="nom" name="nom" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
            </div>

            <div>
                <label for="prenom" class="mb-2 block text-sm font-semibold text-[#071d3b]">Prénom</label>
                <input type="text" id="prenom" name="prenom" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
            </div>

            <div class="sm:col-span-2">
                <label for="serie_bac" class="mb-2 block text-sm font-semibold text-[#071d3b]">Série du baccalauréat</label>
                <select id="serie_bac" name="serie_bac" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30">
                    <option value="" disabled selected>Choisir une série</option>
                    <?php foreach (['A', 'C', 'D', 'L', 'OSE', 'S'] as $s): ?>
                        <option value="<?= $s ?>">Série <?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="sm:col-span-2">
                <label for="email" class="mb-2 block text-sm font-semibold text-[#071d3b]">E-mail</label>
                <input type="email" id="email" name="email" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30" placeholder="vous@exemple.com">
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
                    Créer mon compte
                </button>
            </div>
        </form>

        <p class="mt-5 text-center text-sm text-slate-600">
            Déjà un compte ?
            <a href="index.php?route=auth/login" class="font-semibold text-[#071d3b] hover:underline">Se connecter</a>
        </p>
    </section>
</main>
