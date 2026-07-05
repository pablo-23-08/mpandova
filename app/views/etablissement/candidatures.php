<?php
// $candidatures : liste des candidatures reçues
// $statut       : filtre actif ('tous', 'en_attente', 'acceptee', 'refusee')
?>
<main class="flex-1 max-w-6xl mx-auto w-full px-4 py-16">
    <div class="bg-[#071d3b]/50 backdrop-blur-md border border-white/20 rounded-2xl p-8">

        <div class="flex items-center gap-4 mb-8">
            <a href="index.php?route=etablissement/accueil"
               class="text-white/70 hover:text-[#f1b456] duration-300 text-2xl">&lt;</a>
            <h1 class="text-2xl font-bold text-white">Candidatures reçues</h1>
        </div>

        <!-- Onglets de filtrage par statut -->
        <div class="flex flex-wrap gap-2 mb-6">
            <?php
            $onglets = [
                'tous'       => 'Toutes',
                'en_attente' => 'En attente',
                'acceptee'   => 'Acceptées',
                'refusee'    => 'Refusées',
            ];
            foreach ($onglets as $val => $label):
                // L'onglet actif a un style différent
                $isActif = $statut === $val;
            ?>
                <a href="index.php?route=etablissement/candidatures&statut=<?= $val ?>"
                   class="px-4 py-2 rounded-lg text-sm font-medium duration-300
                          <?= $isActif
                              ? 'bg-[#f1b456] text-[#071d3b]'
                              : 'bg-white/10 text-white/60 hover:bg-white/20' ?>">
                    <?= $label ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($candidatures)): ?>
            <div class="text-center py-16 text-white/50">
                <p>Aucune candidature pour ce filtre.</p>
            </div>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($candidatures as $c): ?>
                    <div class="bg-[#071d3b]/40 border border-white/10 rounded-xl p-5
                                hover:border-white/20 duration-300">

                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">

                            <!-- Infos de l'étudiant + filière -->
                            <div>
                                <h3 class="text-white font-bold">
                                    <?= htmlspecialchars($c['etudiant_prenom'] . ' ' . $c['etudiant_nom']) ?>
                                </h3>
                                <p class="text-[#f1b456] text-sm">
                                    <?= htmlspecialchars($c['filiere_nom']) ?>
                                </p>
                                <!-- Série et moyenne du bac -->
                                <div class="flex flex-wrap gap-2 mt-1">
                                    <?php if ($c['serie']): ?>
                                        <span class="text-white/50 text-xs">
                                            Série <?= htmlspecialchars($c['serie']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($c['moyenne']): ?>
                                        <span class="text-white/50 text-xs">
                                            Moy. <?= htmlspecialchars($c['moyenne']) ?>/20
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-white/30 text-xs mt-1">
                                    Soumis le <?= date('d/m/Y', strtotime($c['date_candidature'])) ?>
                                </p>
                            </div>

                            <!-- Statut + actions -->
                            <div class="flex items-center gap-3 flex-shrink-0">

                                <?php if ($c['statut'] === 'en_attente'): ?>
                                    <!-- Boutons Accepter / Refuser -->
                                    <form method="POST"
                                          action="index.php?route=etablissement/candidature-traiter"
                                          class="flex gap-2">
                                        <input type="hidden" name="id_candidature"
                                               value="<?= $c['id_candidature'] ?>">

                                        <!-- Bouton Accepter -->
                                        <button type="submit" name="statut" value="acceptee"
                                            class="bg-green-500/20 text-green-300 border border-green-500/30
                                                   px-4 py-2 rounded-lg text-sm hover:bg-green-500/30 duration-300">
                                            Accepter
                                        </button>

                                        <!-- Bouton Refuser -->
                                        <button type="submit" name="statut" value="refusee"
                                            class="bg-red-500/20 text-red-400 border border-red-500/30
                                                   px-4 py-2 rounded-lg text-sm hover:bg-red-500/30 duration-300">
                                            Refuser
                                        </button>
                                    </form>

                                <?php else: ?>
                                    <!-- Statut affiché si déjà traité -->
                                    <?php
                                    $badges = [
                                        'acceptee' => 'bg-green-500/20 text-green-300 border-green-500/30',
                                        'refusee'  => 'bg-red-500/20 text-red-400 border-red-500/30',
                                        'annulee'  => 'bg-gray-500/20 text-gray-400 border-gray-500/30',
                                    ];
                                    $labels = [
                                        'acceptee' => '✓ Acceptée',
                                        'refusee'  => '✗ Refusée',
                                        'annulee'  => '— Annulée par l\'étudiant',
                                    ];
                                    $badgeClass = $badges[$c['statut']] ?? 'bg-gray-500/20 text-gray-400 border-gray-500/30';
                                    $badgeLabel = $labels[$c['statut']] ?? $c['statut'];
                                    ?>
                                    <span class="border text-xs px-3 py-1 rounded-full <?= $badgeClass ?>">
                                        <?= $badgeLabel ?>
                                    </span>
                                <?php endif; ?>

                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</main>