<<<<<<< HEAD
<main class="mx-auto flex w-full max-w-6xl flex-1 items-center px-4 py-10 sm:px-6 lg:px-8">
    <section class="grid w-full gap-8 rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 lg:grid-cols-2 lg:items-center lg:p-10">
        <article>
            <p class="inline-flex rounded-full bg-[#071d3b]/10 px-3 py-1 text-xs font-bold uppercase tracking-wide text-[#071d3b]">Connexion</p>
            <h1 class="mt-4 text-3xl font-extrabold leading-tight text-[#071d3b] sm:text-4xl">Connecte-toi à ton espace Mpandova</h1>
            <p class="mt-4 text-slate-600">Accède à ton suivi d’orientation, à tes recommandations et à tes options d’établissement.</p>
            <a href="index.php" class="mt-6 inline-flex text-sm font-semibold text-[#071d3b] hover:underline">Retour à l’accueil</a>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8">
            <form action="index.php?route=auth/login" method="POST" novalidate class="space-y-5">
                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold text-[#071d3b]">E-mail</label>
                    <input type="email" id="email" name="email" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30" placeholder="vous@exemple.com">
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-semibold text-[#071d3b]">Mot de passe</label>
                    <input type="password" id="password" name="password" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30" placeholder="Votre mot de passe">
                </div>

                <button type="submit" class="w-full rounded-lg bg-[#f1b456] px-5 py-3 text-sm font-bold text-[#071d3b] hover:bg-[#e4a744]">
                    Se connecter
                </button>
            </form>

            <p class="mt-5 text-center text-sm text-slate-600">
                Pas encore de compte ?
                <a href="index.php?route=auth/register" class="font-semibold text-[#071d3b] hover:underline">Créer un compte</a>
            </p>
        </article>
    </section>
</main>
=======
<main class="flex-1 flex items-center justify-center px-4 py-16">
    <div class="bg-[#071d3b]/50 backdrop-blur-md border border-black/20 rounded-2xl shadow-2xl p-8 w-full max-w-md">

        <!-- Lien retour vers l'accueil -->
        <a href="index.php" class="text-4xl text-white/70 hover:text-[#f1b456]">&lt;</a>
        <h2 class="text-xl font-bold text-white text-center -mt-8 mb-10">
            Se connecter à Mpandova
        </h2>

        <!-- action pointe vers la route auth/login (même page, méthode POST) -->
        <!-- novalidate : désactive la validation HTML5, le PHP gère la validation -->
        <form action="index.php?route=auth/login" method="POST" novalidate>

            <!-- Champ email avec floating label (label qui monte au focus) -->
            <div class="relative mb-5">
                <input
                    type="email" id="email" name="email" required
                    class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5
                    pb-2 focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                    placeholder=" "
                >
                <!-- peer-placeholder-shown : label visible quand le champ est vide -->
                <!-- peer-focus : label monte et change de couleur quand le champ est actif -->
                <label for="email"
                    class="absolute left-4 top-3 text-white/90 text-sm transition-all
                    peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70
                    peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1"
                >
                    E-mail
                </label>
            </div>

            <!-- Champ mot de passe -->
            <div class="relative mb-8">
                <input
                    type="password" id="password" name="password" required
                    class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5
                    pb-2 focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                    placeholder=" "
                >
                <label for="password"
                    class="absolute left-4 top-3 text-white/90 text-sm transition-all
                    peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70
                    peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1"
                >
                    Mot de passe
                </label>
            </div>

            <button type="submit"
                class="w-full bg-[#f1b456] text-[#071d3b] font-bold py-3 rounded-lg
                hover:bg-[#f1b456]/80 duration-500 hover:translate-y-0.5 transition-transform">
                Se connecter
            </button>
        </form>

        <p class="text-center text-white/50 text-sm mt-6">
            Pas encore de compte ?
            <a href="index.php?route=auth/register" class="text-[#f1b456] hover:underline font-medium">
                Créer un compte
            </a>
        </p>

    </div>
</main>
>>>>>>> 680f67e9609fecabd25b9ef923ff6d432c465405
