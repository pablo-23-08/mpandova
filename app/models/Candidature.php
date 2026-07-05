<?php
// ═══════════════════════════════════════════════
// MODEL Candidature
// Gère la table `candidature`
// ═══════════════════════════════════════════════

class Candidature
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
    public function soumettre(int $idEtudiant, int $idOffre, ?string $message): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO candidature (id_etudiant, id_offre_filiere, message)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$idEtudiant, $idOffre, $message]);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Vérifie si un étudiant a déjà postulé à une offre donnée.
     * Évite l'erreur MySQL de contrainte UNIQUE en vérifiant avant l'INSERT.
     */
    public function existeDeja(int $idEtudiant, int $idOffre): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT id_candidature FROM candidature
            WHERE id_etudiant = ? AND id_offre_filiere = ?
        ");
        $stmt->execute([$idEtudiant, $idOffre]);
        return (bool) $stmt->fetch();
    }

    /**
     * Récupère toutes les candidatures d'un étudiant avec les détails des offres.
     */
    public function parEtudiant(int $idEtudiant): array
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
        $stmt->execute([$idEtudiant]);
        return $stmt->fetchAll();
    }

    /**
     * Annule une candidature (statut → 'annulee').
     * Condition : la candidature doit appartenir à l'étudiant ET être encore 'en_attente'.
     * @return bool true si la mise à jour a eu lieu, false si déjà traitée ou non trouvée
     */
    public function annuler(int $idCandidature, int $idEtudiant): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE candidature
            SET statut = 'annulee'
            WHERE id_candidature = ?
              AND id_etudiant    = ?
              AND statut         = 'en_attente'
        ");
        $stmt->execute([$idCandidature, $idEtudiant]);
        // rowCount() retourne le nombre de lignes modifiées : 1 = succès, 0 = échec
        return $stmt->rowCount() > 0;
    }

    // ─────────────────────────────────────────────
    // CÔTÉ ÉTABLISSEMENT
    // ─────────────────────────────────────────────

    /**
     * Récupère les candidatures reçues par un établissement.
     * @param string|null $statut Filtre optionnel : 'en_attente' | 'acceptee' | 'refusee' | null = tous
     */
    public function parEtablissement(int $idEtablissement, ?string $statut = null): array
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
        $params = [$idEtablissement];

        // Filtre optionnel par statut
        if ($statut !== null && $statut !== 'tous') {
            $sql   .= " AND c.statut = ?";
            $params[] = $statut;
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
    public function traiter(int $idCandidature, int $idEtablissement, string $statut): bool
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
        $stmt->execute([$statut, $idCandidature, $idEtablissement]);
        return $stmt->rowCount() > 0;
    }
}