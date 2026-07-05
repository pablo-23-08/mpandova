<?php
// ═══════════════════════════════════════════════
// MODEL Filiere
// Gère : filiere, offre_filiere, condition_acces, recommandation
// ═══════════════════════════════════════════════

class Filiere
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // ─────────────────────────────────────────────
    // CÔTÉ ÉTABLISSEMENT — Gestion des offres
    // ─────────────────────────────────────────────

    /**
     * Récupère toutes les offres d'un établissement, avec les conditions d'accès.
     * Utilisé pour afficher la liste des filières gérées par l'établissement.
     */
    public function offresParEtablissement(int $idEtablissement): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                off.*,
                f.nom  AS filiere_nom,
                f.description AS filiere_description,
                ca.id_condition_acces,
                ca.serie_bac,
                ca.moyenne_bac,
                ca.age_max,
                ca.diplome_requis
            FROM offre_filiere off
            JOIN filiere f ON f.id_filiere = off.id_filiere
            LEFT JOIN condition_acces ca ON ca.id_offre_filiere = off.id_offre_filiere
            WHERE off.id_etablissement = ?
            ORDER BY f.nom ASC
        ");
        $stmt->execute([$idEtablissement]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère une seule offre par son ID, avec toutes ses données liées.
     * Utilisé pour pré-remplir le formulaire de modification.
     */
    public function findOffreById(int $idOffre): array|false
    {
        $stmt = $this->pdo->prepare("
            SELECT
                off.*,
                f.nom AS filiere_nom,
                ca.id_condition_acces,
                ca.serie_bac,
                ca.moyenne_bac,
                ca.age_max,
                ca.diplome_requis,
                ca.annee_bac
            FROM offre_filiere of
            JOIN filiere f ON f.id_filiere = off.id_filiere
            LEFT JOIN condition_acces ca ON ca.id_offre_filiere = off.id_offre_filiere
            WHERE off.id_offre_filiere = ?
        ");
        $stmt->execute([$idOffre]);
        return $stmt->fetch();
    }

    /**
     * Récupère toutes les filières disponibles (table de référence).
     * Utilisé pour peupler le <select> dans le formulaire d'ajout.
     */
    public function toutesLesFilieres(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM filiere ORDER BY nom ASC");
        return $stmt->fetchAll();
    }

    /**
     * Crée une nouvelle offre de filière pour un établissement.
     * @return int L'ID de l'offre créée
     */
    public function creerOffre(
        int    $idEtablissement,
        int    $idFiliere,
        float  $frais,
        int    $places,
        ?string $duree
    ): int {
        $stmt = $this->pdo->prepare("
            INSERT INTO offre_filiere
                (id_etablissement, id_filiere, frais_scolarite, place_disponible, duree_formation)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$idEtablissement, $idFiliere, $frais, $places, $duree]);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Met à jour les informations d'une offre existante.
     */
    public function mettreAJourOffre(
        int    $idOffre,
        int    $idFiliere,
        float  $frais,
        int    $places,
        ?string $duree
    ): void {
        $stmt = $this->pdo->prepare("
            UPDATE offre_filiere
            SET id_filiere = ?, frais_scolarite = ?, place_disponible = ?, duree_formation = ?
            WHERE id_offre_filiere = ?
        ");
        $stmt->execute([$idFiliere, $frais, $places, $duree, $idOffre]);
    }

    /**
     * Supprime une offre de filière (CASCADE supprime aussi sa condition_acces).
     */
    public function supprimerOffre(int $idOffre): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM offre_filiere WHERE id_offre_filiere = ?");
        $stmt->execute([$idOffre]);
    }

    /**
     * Crée ou met à jour les conditions d'accès d'une offre (pattern upsert).
     * Une offre ne peut avoir qu'une seule condition_acces (contrainte UNIQUE).
     */
    public function upsertConditionAcces(
        int     $idOffre,
        ?string $serie,
        ?float  $moyenneMin,
        ?int    $ageMax
    ): void {
        // Vérifier si une condition existe déjà
        $stmt = $this->pdo->prepare(
            "SELECT id_condition_acces FROM condition_acces WHERE id_offre_filiere = ?"
        );
        $stmt->execute([$idOffre]);
        $existante = $stmt->fetch();

        if ($existante) {
            // Mise à jour
            $stmt = $this->pdo->prepare("
                UPDATE condition_acces
                SET serie_bac = ?, moyenne_bac = ?, age_max = ?
                WHERE id_offre_filiere = ?
            ");
            $stmt->execute([$serie, $moyenneMin, $ageMax, $idOffre]);
        } else {
            // Création
            $stmt = $this->pdo->prepare("
                INSERT INTO condition_acces (id_offre_filiere, serie_bac, moyenne_bac, age_max)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$idOffre, $serie, $moyenneMin, $ageMax]);
        }
    }

    // ─────────────────────────────────────────────
    // CÔTÉ ÉTUDIANT — Catalogue
    // ─────────────────────────────────────────────

    /**
     * Récupère toutes les offres disponibles pour le catalogue.
     * Supporte la recherche textuelle sur le nom de la filière ou de l'établissement.
     */
    public function toutesLesOffres(?string $recherche = null): array
    {
        $sql = "
            SELECT
                off.*,
                f.nom  AS filiere_nom,
                f.description AS filiere_description,
                e.nom  AS etablissement_nom,
                e.type AS etablissement_type,
                l.ville, l.region,
                ca.serie_bac, ca.moyenne_bac, ca.age_max
            FROM offre_filiere off
            JOIN filiere      f  ON f.id_filiere        = off.id_filiere
            JOIN etablissement e ON e.id_etablissement  = off.id_etablissement
            LEFT JOIN localisation l  ON l.id_etablissement = e.id_etablissement
            LEFT JOIN condition_acces ca ON ca.id_offre_filiere = off.id_offre_filiere
            WHERE 1=1
        ";
        $params = [];

        // Si une recherche est saisie, filtrer sur le nom de la filière ou de l'établissement
        if (!empty($recherche)) {
            $sql .= " AND (f.nom LIKE ? OR e.nom LIKE ? OR l.ville LIKE ?)";
            $like = "%$recherche%";
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $sql .= " ORDER BY e.nom ASC, f.nom ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────────
    // RECOMMANDATIONS — Moteur de score
    // ─────────────────────────────────────────────

    /**
     * Calcule les recommandations personnalisées pour un étudiant.
     * Score sur 100 pts selon 3 critères :
     *   - Série bac compatible : 40 pts (correspondance exacte) ou 30 pts (ouvert à tous)
     *   - Moyenne suffisante   : 40 pts (ok) ou 20 pts (pas de condition)
     *   - Places disponibles   : 20 pts (>10) ou 10 pts (1-10)
     * Les offres incompatibles (série ou moyenne insuffisante) sont exclues.
     */
    public function calculerEtSauvegarderRecommandations(int $idEtudiant, array $etudiant): void
    {
        // 1. Supprimer les anciennes recommandations de cet étudiant
        $stmt = $this->pdo->prepare("DELETE FROM recommandation WHERE id_etudiant = ?");
        $stmt->execute([$idEtudiant]);

        // 2. Récupérer toutes les offres avec leurs conditions d'accès
        $stmt = $this->pdo->prepare("
            SELECT
                off.id_offre_filiere,
                off.place_disponible,
                f.nom AS filiere_nom,
                ca.serie_bac    AS serie_requise,
                ca.moyenne_bac  AS moyenne_requise
            FROM offre_filiere off
            JOIN filiere f ON f.id_filiere = off.id_filiere
            LEFT JOIN condition_acces ca ON ca.id_offre_filiere = off.id_offre_filiere
        ");
        $stmt->execute();
        $offres = $stmt->fetchAll();

        // 3. Extraire le profil de l'étudiant
        $serieBac   = $etudiant['serie']   ?? null;
        $moyenneBac = ($etudiant['moyenne'] !== null) ? (float) $etudiant['moyenne'] : null;

        $recommandations = [];

        foreach ($offres as $offre) {
            $score = 0;
            $justifications = [];

            // ── Critère 1 : Série du baccalauréat ──
            if ($offre['serie_requise'] === null) {
                // Aucune restriction de série → ouvert à tous
                $score += 30;
                $justifications[] = "Formation ouverte à toutes les séries";
            } elseif ($offre['serie_requise'] === $serieBac) {
                // Série de l'étudiant correspond exactement à celle requise
                $score += 40;
                $justifications[] = "Série {$serieBac} requise — correspond à votre profil";
            } else {
                // Série incompatible → on saute cette offre entièrement
                continue;
            }

            // ── Critère 2 : Moyenne du baccalauréat ──
            if ($offre['moyenne_requise'] === null) {
                // Pas de condition de moyenne
                $score += 20;
                $justifications[] = "Aucune moyenne minimale requise";
            } elseif ($moyenneBac !== null && $moyenneBac >= (float) $offre['moyenne_requise']) {
                // Moyenne de l'étudiant >= condition requise
                $score += 40;
                $diff = round($moyenneBac - (float) $offre['moyenne_requise'], 2);
                $justifications[] = "Votre moyenne ({$moyenneBac}/20) dépasse le minimum requis ({$offre['moyenne_requise']}/20) de +{$diff} pts";
            } elseif ($moyenneBac === null) {
                // L'étudiant n'a pas encore renseigné sa moyenne
                $score += 10;
                $justifications[] = "Renseignez votre moyenne dans votre profil pour une évaluation complète";
            } else {
                // Moyenne insuffisante → on exclut cette offre
                continue;
            }

            // ── Critère 3 : Places disponibles (bonus) ──
            if ($offre['place_disponible'] > 10) {
                $score += 20;
                $justifications[] = "{$offre['place_disponible']} places disponibles";
            } elseif ($offre['place_disponible'] > 0) {
                $score += 10;
                $justifications[] = "{$offre['place_disponible']} place(s) disponible(s) — places limitées";
            }

            // On ne recommande que les offres avec un score positif
            if ($score > 0) {
                $recommandations[] = [
                    'id_offre_filiere' => $offre['id_offre_filiere'],
                    // min() : cap à 100 au cas où les critères bonus dépassent
                    'score'            => min($score, 100),
                    'justification'    => implode('. ', $justifications) . '.',
                ];
            }
        }

        // 4. Insérer toutes les recommandations en base
        if (empty($recommandations)) {
            return; // Rien à insérer
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO recommandation (id_etudiant, id_offre_filiere, score, justification)
            VALUES (?, ?, ?, ?)
        ");
        foreach ($recommandations as $r) {
            $stmt->execute([
                $idEtudiant,
                $r['id_offre_filiere'],
                $r['score'],
                $r['justification'],
            ]);
        }
    }

    /**
     * Récupère les recommandations sauvegardées d'un étudiant, triées par score.
     */
    public function recommandationsParEtudiant(int $idEtudiant): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                r.*,
                f.nom  AS filiere_nom,
                e.nom  AS etablissement_nom,
                l.ville,
                off.frais_scolarite,
                off.place_disponible,
                off.duree_formation,
                ca.serie_bac, ca.moyenne_bac
            FROM recommandation r
            JOIN offre_filiere  off ON off.id_offre_filiere  = r.id_offre_filiere
            JOIN filiere        f  ON f.id_filiere         = off.id_filiere
            JOIN etablissement  e  ON e.id_etablissement   = off.id_etablissement
            LEFT JOIN localisation    l  ON l.id_etablissement  = e.id_etablissement
            LEFT JOIN condition_acces ca ON ca.id_offre_filiere = off.id_offre_filiere
            WHERE r.id_etudiant = ?
            ORDER BY r.score DESC
        ");
        $stmt->execute([$idEtudiant]);
        return $stmt->fetchAll();
    }
}