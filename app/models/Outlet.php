<?php
// ═══════════════════════════════════════════════
// MODEL Outlet (« Débouché »)
// Gère les tables `debouche` et `mener`
// Remarque : les noms de tables/colonnes SQL restent inchangés
// ═══════════════════════════════════════════════

class Outlet
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Récupère les débouchés professionnels liés à une filière, avec le niveau
     * d'étude nécessaire pour y accéder (table de liaison `mener`).
     * Utilisé sur la fiche détaillée d'une filière côté étudiant, ainsi que
     * sur la page de gestion des débouchés côté établissement.
     */
    public function findByProgram(int $programId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                deb.id_debouche,
                deb.nom,
                deb.description,
                m.niveau_etude
            FROM mener m
            JOIN debouche deb ON deb.id_debouche = m.id_debouche
            WHERE m.id_filiere = ?
            ORDER BY m.niveau_etude ASC, deb.nom ASC
        ");
        $stmt->execute([$programId]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère un débouché précis lié à une filière (avec son niveau d'étude).
     * Utilisé pour pré-remplir le formulaire de modification d'un débouché.
     */
    public function findOneByProgram(int $programId, int $outletId): array|false
    {
        $stmt = $this->pdo->prepare("
            SELECT
                deb.id_debouche,
                deb.nom,
                deb.description,
                m.niveau_etude
            FROM mener m
            JOIN debouche deb ON deb.id_debouche = m.id_debouche
            WHERE m.id_filiere = ? AND deb.id_debouche = ?
        ");
        $stmt->execute([$programId, $outletId]);
        return $stmt->fetch();
    }

    /**
     * Crée un nouveau débouché (table `debouche`) et le relie immédiatement
     * à une filière avec son niveau d'étude requis (table `mener`).
     * Regroupe la création + la liaison : c'est l'opération "ajouter un débouché"
     * utilisée par l'établissement lors de la création/modification d'une filière.
     * @return int L'ID du débouché créé
     */
    public function createForProgram(int $programId, string $nom, ?string $description, string $niveauEtude): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO debouche (nom, description) VALUES (?, ?)");
        $stmt->execute([$nom, $description]);
        $outletId = (int) $this->pdo->lastInsertId();

        $stmt = $this->pdo->prepare("
            INSERT INTO mener (id_filiere, id_debouche, niveau_etude)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$programId, $outletId, $niveauEtude]);

        return $outletId;
    }

    /**
     * Met à jour un débouché existant : ses informations (nom, description)
     * ainsi que le niveau d'étude nécessaire pour la filière concernée.
     */
    public function updateForProgram(int $programId, int $outletId, string $nom, ?string $description, string $niveauEtude): void
    {
        $stmt = $this->pdo->prepare("UPDATE debouche SET nom = ?, description = ? WHERE id_debouche = ?");
        $stmt->execute([$nom, $description, $outletId]);

        $stmt = $this->pdo->prepare("
            UPDATE mener SET niveau_etude = ?
            WHERE id_filiere = ? AND id_debouche = ?
        ");
        $stmt->execute([$niveauEtude, $programId, $outletId]);
    }

    /**
     * Compte le nombre de filières encore liées à un débouché.
     * Utilisé pour savoir si un débouché peut être supprimé définitivement
     * (« orphelin ») une fois son lien avec une filière retiré.
     */
    public function countLinks(int $outletId): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM mener WHERE id_debouche = ?");
        $stmt->execute([$outletId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Retire le lien entre une filière et un débouché (table `mener`).
     * Si le débouché n'est plus rattaché à aucune filière, il est aussi
     * supprimé de la table `debouche` pour ne pas laisser de données orphelines.
     */
    public function removeFromProgram(int $programId, int $outletId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM mener WHERE id_filiere = ? AND id_debouche = ?");
        $stmt->execute([$programId, $outletId]);

        if ($this->countLinks($outletId) === 0) {
            $stmt = $this->pdo->prepare("DELETE FROM debouche WHERE id_debouche = ?");
            $stmt->execute([$outletId]);
        }
    }
}
