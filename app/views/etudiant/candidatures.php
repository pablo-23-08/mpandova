<?php
// $candidatures : liste des candidatures de l'étudiant, triées par date DESC
?>
<main class="flex-1 max-w-6xl mx-auto w-full px-4 py-16">
    <div class="bg-[#071d3b]/50 backdrop-blur-md border border-white/20 rounded-2xl p-8">

        <div class="flex items-center gap-4 mb-8">
            <a href="index.php?route=etudiant/accueil"
               class="text-white/70 hover:text-[#f1b456] duration-300 text-2xl">&lt;</a>
            <h1 class="text-2xl font-bold text-white">Mes candidatures</h1>
        </div>

        <?php if (empty($candidatures)): ?>
            <div class="text-center py-16 text-white/50">
                <p class="mb-4">Vous n'avez encore soumis aucune candidature.</p>
                <a href="index.php?route=etudiant/etablissements"
                   class="text-[#f1b456] hover:underline">
                    Explorer les filières disponibles
                </a>
            </div>
        <?php else: ?>

            <div class="space-y-4">
                <?php foreach ($candidatures as $c): ?>
                    <div class="bg-[#071d3b]/40 border border-white/10 rounded-xl p-5
                                hover:border-white/20 duration-300">

                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">

                            <!-- Informations sur la candidature -->
                            <div>
                                <h3 class="text-white font-bold">
                                    <?= htmlspecialchars($c['filiere_nom']) ?>
                                </h3>
                                <p class="text-[#f1b456] text-sm">
                                    <?= htmlspecialchars($c['etablissement_nom']) ?>
                                </p>
                                <p class="text-white/40 text-xs mt-1">
                                    Postulé le <?= date('d/m/Y à H:i', strtotime($c['date_candidature'])) ?>
                                </p>
                                <?php if ($c['date_traitement']): ?>
                                    <p class="text-white/40 text-xs">
                                        Traité le <?= date('d/m/Y', strtotime($c['date_traitement'])) ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <!-- Statut + action d'annulation -->
                            <div class="flex items-center gap-3 flex-shrink-0">

                                <?php
                                // Mapping statut → classe CSS et label
                                $badges = [
                                    'en_attente' => ['bg-yellow-500/20 text-yellow-300 border-yellow-500/30', '⏳ En attente'],
                                    'acceptee'   => ['bg-green-500/20  text-green-300  border-green-500/30',  '✓ Acceptée'],
                                    'refusee'    => ['bg-red-500/20    text-red-400    border-red-500/30',    '✗ Refusée'],
                                    'annulee'    => ['bg-gray-500/20   text-gray-400   border-gray-500/30',   '— Annulée'],
                                ];
                                // Récupérer le badge correspondant au statut, ou un badge générique
                                [$badgeClass, $badgeLabel] = $badges[$c['statut']] ?? ['bg-gray-500/20 text-gray-400 border-gray-500/30', $c['statut']];
                                ?>

                                <!-- Badge de statut -->
                                <span class="border text-xs px-3 py-1 rounded-full font-medium <?= $badgeClass ?>">
                                    <?= $badgeLabel ?>
                                </span>

                                <!-- Bouton Annuler : visible uniquement pour les candidatures en attente -->
                                <?php if ($c['statut'] === 'en_attente'): ?>
                                    <form method="POST"
                                          action="index.php?route=etudiant/candidature-annuler">
                                        <input type="hidden" name="id_candidature"
                                               value="<?= $c['id_candidature'] ?>">
                                        <button type="submit"
                                            onclick="return confirm('Annuler cette candidature ?')"
                                            class="text-red-400 hover:text-red-300 text-sm
                                                   border border-red-500/30 hover:border-red-400/50
                                                   px-3 py-1 rounded-lg duration-300">
                                            Annuler
                                        </button>
                                    </form>
                                <?php endif; ?>

                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</main>