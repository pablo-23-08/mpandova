<?php
// ═══════════════════════════════════════════════
// MODEL User (anciennement "Utilisateur")
// Gère la table `utilisateur` (connexion, inscription, mot de passe)
// Remarque : le nom de la table SQL reste inchangé (structure de la BDD conservée)
// ═══════════════════════════════════════════════

class User
{
    // La connexion PDO est stockée comme propriété de l'objet
    // Elle est injectée depuis le Controller (injection de dépendances)
    private PDO $pdo;

    // Constructeur : appelé automatiquement quand on fait new User($pdo)
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Recherche un utilisateur par son email.
     * Utilisé lors de la connexion.
     * @return array|false Le tableau des données ou false si non trouvé
     */
    public function findByEmail(string $email): array|false
    {
        // Requête préparée : le ? est remplacé de façon sécurisée par $email
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(); // Retourne false si aucun résultat
    }

    /**
     * Vérifie si un email est déjà utilisé dans la base.
     * Utilisé lors de l'inscription pour éviter les doublons.
     */
    public function emailExists(string $email): bool
    {
        $stmt = $this->pdo->prepare("SELECT id_utilisateur FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        // fetch() retourne false si rien trouvé, donc (bool) false = false, (bool) array = true
        return (bool) $stmt->fetch();
    }

    /**
     * Crée un nouvel utilisateur en base.
     * @return int L'ID auto-incrémenté du nouvel utilisateur
     */
    public function create(string $email, string $password, string $role): int
    {
        // Hachage du mot de passe : bcrypt par défaut via PASSWORD_DEFAULT
        // Le hash est stocké, jamais le mot de passe en clair
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare(
            "INSERT INTO utilisateur (email, mot_de_passe_hash, role) VALUES (?, ?, ?)"
        );
        $stmt->execute([$email, $hash, $role]);

        // lastInsertId() retourne l'ID généré par AUTO_INCREMENT
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Met à jour le mot de passe d'un utilisateur.
     * Utilisé dans les pages de profil.
     */
    public function updatePassword(int $userId, string $newPassword): void
    {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare(
            "UPDATE utilisateur SET mot_de_passe_hash = ? WHERE id_utilisateur = ?"
        );
        $stmt->execute([$hash, $userId]);
    }

    /**
     * Enregistre une session en base de données.
     * Appelé après une connexion réussie.
     * REPLACE INTO = INSERT si n'existe pas, UPDATE si existe déjà (même session_id).
     */
    public function saveSession(string $sessionId, int $userId, string $role): void
    {
        $stmt = $this->pdo->prepare(
            "REPLACE INTO session (id_session, id_utilisateur, role, initial) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$sessionId, $userId, $role, time()]);
    }

    /**
     * Supprime une session de la base de données.
     * Appelé lors de la déconnexion.
     */
    public function deleteSession(string $sessionId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM session WHERE id_session = ?");
        $stmt->execute([$sessionId]);
    }
}
