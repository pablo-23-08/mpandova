<?php
// $etablissement injectée par FiliereController::index()
// $offres : tableau de toutes les offres avec leurs conditions d'accès
?>
<main class="flex-1 max-w-6xl mx-auto w-full px-4 py-16">
    <div class="bg-[#071d3b]/50 backdrop-blur-md border border-white/20 rounded-2xl p-8">

        <!-- En-tête avec lien retour et bouton ajout -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <a href="index.php?route=etablissement/accueil"
                   class="text-white/70 hover:text-[#f1b456] duration-300 text-2xl">&lt;</a>
                <h1 class="text-2xl font-bold text-white">Mes filières</h1>
            </div>
            <a href="index.php?route=etablissement/filiere-ajouter"
               class="bg-[#f1b456] text-[#071d3b] font-bold px-5 py-2 rounded-lg
                      hover:bg-[#f1b456]/80 duration-300 hover:translate-y-0.5 transition-transform">
                + Ajouter une filière
            </a>
        </div>

        <?php if (empty($offres)): ?>
            <!-- Message si aucune filière n'est encore configurée -->
            <div class="text-center py-16">
                <p class="text-white/50 mb-4">Vous n'avez encore proposé aucune filière.</p>
                <a href="index.php?route=etablissement/filiere-ajouter"
                   class="text-[#f1b456] hover:underline">Ajouter votre première filière</a>
            </div>

        <?php else: ?>
            <!-- Grille des offres de filières -->
            <div class="space-y-4">
                <?php foreach ($offres as $offre): ?>
                    <div class="bg-[#071d3b]/40 border border-white/10 rounded-xl p-5
                                hover:border-white/20 duration-300">

                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">

                            <!-- Infos principales -->
                            <div>
                                <h3 class="text-white font-bold text-lg">
                                    <?= htmlspecialchars($offre['filiere_nom']) ?>
                                </h3>
                                <div class="flex flex-wrap gap-3 mt-2">

                                    <!-- Frais de scolarité -->
                                    <span class="text-white/60 text-sm">
                                        💰 <?= number_format($offre['frais_scolarite'], 0, ',', ' ') ?> Ar/an
                                    </span>

                                    <!-- Places disponibles -->
                                    <span class="text-white/60 text-sm">
                                        🎓 <?= $offre['place_disponible'] ?> place(s)
                                    </span>

                                    <!-- Durée si renseignée -->
                                    <?php if ($offre['duree_formation']): ?>
                                        <span class="text-white/60 text-sm">
                                            📅 <?= htmlspecialchars($offre['duree_formation']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Conditions d'accès si définies -->
                                <?php if ($offre['serie_bac'] || $offre['moyenne_bac']): ?>
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        <?php if ($offre['serie_bac']): ?>
                                            <!-- Badge série requise -->
                                            <span class="bg-blue-500/20 text-blue-300
                                                         text-xs px-2 py-1 rounded-full">
                                                Série <?= htmlspecialchars($offre['serie_bac']) ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($offre['moyenne_bac']): ?>
                                            <!-- Badge moyenne minimale requise -->
                                            <span class="bg-yellow-500/20 text-yellow-300
                                                         text-xs px-2 py-1 rounded-full">
                                                Moy. min. <?= htmlspecialchars($offre['moyenne_bac']) ?>/20
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Actions : Modifier et Supprimer -->
                            <div class="flex gap-3 flex-shrink-0">
                                <a href="index.php?route=etablissement/filiere-modifier&id=<?= $offre['id_offre_filiere'] ?>"
                                   class="bg-[#f1b456]/20 text-[#f1b456] border border-[#f1b456]/30
                                          px-4 py-2 rounded-lg text-sm hover:bg-[#f1b456]/30 duration-300">
                                    Modifier
                                </a>
                                <a href="index.php?route=etablissement/filiere-supprimer&id=<?= $offre['id_offre_filiere'] ?>"
                                   onclick="return confirm('Supprimer cette filière ? Les candidatures associées seront aussi supprimées.')"
                                   class="bg-red-500/20 text-red-400 border border-red-500/30
                                          px-4 py-2 rounded-lg text-sm hover:bg-red-500/30 duration-300">
                                    Supprimer
                                </a>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</main>