<?php
// $stats : tableau retourné par Admin::getStats()
// Clés disponibles : nb_etudiants, nb_etablissements, nb_filieres,
//                    nb_offres, nb_candidatures, nb_en_attente
?>
<main class="mx-auto w-full max-w-6xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">

        <!-- En-tête -->
        <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <!-- Initiale "A" stylisée en guise d'avatar admin -->
                <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-[#071d3b] text-2xl font-extrabold text-[#f1b456]">
                    A
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-[#071d3b] sm:text-3xl">
                        Tableau de bord
                    </h1>
                    <p class="mt-1 text-sm text-slate-600">
                        Espace administrateur — Vue d'ensemble de la plateforme
                    </p>
                </div>
            </div>
        </div>

        <!-- Statistiques globales -->
        <div class="mt-6 grid gap-4 sm:grid-cols-2 md:grid-cols-3">

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Étudiants</p>
                <p class="mt-2 text-3xl font-extrabold text-[#071d3b]">
                    <?= $stats['nb_etudiants'] ?>
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Établissements</p>
                <p class="mt-2 text-3xl font-extrabold text-[#071d3b]">
                    <?= $stats['nb_etablissements'] ?>
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Filières (catalogue)</p>
                <p class="mt-2 text-3xl font-extrabold text-[#071d3b]">
                    <?= $stats['nb_filieres'] ?>
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Offres publiées</p>
                <p class="mt-2 text-3xl font-extrabold text-[#071d3b]">
                    <?= $stats['nb_offres'] ?>
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Candidatures totales</p>
                <p class="mt-2 text-3xl font-extrabold text-[#071d3b]">
                    <?= $stats['nb_candidatures'] ?>
                </p>
            </div>

            <!-- Indicateur d'alerte si candidatures en attente -->
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">En attente</p>
                <p class="mt-2 text-3xl font-extrabold text-amber-800">
                    <?= $stats['nb_en_attente'] ?>
                </p>
            </div>

        </div>

        <!-- Raccourcis vers les sections -->
        <h2 class="mt-8 text-lg font-bold text-[#071d3b]">Actions rapides</h2>
        <div class="mt-4 grid gap-4 sm:grid-cols-2">

            <a href="index.php?route=admin/users"
               class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-[#f1b456] hover:bg-[#f1b456]/10">
                <h3 class="text-base font-bold text-[#071d3b]">Utilisateurs</h3>
                <p class="mt-2 text-sm text-slate-600">
                    Consulter et supprimer les comptes étudiants et établissements.
                </p>
            </a>

            <a href="index.php?route=admin/programs"
               class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-[#f1b456] hover:bg-[#f1b456]/10">
                <h3 class="text-base font-bold text-[#071d3b]">Filières</h3>
                <p class="mt-2 text-sm text-slate-600">
                    Gérer le catalogue de filières disponibles sur la plateforme.
                </p>
            </a>

            <a href="index.php?route=admin/institutions"
               class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-[#f1b456] hover:bg-[#f1b456]/10">
                <h3 class="text-base font-bold text-[#071d3b]">Établissements</h3>
                <p class="mt-2 text-sm text-slate-600">
                    Vue d'ensemble de tous les établissements inscrits.
                </p>
            </a>

            <a href="index.php?route=admin/applications"
               class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-[#f1b456] hover:bg-[#f1b456]/10">
                <h3 class="text-base font-bold text-[#071d3b]">Candidatures</h3>
                <p class="mt-2 text-sm text-slate-600">
                    Suivre toutes les candidatures de la plateforme.
                </p>
            </a>

        </div>
    </section>
</main>