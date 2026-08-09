<?php
// ═══════════════════════════════════════════════
// MODEL Program (anciennement "Filiere")
// Gère les tables `filiere`, `offre_filiere`, `condition_acces`, `recommandation`
// Remarque : les noms de tables/colonnes SQL restent inchangés
// ═══════════════════════════════════════════════

class Program
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
    public function findOffersByInstitution(int $institutionId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                off.*,
                f.nom AS filiere_nom, f.description AS filiere_description,
                ca.id_condition_acces, ca.serie_bac, ca.moyenne_bac, ca.age_max, ca.diplome_requis
            FROM offre_filiere off
            JOIN filiere f ON f.id_filiere = off.id_filiere
            LEFT JOIN condition_acces ca ON ca.id_offre_filiere = off.id_offre_filiere
            WHERE off.id_etablissement = ?
            ORDER BY f.nom ASC
        ");
        $stmt->execute([$institutionId]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère une seule offre par son ID, avec toutes ses données liées.
     * Utilisé pour pré-remplir le formulaire de modification.
     */
    public function findOfferById(int $offerId): array|false
    {
        $stmt = $this->pdo->prepare("
            SELECT
                off.*,
                f.nom AS filiere_nom,
                ca.id_condition_acces, ca.serie_bac, ca.moyenne_bac, ca.age_max, ca.diplome_requis, ca.annee_bac
            FROM offre_filiere off
            JOIN filiere f ON f.id_filiere = off.id_filiere
            LEFT JOIN condition_acces ca ON ca.id_offre_filiere = off.id_offre_filiere
            WHERE off.id_offre_filiere = ?
        ");
        $stmt->execute([$offerId]);
        return $stmt->fetch();
    }

    /**
     * Récupère toutes les filières disponibles (table de référence).
     * Utilisé pour peupler le <select> dans le formulaire d'ajout.
     */
    public function findAllPrograms(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM filiere ORDER BY nom ASC");
        return $stmt->fetchAll();
    }

    /**
     * Crée une nouvelle offre de filière pour un établissement.
     * @return int L'ID de l'offre créée
     */
    public function createOffer(
        int $institutionId,
        int $programId,
        float $tuitionFee,
        int $availableSeats,
        ?string $duration
    ): int {
        $stmt = $this->pdo->prepare("
            INSERT INTO offre_filiere (id_etablissement, id_filiere, frais_scolarite, place_disponible, duree_formation)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$institutionId, $programId, $tuitionFee, $availableSeats, $duration]);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Met à jour les informations d'une offre existante.
     */
    public function updateOffer(
        int $offerId,
        int $programId,
        float $tuitionFee,
        int $availableSeats,
        ?string $duration
    ): void {
        $stmt = $this->pdo->prepare("
            UPDATE offre_filiere
            SET id_filiere = ?, frais_scolarite = ?, place_disponible = ?, duree_formation = ?
            WHERE id_offre_filiere = ?
        ");
        $stmt->execute([$programId, $tuitionFee, $availableSeats, $duration, $offerId]);
    }

    /**
     * Supprime une offre de filière (CASCADE supprime aussi sa condition_acces).
     */
    public function deleteOffer(int $offerId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM offre_filiere WHERE id_offre_filiere = ?");
        $stmt->execute([$offerId]);
    }

    /**
     * Crée ou met à jour les conditions d'accès d'une offre (pattern upsert).
     * Une offre ne peut avoir qu'une seule condition_acces (contrainte UNIQUE).
     */
    public function upsertAccessCondition(
        int $offerId,
        ?string $series,
        ?float $minAverage,
        ?int $maxAge
    ): void {
        // Vérifier si une condition existe déjà
        $stmt = $this->pdo->prepare(
            "SELECT id_condition_acces FROM condition_acces WHERE id_offre_filiere = ?"
        );
        $stmt->execute([$offerId]);
        $existing = $stmt->fetch();

        if ($existing) {
            // Mise à jour
            $stmt = $this->pdo->prepare("
                UPDATE condition_acces
                SET serie_bac = ?, moyenne_bac = ?, age_max = ?
                WHERE id_offre_filiere = ?
            ");
            $stmt->execute([$series, $minAverage, $maxAge, $offerId]);
        } else {
            // Création
            $stmt = $this->pdo->prepare("
                INSERT INTO condition_acces (id_offre_filiere, serie_bac, moyenne_bac, age_max)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$offerId, $series, $minAverage, $maxAge]);
        }
    }

    // ─────────────────────────────────────────────
    // CÔTÉ ÉTUDIANT — Catalogue
    // ─────────────────────────────────────────────

    /**
     * Récupère toutes les offres disponibles pour le catalogue.
     * Supporte la recherche textuelle sur le nom de la filière ou de l'établissement.
     */
    public function findAllOffers(?string $search = null): array
    {
        $sql = "
            SELECT
                off.*,
                f.nom AS filiere_nom, f.description AS filiere_description,
                e.nom AS etablissement_nom, e.type AS etablissement_type,
                l.ville, l.region,
                ca.serie_bac, ca.moyenne_bac, ca.age_max
            FROM offre_filiere off
            JOIN filiere f ON f.id_filiere = off.id_filiere
            JOIN etablissement e ON e.id_etablissement = off.id_etablissement
            LEFT JOIN localisation l ON l.id_etablissement = e.id_etablissement
            LEFT JOIN condition_acces ca ON ca.id_offre_filiere = off.id_offre_filiere
            WHERE 1=1
        ";
        $params = [];

        // Si une recherche est saisie, filtrer sur le nom de la filière ou de l'établissement
        if (!empty($search)) {
            $sql .= " AND (f.nom LIKE ? OR e.nom LIKE ? OR l.ville LIKE ?)";
            $like = "%$search%";
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $sql .= " ORDER BY e.nom ASC, f.nom ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Récupère le détail complet d'une offre pour la fiche filière côté étudiant :
     * filière, établissement, localisation et conditions d'accès.
     */
    public function findOfferDetailsForStudent(int $offerId): array|false
    {
        $stmt = $this->pdo->prepare("
            SELECT
                off.*,
                f.nom AS filiere_nom, f.description AS filiere_description,
                e.id_etablissement, e.nom AS etablissement_nom, e.type AS etablissement_type,
                e.site_web, e.description AS etablissement_description,
                l.ville, l.adresse, l.region,
                ca.serie_bac, ca.moyenne_bac, ca.age_max, ca.diplome_requis, ca.annee_bac
            FROM offre_filiere off
            JOIN filiere f ON f.id_filiere = off.id_filiere
            JOIN etablissement e ON e.id_etablissement = off.id_etablissement
            LEFT JOIN localisation l ON l.id_etablissement = e.id_etablissement
            LEFT JOIN condition_acces ca ON ca.id_offre_filiere = off.id_offre_filiere
            WHERE off.id_offre_filiere = ?
        ");
        $stmt->execute([$offerId]);
        return $stmt->fetch();
    }

    // ─────────────────────────────────────────────
    // RECOMMANDATIONS — Moteur de score
    // ─────────────────────────────────────────────

    /**
     * Calcule les recommandations personnalisées pour un étudiant.
     * Score sur 100 pts selon 3 critères :
     * - Série bac compatible : 40 pts (correspondance exacte) ou 30 pts (ouvert à tous)
     * - Moyenne suffisante : 40 pts (ok) ou 20 pts (pas de condition)
     * - Places disponibles : 20 pts (>10) ou 10 pts (1-10)
     * Les offres incompatibles (série ou moyenne insuffisante) sont exclues.
     */
    public function computeAndSaveRecommendations(int $studentId, array $student): void
    {
        // 1. Supprimer les anciennes recommandations de cet étudiant
        $stmt = $this->pdo->prepare("DELETE FROM recommandation WHERE id_etudiant = ?");
        $stmt->execute([$studentId]);

        // 2. Récupérer toutes les offres avec leurs conditions d'accès
        $stmt = $this->pdo->prepare("
            SELECT
                off.id_offre_filiere, off.place_disponible,
                f.nom AS filiere_nom,
                ca.serie_bac AS serie_requise,
                ca.moyenne_bac AS moyenne_requise
            FROM offre_filiere off
            JOIN filiere f ON f.id_filiere = off.id_filiere
            LEFT JOIN condition_acces ca ON ca.id_offre_filiere = off.id_offre_filiere
        ");
        $stmt->execute();
        $offers = $stmt->fetchAll();

        // 3. Extraire le profil de l'étudiant
        $examSeries  = $student['serie'] ?? null;
        $examAverage = ($student['moyenne'] !== null) ? (float) $student['moyenne'] : null;

        $recommendations = [];

        foreach ($offers as $offer) {
            $score = 0;
            $reasons = [];

            // ── Critère 1 : Série du baccalauréat ──
            if ($offer['serie_requise'] === null) {
                // Aucune restriction de série -> ouvert à tous
                $score += 30;
                $reasons[] = "Formation ouverte à toutes les séries";
            } elseif ($offer['serie_requise'] === $examSeries) {
                // Série de l'étudiant correspond exactement à celle requise
                $score += 40;
                $reasons[] = "Série {$examSeries} requise — correspond à votre profil";
            } else {
                // Série incompatible -> on saute cette offre entièrement
                continue;
            }

            // ── Critère 2 : Moyenne du baccalauréat ──
            if ($offer['moyenne_requise'] === null) {
                // Pas de condition de moyenne
                $score += 20;
                $reasons[] = "Aucune moyenne minimale requise";
            } elseif ($examAverage !== null && $examAverage >= (float) $offer['moyenne_requise']) {
                // Moyenne de l'étudiant >= condition requise
                $score += 40;
                $diff = round($examAverage - (float) $offer['moyenne_requise'], 2);
                $reasons[] = "Votre moyenne ({$examAverage}/20) dépasse le minimum requis ({$offer['moyenne_requise']}/20) de +{$diff} pts";
            } elseif ($examAverage === null) {
                // L'étudiant n'a pas encore renseigné sa moyenne
                $score += 10;
                $reasons[] = "Renseignez votre moyenne dans votre profil pour une évaluation complète";
            } else {
                // Moyenne insuffisante -> on exclut cette offre
                continue;
            }

            // ── Critère 3 : Places disponibles (bonus) ──
            if ($offer['place_disponible'] > 10) {
                $score += 20;
                $reasons[] = "{$offer['place_disponible']} places disponibles";
            } elseif ($offer['place_disponible'] > 0) {
                $score += 10;
                $reasons[] = "{$offer['place_disponible']} place(s) disponible(s) — places limitées";
            }

            // On ne recommande que les offres avec un score positif
            if ($score > 0) {
                $recommendations[] = [
                    'id_offre_filiere' => $offer['id_offre_filiere'],
                    // min() : cap à 100 au cas où les critères bonus dépassent
                    'score' => min($score, 100),
                    'justification' => implode('. ', $reasons) . '.',
                ];
            }
        }

        // 4. Insérer toutes les recommandations en base
        if (empty($recommendations)) {
            return; // Rien à insérer
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO recommandation (id_etudiant, id_offre_filiere, score, justification)
            VALUES (?, ?, ?, ?)
        ");
        foreach ($recommendations as $r) {
            $stmt->execute([
                $studentId,
                $r['id_offre_filiere'],
                $r['score'],
                $r['justification'],
            ]);
        }
    }

    /**
     * Récupère les recommandations sauvegardées d'un étudiant, triées par score.
     */
    public function findRecommendationsByStudent(int $studentId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                r.*,
                f.nom AS filiere_nom,
                e.nom AS etablissement_nom,
                l.ville,
                off.frais_scolarite, off.place_disponible, off.duree_formation,
                ca.serie_bac, ca.moyenne_bac
            FROM recommandation r
            JOIN offre_filiere off ON off.id_offre_filiere = r.id_offre_filiere
            JOIN filiere f ON f.id_filiere = off.id_filiere
            JOIN etablissement e ON e.id_etablissement = off.id_etablissement
            LEFT JOIN localisation l ON l.id_etablissement = e.id_etablissement
            LEFT JOIN condition_acces ca ON ca.id_offre_filiere = off.id_offre_filiere
            WHERE r.id_etudiant = ?
            ORDER BY r.score DESC
        ");
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }
}
