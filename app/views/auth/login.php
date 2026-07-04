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
