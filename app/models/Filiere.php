<?php
// ═══════════════════════════════════════════════
// MODEL Filiere
// Gère les tables `filiere`, `offre_filiere`, `condition_acces`
// ═══════════════════════════════════════════════
// Ce Model sera enrichi dans les prochaines étapes du développement
// (recommandations, candidatures). Voici la base.

class Filiere
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Récupère toutes les filières disponibles.
     * Utilisé pour afficher le catalogue.
     */
    public function toutesLesEtablissements(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM filiere ORDER BY nom ASC");
        // fetchAll() retourne un tableau de tous les résultats
        return $stmt->fetchAll();
    }

    /**
     * Récupère les offres de filières d'un établissement donné.
     * Jointure pour avoir aussi le nom de la filière (pas juste son ID).
     */
    public function offresParEtablissement(int $idEtablissement): array
    {
        $stmt = $this->pdo->prepare("
            SELECT of.*, f.nom AS filiere_nom, f.description AS filiere_description
            FROM offre_filiere of
            JOIN filiere f ON f.id_filiere = of.id_filiere
            WHERE of.id_etablissement = ?
            ORDER BY f.nom ASC
        ");
        $stmt->execute([$idEtablissement]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère les recommandations pour un étudiant.
     * Jointure pour avoir les détails de l'offre et de l'établissement.
     */
    public function recommandationsParEtudiant(int $idEtudiant): array
    {
        $stmt = $this->pdo->prepare("
            SELECT r.*, f.nom AS filiere_nom, e.nom AS etablissement_nom, r.score, r.justification
            FROM recommandation r
            JOIN offre_filiere of ON of.id_offre_filiere = r.id_offre_filiere
            JOIN filiere f ON f.id_filiere = of.id_filiere
            JOIN etablissement e ON e.id_etablissement = of.id_etablissement
            WHERE r.id_etudiant = ?
            ORDER BY r.score DESC
        ");
        $stmt->execute([$idEtudiant]);
        return $stmt->fetchAll();
    }
}