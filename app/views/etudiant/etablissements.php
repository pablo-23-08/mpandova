<?php
// $offres    : toutes les offres (filtrées ou non)
// $recherche : terme de recherche actuel (pour pré-remplir le champ)
?>
<main class="flex-1 max-w-6xl mx-auto w-full px-4 py-16">
    <div class="bg-[#071d3b]/50 backdrop-blur-md border border-white/20 rounded-2xl p-8">

        <div class="flex items-center gap-4 mb-6">
            <a href="index.php?route=etudiant/accueil"
               class="text-white/70 hover:text-[#f1b456] duration-300 text-2xl">&lt;</a>
            <h1 class="text-2xl font-bold text-white">Catalogue des filières</h1>
        </div>

        <!-- Barre de recherche -->
        <form method="GET" action="index.php" class="mb-8">
            <!-- Conserver le paramètre de route dans l'URL lors de la soumission -->
            <input type="hidden" name="route" value="etudiant/etablissements">
            <div class="flex gap-3">
                <input type="text" name="q"
                    value="<?= htmlspecialchars($recherche) ?>"
                    placeholder="Rechercher une filière, un établissement, une ville…"
                    class="flex-1 border border-black/20 text-white rounded-lg px-4 py-3
                    focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]
                    placeholder:text-white/30">
                <button type="submit"
                    class="bg-[#f1b456] text-[#071d3b] font-bold px-6 py-3 rounded-lg
                    hover:bg-[#f1b456]/80 duration-300">
                    Rechercher
                </button>
                <?php if (!empty($recherche)): ?>
                    <!-- Bouton pour effacer la recherche -->
                    <a href="index.php?route=etudiant/etablissements"
                       class="text-white/50 hover:text-white px-3 py-3 rounded-lg
                              border border-white/20 hover:border-white/40 duration-300">
                        ✕
                    </a>
                <?php endif; ?>
            </div>
        </form>

        <!-- Compteur de résultats -->
        <p class="text-white/40 text-sm mb-6">
            <?= count($offres) ?> offre(s) trouvée(s)
            <?= !empty($recherche) ? 'pour « ' . htmlspecialchars($recherche) . ' »' : '' ?>
        </p>

        <?php if (empty($offres)): ?>
            <div class="text-center py-16 text-white/50">
                <p>Aucune offre ne correspond à votre recherche.</p>
            </div>
        <?php else: ?>
            <!-- Grille des offres -->
            <div class="grid md:grid-cols-2 gap-4">
                <?php foreach ($offres as $offre): ?>
                    <div class="bg-[#071d3b]/40 border border-white/10 rounded-xl p-5
                                hover:border-[#f1b456]/30 duration-300 flex flex-col gap-3">

                        <!-- Nom de la filière + établissement -->
                        <div>
                            <h3 class="text-white font-bold text-lg">
                                <?= htmlspecialchars($offre['filiere_nom']) ?>
                            </h3>
                            <p class="text-[#f1b456] text-sm font-medium">
                                <?= htmlspecialchars($offre['etablissement_nom']) ?>
                            </p>
                            <?php if ($offre['ville']): ?>
                                <p class="text-white/40 text-xs mt-1">
                                    📍 <?= htmlspecialchars($offre['ville']) ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <!-- Métadonnées -->
                        <div class="flex flex-wrap gap-2 text-sm text-white/60">
                            <span>💰 <?= number_format($offre['frais_scolarite'], 0, ',', ' ') ?> Ar/an</span>
                            <span>🎓 <?= $offre['place_disponible'] ?> place(s)</span>
                            <?php if ($offre['duree_formation']): ?>
                                <span>📅 <?= htmlspecialchars($offre['duree_formation']) ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Badges conditions d'accès -->
                        <?php if ($offre['serie_bac'] || $offre['moyenne_bac']): ?>
                            <div class="flex flex-wrap gap-2">
                                <?php if ($offre['serie_bac']): ?>
                                    <span class="bg-blue-500/20 text-blue-300 text-xs px-2 py-1 rounded-full">
                                        Série <?= htmlspecialchars($offre['serie_bac']) ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ($offre['moyenne_bac']): ?>
                                    <span class="bg-yellow-500/20 text-yellow-300 text-xs px-2 py-1 rounded-full">
                                        Moy. min. <?= htmlspecialchars($offre['moyenne_bac']) ?>/20
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <span class="text-green-400 text-xs">✓ Ouvert à tous</span>
                        <?php endif; ?>

                        <!-- Bouton Postuler (mini formulaire POST) -->
                        <form method="POST" action="index.php?route=etudiant/candidature-soumettre"
                              class="mt-auto">
                            <input type="hidden" name="id_offre_filiere"
                                   value="<?= $offre['id_offre_filiere'] ?>">
                            <button type="submit"
                                class="w-full bg-[#f1b456] text-[#071d3b] font-bold py-2 rounded-lg
                                       hover:bg-[#f1b456]/80 duration-300 text-sm">
                                Postuler
                            </button>
                        </form>

                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</main>