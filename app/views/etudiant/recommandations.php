<?php
// $etudiant        : données de l'étudiant connecté
// $recommandations : tableau trié par score DESC
?>
<main class="flex-1 max-w-6xl mx-auto w-full px-4 py-16">
    <div class="bg-[#071d3b]/50 backdrop-blur-md border border-white/20 rounded-2xl p-8">

        <div class="flex items-center justify-between mb-8 flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <a href="index.php?route=etudiant/accueil"
                   class="text-white/70 hover:text-[#f1b456] duration-300 text-2xl">&lt;</a>
                <h1 class="text-2xl font-bold text-white">Mes recommandations</h1>
            </div>

            <!-- Bouton pour (re)générer les recommandations -->
            <form method="POST" action="index.php?route=etudiant/recommandations">
                <button type="submit"
                    class="bg-[#f1b456] text-[#071d3b] font-bold px-5 py-2 rounded-lg
                           hover:bg-[#f1b456]/80 duration-300 hover:translate-y-0.5 transition-transform">
                    ↻ Générer mes recommandations
                </button>
            </form>
        </div>

        <!-- Message si profil incomplet -->
        <?php if (!$etudiant['serie'] || !$etudiant['moyenne']): ?>
            <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-xl p-4 mb-6">
                <p class="text-yellow-300 text-sm">
                    ⚠️ Votre profil est incomplet.
                    <a href="index.php?route=etudiant/profil" class="underline hover:text-yellow-200">
                        Renseignez votre série et moyenne de bac
                    </a>
                    pour obtenir des recommandations précises.
                </p>
            </div>
        <?php endif; ?>

        <?php if (empty($recommandations)): ?>
            <!-- Aucune recommandation générée -->
            <div class="text-center py-16 text-white/50">
                <p class="mb-4">Aucune recommandation pour le moment.</p>
                <p class="text-sm">Cliquez sur "Générer mes recommandations" pour analyser votre profil.</p>
            </div>
        <?php else: ?>

            <p class="text-white/50 text-sm mb-6">
                <?= count($recommandations) ?> offre(s) compatible(s) trouvée(s), triées par score de compatibilité.
            </p>

            <div class="space-y-4">
                <?php foreach ($recommandations as $i => $reco): ?>
                    <div class="bg-[#071d3b]/40 border border-white/10 rounded-xl p-5
                                hover:border-[#f1b456]/20 duration-300">

                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">

                            <div class="flex-1">
                                <!-- Rang + noms -->
                                <div class="flex items-start gap-3 mb-3">
                                    <!-- Numéro de rang -->
                                    <span class="bg-[#f1b456]/20 text-[#f1b456] font-bold text-sm
                                                 px-2 py-1 rounded-lg flex-shrink-0">
                                        #<?= $i + 1 ?>
                                    </span>
                                    <div>
                                        <h3 class="text-white font-bold">
                                            <?= htmlspecialchars($reco['filiere_nom']) ?>
                                        </h3>
                                        <p class="text-[#f1b456] text-sm">
                                            <?= htmlspecialchars($reco['etablissement_nom']) ?>
                                            <?php if ($reco['ville']): ?>
                                                — <?= htmlspecialchars($reco['ville']) ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- Barre de score visuelle -->
                                <div class="mb-3">
                                    <div class="flex justify-between text-xs text-white/50 mb-1">
                                        <span>Compatibilité</span>
                                        <span class="text-[#f1b456] font-bold">
                                            <?= $reco['score'] ?>/100
                                        </span>
                                    </div>
                                    <!-- Barre de progression : largeur proportionnelle au score -->
                                    <div class="w-full bg-white/10 rounded-full h-2">
                                        <div class="bg-[#f1b456] rounded-full h-2 transition-all duration-500"
                                             style="width: <?= $reco['score'] ?>%">
                                        </div>
                                    </div>
                                </div>

                                <!-- Justification du score -->
                                <p class="text-white/50 text-sm italic">
                                    <?= htmlspecialchars($reco['justification']) ?>
                                </p>

                                <!-- Infos pratiques -->
                                <div class="flex flex-wrap gap-3 mt-3 text-sm text-white/60">
                                    <span>💰 <?= number_format($reco['frais_scolarite'], 0, ',', ' ') ?> Ar/an</span>
                                    <span>🎓 <?= $reco['place_disponible'] ?> place(s)</span>
                                    <?php if ($reco['duree_formation']): ?>
                                        <span>📅 <?= htmlspecialchars($reco['duree_formation']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Bouton Postuler -->
                            <form method="POST" action="index.php?route=etudiant/candidature-soumettre"
                                  class="flex-shrink-0">
                                <input type="hidden" name="id_offre_filiere"
                                       value="<?= $reco['id_offre_filiere'] ?>">
                                <button type="submit"
                                    class="bg-[#f1b456] text-[#071d3b] font-bold py-2 px-6 rounded-lg
                                           hover:bg-[#f1b456]/80 duration-300 text-sm whitespace-nowrap">
                                    Postuler
                                </button>
                            </form>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</main>