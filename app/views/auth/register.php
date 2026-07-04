<<<<<<< HEAD
<main class="mx-auto flex w-full max-w-6xl flex-1 items-center px-4 py-10 sm:px-6 lg:px-8">
    <section class="w-full rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8 lg:p-10">
        <div class="text-center">
            <p class="inline-flex rounded-full bg-[#071d3b]/10 px-3 py-1 text-xs font-bold uppercase tracking-wide text-[#071d3b]">Inscription</p>
            <h1 class="mt-4 text-3xl font-extrabold leading-tight text-[#071d3b] sm:text-4xl">Choisir un type de compte</h1>
            <p class="mt-3 text-slate-600">Sélectionne le profil qui correspond à ton usage de la plateforme.</p>
        </div>

        <div class="mt-8 grid gap-4 sm:grid-cols-2">
            <a href="index.php?route=auth/register-etudiant" class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-[#f1b456] hover:bg-[#f1b456]/10">
                <img src="assets/img/student.webp" alt="Compte étudiant" class="h-12 w-12 object-contain">
                <h2 class="mt-4 text-lg font-bold text-[#071d3b]">Étudiant</h2>
                <p class="mt-2 text-sm text-slate-600">Créer un profil pour recevoir des recommandations de filières et établissements.</p>
                <span class="mt-4 inline-flex text-sm font-semibold text-[#071d3b] group-hover:underline">Continuer</span>
            </a>

            <a href="index.php?route=auth/register-etablissement" class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-[#f1b456] hover:bg-[#f1b456]/10">
                <img src="assets/img/school.webp" alt="Compte établissement" class="h-12 w-12 object-contain">
                <h2 class="mt-4 text-lg font-bold text-[#071d3b]">Établissement</h2>
                <p class="mt-2 text-sm text-slate-600">Publier des filières, gérer les informations de l’établissement et suivre les candidatures.</p>
                <span class="mt-4 inline-flex text-sm font-semibold text-[#071d3b] group-hover:underline">Continuer</span>
            </a>
        </div>

        <p class="mt-7 text-center text-sm text-slate-600">
            Déjà un compte ?
            <a href="index.php?route=auth/login" class="font-semibold text-[#071d3b] hover:underline">Se connecter</a>
        </p>
    </section>
</main>
=======
<main class="flex-1 flex items-center justify-center px-4 py-16">
    <div class="bg-[#071d3b]/50 backdrop-blur-md border border-black/20 rounded-2xl shadow-2xl p-8 w-full max-w-md text-center">

        <a href="index.php" class="text-4xl text-white/70 hover:text-[#f1b456] mr-90">&lt;</a>
        <h2 class="text-xl font-bold text-white text-center -mt-8 mb-10">Créer un compte</h2>
        <p class="text-white/70 text-sm mb-10">Quel type de compte souhaitez-vous créer ?</p>

        <div class="grid grid-cols-2 gap-4">
            <!-- Chaque carte redirige vers la route d'inscription correspondante -->
            <a href="index.php?route=auth/register-etudiant"
               class="flex flex-col items-center gap-3 bg-black/10 border border-black/20 rounded-xl p-6
                      hover:border-[#f1b456] hover:bg-[#f1b456]/10 duration-300 group">
                <span class="text-4xl"><img src="../assets/img/student.webp" alt="étudiant"/></span>
                <span class="text-white font-bold group-hover:text-[#f1b456] duration-300">Étudiant</span>
                <span class="text-white/70 text-xs">Tu cherches une filière</span>
            </a>
            <a href="index.php?route=auth/register-etablissement"
               class="flex flex-col items-center gap-3 bg-black/10 border border-black/20 rounded-xl p-6
                      hover:border-[#f1b456] hover:bg-[#f1b456]/10 duration-300 group">
                <span class="text-4xl"><img src="../assets/img/school.webp" alt="établissement"/></span>
                <span class="text-white font-bold group-hover:text-[#f1b456] duration-300">Établissement</span>
                <span class="text-white/70 text-xs">Tu proposes des filières</span>
            </a>
        </div>

        <p class="text-center text-white/70 text-sm mt-8">
            Déjà un compte ?
            <a href="index.php?route=auth/login" class="text-[#f1b456] hover:underline font-medium">
                Se connecter
            </a>
        </p>

    </div>
</main>
>>>>>>> 680f67e9609fecabd25b9ef923ff6d432c465405
