<main class="mx-auto flex w-full max-w-6xl flex-1 items-center px-4 py-10 sm:px-6 lg:px-8">
    <section class="w-full rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8 lg:p-10">
        
        <div class="flex flex-col gap-4  pb-6 sm:flex-row sm:items-center sm:justify-between mb-6">
            <div class="text-center">
                <p class="inline-flex rounded-full bg-[#071d3b]/10 px-3 py-1 text-xs font-bold uppercase tracking-wide text-[#071d3b]">Inscription</p>
                <h1 class="mt-4 text-3xl font-extrabold leading-tight text-[#071d3b] sm:text-4xl">Choisir un type de compte</h1>
                <p class="mt-3 text-slate-600">Sélectionne le profil qui correspond à votre usage de la plateforme.</p>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-2 sm:gap-3">
                <a href="index.php?route=home" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-[#071d3b] hover:border-[#f1b456]">
                    Revenir à l’accueil
                </a>
            </div>
        </div>

        <div class="mt-8 grid gap-4 sm:grid-cols-2">
            <a href="index.php?route=auth/register-etudiant" class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-[#f1b456] hover:bg-[#f1b456]/10">
                <img src="assets/img/student.webp" alt="Compte étudiant" class="h-12 w-12 object-contain">
                <h2 class="mt-4 text-lg font-bold text-[#071d3b]">Étudiant</h2>
                <p class="mt-2 text-sm text-slate-600">Créer un profil pour recevoir des recommandations de filières et établissements.</p>
            </a>

            <a href="index.php?route=auth/register-etablissement" class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-[#f1b456] hover:bg-[#f1b456]/10">
                <img src="assets/img/school.webp" alt="Compte établissement" class="h-12 w-12 object-contain">
                <h2 class="mt-4 text-lg font-bold text-[#071d3b]">Établissement</h2>
                <p class="mt-2 text-sm text-slate-600">Publier des filières, gérer les informations de l’établissement et suivre les candidatures.</p>
            </a>
        </div>

        <p class="mt-7 text-center text-sm text-slate-600">
            Déjà un compte ?
            <a href="index.php?route=auth/login" class="font-semibold text-[#071d3b] hover:underline">Se connecter</a>
        </p>
    </section>
</main>
