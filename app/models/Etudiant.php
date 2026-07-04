<?php
// ═══════════════════════════════════════════════
// MODEL Etudiant
// Gère les tables `etudiant`, `diplome`, `bac`
// ═══════════════════════════════════════════════

class Etudiant
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Récupère le profil complet d'un étudiant avec ses infos bac.
     * Fait une jointure sur 3 tables : etudiant → diplome → bac
     * LEFT JOIN = garde l'étudiant même si diplome/bac est NULL (inscription récente)
     */
    public function findByIdUtilisateur(int $idUtilisateur): array|false
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
        $stmt->execute([$idUtilisateur]);
        return $stmt->fetch();
    }

    /**
     * Crée un profil étudiant (nom + prénom liés à un utilisateur).
     * @return int L'ID de l'étudiant créé
     */
    public function creer(string $nom, string $prenom, int $idUtilisateur): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO etudiant (nom, prenom, id_utilisateur) VALUES (?, ?, ?)"
        );
        $stmt->execute([$nom, $prenom, $idUtilisateur]);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Met à jour les informations personnelles de l'étudiant.
     * La date de naissance est nullable (null si non renseignée).
     */
    public function mettreAJour(int $idEtudiant, string $nom, string $prenom, ?string $dateNaissance): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE etudiant
            SET nom = ?, prenom = ?, date_de_naissance = ?
            WHERE id_etudiant = ?
        ");
        // L'opérateur ternaire : si $dateNaissance est vide → null, sinon la valeur
        $stmt->execute([$nom, $prenom, !empty($dateNaissance) ? $dateNaissance : null, $idEtudiant]);
    }

    /**
     * Calcule automatiquement la mention selon la moyenne.
     * Règle classique du système éducatif malgache.
     * Le ? avant float indique que la valeur peut être null.
     */
    public static function calculerMention(?float $moyenne): ?string
    {
        // static : cette méthode peut être appelée sans instancier la classe
        // Utilisée comme utilitaire : Etudiant::calculerMention(14.5)
        if ($moyenne === null) return null;
        if ($moyenne >= 16)    return 'Très bien';
        if ($moyenne >= 14)    return 'Bien';
        if ($moyenne >= 12)    return 'Assez bien';
        if ($moyenne >= 10)    return 'Passable';
        return null; // Moins de 10 : recalé, pas de mention
    }
}