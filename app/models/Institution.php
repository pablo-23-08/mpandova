<?php
// ═══════════════════════════════════════════════
// MODEL Institution (anciennement "Etablissement")
// Gère les tables `etablissement` et `localisation`
// Remarque : les noms de tables/colonnes SQL restent inchangés
// ═══════════════════════════════════════════════

class Institution
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Récupère le profil complet d'un établissement avec sa localisation.
     * LEFT JOIN sur localisation : un établissement peut ne pas avoir encore de localisation.
     */
    public function findByUserId(int $userId): array|false
    {
        $stmt = $this->pdo->prepare("
            SELECT e.*, l.id_localisation, l.ville, l.adresse, l.region
            FROM etablissement e
            LEFT JOIN localisation l ON l.id_etablissement = e.id_etablissement
            WHERE e.id_utilisateur = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    /**
     * Crée un profil établissement lors de l'inscription.
     * @return int L'ID de l'établissement créé
     */
    public function create(string $name, string $type, int $userId): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO etablissement (nom, type, id_utilisateur) VALUES (?, ?, ?)"
        );
        $stmt->execute([$name, $type, $userId]);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Met à jour les informations générales d'un établissement.
     * site_web est nullable (null si l'URL est vide).
     */
    public function update(int $institutionId, string $name, string $type, ?string $website): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE etablissement SET nom = ?, type = ?, site_web = ?
            WHERE id_etablissement = ?
        ");
        $stmt->execute([$name, $type, $website ?: null, $institutionId]);
    }

    /**
     * Met à jour ou crée la localisation d'un établissement.
     * Pattern "upsert" : UPDATE si existe, INSERT sinon.
     * Évite une erreur de doublon sur la contrainte UNIQUE de id_etablissement.
     */
    public function upsertLocation(int $institutionId, ?string $city, ?string $address): void
    {
        // Vérifier si une localisation existe déjà pour cet établissement
        $stmt = $this->pdo->prepare(
            "SELECT id_localisation FROM localisation WHERE id_etablissement = ?"
        );
        $stmt->execute([$institutionId]);
        $existing = $stmt->fetch();

        if ($existing) {
            // La localisation existe -> mettre à jour
            $stmt = $this->pdo->prepare(
                "UPDATE localisation SET ville = ?, adresse = ? WHERE id_etablissement = ?"
            );
            $stmt->execute([$city ?: null, $address ?: null, $institutionId]);
        } else {
            // Pas encore de localisation -> créer
            $stmt = $this->pdo->prepare(
                "INSERT INTO localisation (id_etablissement, ville, adresse) VALUES (?, ?, ?)"
            );
            $stmt->execute([$institutionId, $city ?: null, $address ?: null]);
        }
    }

    /**
     * Retourne la liste des types valides pour la validation.
     * Source unique de vérité : si on ajoute un type ici, il est automatiquement
     * accepté dans toute l'application.
     */
    public static function validTypes(): array
    {
        return ['universite_publique', 'universite_privee', 'grande_ecole', 'institut', 'autre'];
    }
}
