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
     * Utilisé sur la fiche détaillée d'une filière côté étudiant.
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
}
