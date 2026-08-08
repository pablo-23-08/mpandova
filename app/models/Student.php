<?php
// ═══════════════════════════════════════════════
// MODEL Student (anciennement "Etudiant")
// Gère les tables `etudiant`, `diplome`, `bac`
// Remarque : les noms de tables/colonnes SQL restent inchangés
// ═══════════════════════════════════════════════

class Student
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Récupère le profil complet d'un étudiant avec ses infos bac.
     * Fait une jointure sur 3 tables : etudiant -> diplome -> bac
     * LEFT JOIN = garde l'étudiant même si diplome/bac est NULL (inscription récente)
     */
    public function findByUserId(int $userId): array|false
    {
        $stmt = $this->pdo->prepare("
            SELECT
                e.*,
                d.id_diplome,
                d.nom      AS diplome_nom,
                d.annee_obtention,
                b.id_bac,
                b.serie,
                b.moyenne,
                b.mention
            FROM etudiant e
            LEFT JOIN diplome d ON d.id_etudiant = e.id_etudiant
            LEFT JOIN bac     b ON b.id_diplome  = d.id_diplome
            WHERE e.id_utilisateur = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    /**
     * Crée un profil étudiant (nom + prénom liés à un utilisateur).
     * @return int L'ID de l'étudiant créé
     */
    public function create(string $lastName, string $firstName, int $userId): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO etudiant (nom, prenom, id_utilisateur) VALUES (?, ?, ?)"
        );
        $stmt->execute([$lastName, $firstName, $userId]);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Met à jour les informations personnelles de l'étudiant.
     * La date de naissance est nullable (null si non renseignée).
     */
    public function update(int $studentId, string $lastName, string $firstName, ?string $birthDate): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE etudiant
            SET nom = ?, prenom = ?, date_de_naissance = ?
            WHERE id_etudiant = ?
        ");
        // L'opérateur ternaire : si $birthDate est vide -> null, sinon la valeur
        $stmt->execute([$lastName, $firstName, !empty($birthDate) ? $birthDate : null, $studentId]);
    }

    /**
     * Calcule automatiquement la mention selon la moyenne.
     * Règle classique du système éducatif malgache.
     * Le ? avant float indique que la valeur peut être null.
     */
    public static function computeHonors(?float $average): ?string
    {
        // static : cette méthode peut être appelée sans instancier la classe
        // Utilisée comme utilitaire : Student::computeHonors(14.5)
        if ($average === null) return null;
        if ($average >= 16)    return 'Très bien';
        if ($average >= 14)    return 'Bien';
        if ($average >= 12)    return 'Assez bien';
        if ($average >= 10)    return 'Passable';
        return null; // Moins de 10 : recalé, pas de mention
    }
}
