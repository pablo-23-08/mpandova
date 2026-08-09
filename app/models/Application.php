<?php
// ═══════════════════════════════════════════════
// MODEL Application (anciennement "Candidature")
// Gère la table `candidature`
// Remarque : les noms de tables/colonnes SQL restent inchangés
// ═══════════════════════════════════════════════

class Application
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // ─────────────────────────────────────────────
    // CÔTÉ ÉTUDIANT
    // ─────────────────────────────────────────────

    /**
     * Soumet une nouvelle candidature.
     * @return int L'ID de la candidature créée
     * @throws PDOException Si l'étudiant a déjà postulé à cette offre (contrainte UNIQUE)
     */
    public function submit(int $studentId, int $offerId, ?string $message): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO candidature (id_etudiant, id_offre_filiere, message)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$studentId, $offerId, $message]);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Vérifie si un étudiant a déjà postulé à une offre donnée.
     * Évite l'erreur MySQL de contrainte UNIQUE en vérifiant avant l'INSERT.
     */
    public function alreadyExists(int $studentId, int $offerId): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT id_candidature FROM candidature
            WHERE id_etudiant = ? AND id_offre_filiere = ?
        ");
        $stmt->execute([$studentId, $offerId]);
        return (bool) $stmt->fetch();
    }

    /**
     * Récupère la candidature d'un étudiant pour une offre précise (si elle existe).
     * Utilisé sur la fiche détaillée d'une filière pour afficher le statut et le
     * message déjà envoyé au lieu de proposer à nouveau le formulaire de candidature.
     */
    public function findOne(int $studentId, int $offerId): array|false
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM candidature
            WHERE id_etudiant = ? AND id_offre_filiere = ?
        ");
        $stmt->execute([$studentId, $offerId]);
        return $stmt->fetch();
    }

    /**
     * Récupère toutes les candidatures d'un étudiant avec les détails des offres.
     */
    public function findByStudent(int $studentId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                c.*,
                f.nom  AS filiere_nom,
                e.nom  AS etablissement_nom,
                off.duree_formation,
                off.frais_scolarite
            FROM candidature c
            JOIN offre_filiere  off ON off.id_offre_filiere  = c.id_offre_filiere
            JOIN filiere        f  ON f.id_filiere         = off.id_filiere
            JOIN etablissement  e  ON e.id_etablissement   = off.id_etablissement
            WHERE c.id_etudiant = ?
            ORDER BY c.date_candidature DESC
        ");
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }

    /**
     * Annule une candidature (statut -> 'annulee').
     * Condition : la candidature doit appartenir à l'étudiant ET être encore 'en_attente'.
     * @return bool true si la mise à jour a eu lieu, false si déjà traitée ou non trouvée
     */
    public function cancel(int $applicationId, int $studentId): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE candidature
            SET statut = 'annulee'
            WHERE id_candidature = ?
              AND id_etudiant    = ?
              AND statut         = 'en_attente'
        ");
        $stmt->execute([$applicationId, $studentId]);
        // rowCount() retourne le nombre de lignes modifiées : 1 = succès, 0 = échec
        return $stmt->rowCount() > 0;
    }

    // ─────────────────────────────────────────────
    // CÔTÉ ÉTABLISSEMENT
    // ─────────────────────────────────────────────

    /**
     * Récupère les candidatures reçues par un établissement.
     * @param string|null $status Filtre optionnel : 'en_attente' | 'acceptee' | 'refusee' | null = tous
     */
    public function findByInstitution(int $institutionId, ?string $status = null): array
    {
        $sql = "
            SELECT
                c.*,
                et.nom    AS etudiant_nom,
                et.prenom AS etudiant_prenom,
                b.serie, b.moyenne,
                f.nom     AS filiere_nom
            FROM candidature c
            JOIN etudiant      et ON et.id_etudiant      = c.id_etudiant
            LEFT JOIN diplome  d  ON d.id_etudiant       = et.id_etudiant
            LEFT JOIN bac      b  ON b.id_diplome        = d.id_diplome
            JOIN offre_filiere off ON off.id_offre_filiere = c.id_offre_filiere
            JOIN filiere       f  ON f.id_filiere        = off.id_filiere
            WHERE off.id_etablissement = ?
        ";
        $params = [$institutionId];

        // Filtre optionnel par statut
        if ($status !== null && $status !== 'tous') {
            $sql   .= " AND c.statut = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY c.date_candidature DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Accepte ou refuse une candidature.
     * SECURITE : le multi-table UPDATE vérifie que la candidature appartient
     * bien à cet établissement via la jointure offre_filiere.
     * @return bool true si la mise à jour a eu lieu
     */
    public function process(int $applicationId, int $institutionId, string $status): bool
    {
        // Multi-table UPDATE : UPDATE d'une table en joignant une autre
        // Syntaxe MySQL spécifique : UPDATE table1 JOIN table2 ON ... SET table1.col = ?
        $stmt = $this->pdo->prepare("
            UPDATE candidature c
            JOIN offre_filiere off ON off.id_offre_filiere = c.id_offre_filiere
            SET c.statut          = ?,
                c.date_traitement = NOW()
            WHERE c.id_candidature    = ?
              AND off.id_etablissement = ?
              AND c.statut            = 'en_attente'
        ");
        $stmt->execute([$status, $applicationId, $institutionId]);
        return $stmt->rowCount() > 0;
    }
}
