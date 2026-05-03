<?php
require_once "../config/bootstrap.php";
redirect_if_logged();
include '../app/views/layouts/header.php';
?>

<main class="flex-1 flex items-center justify-center px-4 py-16">
    <div class="bg-[#071d3b]/50 backdrop-blur-md border border-white/20 rounded-2xl shadow-2xl p-8 w-full max-w-lg">

        <a href="register.php" class="text-4xl text-white/70 hover:text-[#f1b456]">&lt; </a>

        <h2 class="text-xl font-bold text-white text-center -mt-8 mb-10 ">Inscription Établissement</h2>
        <p class="text-white/50 text-sm text-center mb-8">Référencez votre école et publiez vos filières.</p>

        <form method="POST" action="traitement_register.php" novalidate>
            <?php csrf_field(); ?>
            <input type="hidden" name="role" value="etablissement">

            <div class=" relative mb-5">
                <input
                    type="text"
                    id="nom"
                    name="nom"
                    required
                    class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 pb-2 
                    focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                >
                <label
                    for="nom"
                    class="absolute left-4 top-3 text-white/90 text-sm transition-all 
                    peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70
                    peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1"   
                >
                    Nom de l'établissement
                </label>
            </div>

            <div class="relative mb-5">
                <select
                    id="type" name="type" required
                    class="peer w-full border border-black/20 text-sm text-white rounded-lg px-4 pt-6 pb-2
                        focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                >
                    <option value="" disabled selected hidden >Choisir un type…</option>
                    <option value="universite" class="bg-[#071d3b]/50">Université publique</option>
                    <option value="grande_ecole" class="bg-[#071d3b]/50">Grande école</option>
                    <option value="institut_prive" class="bg-[#071d3b]/50">Institut privé</option>
                    <option value="autre" class="bg-[#071d3b]/50">Autre</option>
                </select>

                <label class="absolute left-4 top-3 text-white/90 text-sm transition-all 
                    peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1"
                >
                    Type d'établissement
                </label>
            </div>

            <div class="relative mb-5">
                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                    class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 
                    pb-2 focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                >
                <label
                    for="email"
                    class="absolute left-4 top-3 text-white/90 text-sm transition-all 
                    peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1"
                >
                    E-mail
                </label>
            </div>

            <div class="relative mb-8">
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 
                    pb-2 focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
                >
                <label
                    for="password"
                    class="absolute left-4 top-3 text-white/90 text-sm transition-all 
                    peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70
                    peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1"   
                >
                    Mot de passe
                </label>
            </div>

            <button
                type="submit"
                class="w-full bg-[#f1b456] text-[#071d3b] font-bold py-3 rounded-lg hover:bg-[#f1b456]/80 duration-300 hover:translate-y-0.5 transition-transform"
            >
                Inscrire l'établissement
            </button>
        </form>

        <p class="text-center text-white/50 text-sm mt-6">
            Déjà inscrit ?
            <a href="login.php" class="text-[#f1b456] hover:underline font-medium">Se connecter</a>
        </p>

    </div>
</main>

<?php include '../app/views/layouts/footer.php'; ?>