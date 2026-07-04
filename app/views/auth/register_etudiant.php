<<<<<<< HEAD
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
=======
<main class="flex-1 flex items-center justify-center px-4 py-16">
    <div class="bg-[#071d3b]/50 backdrop-blur-md border border-white/20 rounded-2xl shadow-2xl p-8 w-full max-w-lg">

        <a href="index.php?route=auth/register" class="text-4xl text-white/70 hover:text-[#f1b456]">&lt;</a>
        <h2 class="text-xl font-bold text-white text-center -mt-8 mb-10">Inscription Étudiant</h2>
        <p class="text-white/50 text-sm text-center mb-8">Trouve ta voie avec des recommandations personnalisées.</p>

        <!-- action pointe vers la même route en POST -->
        <form method="POST" action="index.php?route=auth/register-etudiant" novalidate>

            <!-- Grille 2 colonnes pour Nom / Prénom -->
            <div class="grid grid-cols-2 gap-4 mb-5">
                <div class="relative">
                    <input type="text" id="nom" name="nom" required
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                        focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" ">
                    <label for="nom"
                        class="absolute left-4 top-3 text-white/90 text-sm transition-all
                        peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70
                        peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1">
                        Nom
                    </label>
                </div>
                <div class="relative">
                    <input type="text" id="prenom" name="prenom" required
                        class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                        focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                        placeholder=" ">
                    <label for="prenom"
                        class="absolute left-4 top-3 text-white/90 text-sm transition-all
                        peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70
                        peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1">
                        Prénom
                    </label>
                </div>
            </div>

            <!-- Sélection de la série de bac -->
            <div class="relative mb-5">
                <select id="serie_bac" name="serie_bac" required
                    class="peer w-full border border-black/20 text-sm text-white rounded-lg px-4 pt-6 pb-2
                    focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]">
                    <option value="" disabled selected hidden>Choisir une série…</option>
                    <?php foreach (['A', 'C', 'D', 'L', 'OSE', 'S'] as $s): ?>
                        <!-- Boucle PHP : génère 6 options dynamiquement -->
                        <option value="<?= $s ?>" class="bg-[#071d3b]/50">Série <?= $s ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="serie_bac"
                    class="absolute left-4 top-3 text-white/90 text-sm peer-focus:text-[#f1b456] -mt-1">
                    Série du baccalauréat
                </label>
            </div>

            <!-- Email -->
            <div class="relative mb-5">
                <input type="email" id="email" name="email" required
                    class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                    focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                    placeholder=" ">
                <label for="email"
                    class="absolute left-4 top-3 text-white/90 text-sm transition-all
                    peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70
                    peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1">
                    E-mail
                </label>
            </div>

            <!-- Mot de passe -->
            <div class="relative mb-5">
                <input type="password" id="password" name="password" required
                    class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2
                    focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                    placeholder=" ">
                <label for="password"
                    class="absolute left-4 top-3 text-white/90 text-sm transition-all
                    peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70
                    peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1">
                    Mot de passe
                </label>
            </div>

            <!-- Confirmation du mot de passe -->
            <div class="relative mb-8">
                <input type="password" id="password_confirm" name="password_confirm" required
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

            <button type="submit"
                class="w-full bg-[#f1b456] text-[#071d3b] font-bold py-3 rounded-lg
                hover:bg-[#f1b456]/80 duration-300 hover:translate-y-0.5 transition-transform">
                Créer mon compte
            </button>
        </form>

        <p class="text-center text-white/50 text-sm mt-6">
            Déjà un compte ?
            <a href="index.php?route=auth/login" class="text-[#f1b456] hover:underline font-medium">
                Se connecter
            </a>
        </p>

    </div>
</main>
>>>>>>> 680f67e9609fecabd25b9ef923ff6d432c465405
