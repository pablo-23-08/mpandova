<?php
// ═══════════════════════════════════════════════
// MODEL Admin
// Gère toutes les requêtes SQL spécifiques à l'administration :
// statistiques, utilisateurs, filières (catalogue), établissements,
// et vue globale des candidatures.
// ═══════════════════════════════════════════════

class Admin
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // ─────────────────────────────────────────────
    // Statistiques globales
    // ─────────────────────────────────────────────

    /**
     * Retourne un tableau de compteurs pour le tableau de bord admin.
     * Chaque COUNT(*) est une requête séparée : simple à lire et à maintenir.
     */
    public function getStats(): array
    {
        // fetchColumn() récupère la première colonne de la première ligne
        // Pratique pour un COUNT(*) qui retourne toujours une seule valeur

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM etudiant");
        $stats['nb_etudiants'] = (int) $stmt->fetchColumn();

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM etablissement");
        $stats['nb_etablissements'] = (int) $stmt->fetchColumn();

        // filiere = catalogue de référence (géré par l'admin)
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM filiere");
        $stats['nb_filieres'] = (int) $stmt->fetchColumn();

        // offre_filiere = filières publiées par les établissements
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM offre_filiere");
        $stats['nb_offres'] = (int) $stmt->fetchColumn();

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM candidature");
        $stats['nb_candidatures'] = (int) $stmt->fetchColumn();

        // Candidatures non encore traitées : indicateur d'activité
        $stmt = $this->pdo->query(
            "SELECT COUNT(*) FROM candidature WHERE statut = 'en_attente'"
        );
        $stats['nb_en_attente'] = (int) $stmt->fetchColumn();

        return $stats;
    }

    // ─────────────────────────────────────────────
    // Gestion des utilisateurs
    // ─────────────────────────────────────────────

    /**
     * Récupère tous les utilisateurs NON administrateurs.
     * La jointure LEFT JOIN sur etudiant et etablissement permet d'afficher
     * le nom de la personne ou de l'école selon le rôle.
     */
    public function findAllUsers(): array
    {
        $stmt = $this->pdo->query("
            SELECT
                u.id_utilisateur,
                u.email,
                u.role,
                et.nom    AS etudiant_nom,
                et.prenom AS etudiant_prenom,
                e.nom     AS etablissement_nom
            FROM utilisateur u
            LEFT JOIN etudiant      et ON et.id_utilisateur = u.id_utilisateur
            LEFT JOIN etablissement e  ON e.id_utilisateur  = u.id_utilisateur
            WHERE u.role != 'admin'
            ORDER BY u.role ASC, u.id_utilisateur DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Récupère un utilisateur précis par son ID.
     * Utilisé pour vérifier le rôle avant suppression (sécurité).
     */
    public function findUserById(int $userId): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT id_utilisateur, email, role
             FROM utilisateur
             WHERE id_utilisateur = ?"
        );
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    /**
     * Supprime un utilisateur (jamais un admin).
     * La contrainte ON DELETE CASCADE dans la BDD supprime automatiquement :
     * - Pour un étudiant : son profil, son diplôme, son bac, ses candidatures
     * - Pour un établissement : son profil, sa localisation, ses offres, ses candidatures
     * Cela garantit l'intégrité des données sans requêtes supplémentaires.
     */
    public function deleteUser(int $userId): void
    {
        // La condition AND role != 'admin' est une sécurité supplémentaire :
        // même si le Controller a déjà vérifié, la BDD est la dernière barrière.
        $stmt = $this->pdo->prepare(
            "DELETE FROM utilisateur
             WHERE id_utilisateur = ? AND role != 'admin'"
        );
        $stmt->execute([$userId]);
    }

    // ─────────────────────────────────────────────
    // Gestion du catalogue de filières
    // ─────────────────────────────────────────────

    /**
     * Récupère toutes les filières du catalogue avec le nombre d'offres liées.
     * COUNT + LEFT JOIN : si une filière n'a aucune offre, elle apparaît quand même
     * avec nb_offres = 0.
     */
    public function findAllPrograms(): array
    {
        $stmt = $this->pdo->query("
            SELECT
                f.id_filiere,
                f.nom,
                f.description,
                COUNT(off.id_offre_filiere) AS nb_offres
            FROM filiere f
            LEFT JOIN offre_filiere off ON off.id_filiere = f.id_filiere
            GROUP BY f.id_filiere
            ORDER BY f.nom ASC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Récupère une filière par son ID.
     * Utilisé pour pré-remplir le formulaire de modification.
     */
    public function findProgramById(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM filiere WHERE id_filiere = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Ajoute une nouvelle filière dans le catalogue.
     * Les établissements peuvent ensuite publier des offres sur cette filière.
     * @return int L'ID de la filière créée
     */
    public function createProgram(string $nom, ?string $description): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO filiere (nom, description) VALUES (?, ?)"
        );
        $stmt->execute([$nom, $description]);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Met à jour le nom et la description d'une filière du catalogue.
     */
    public function updateProgram(int $id, string $nom, ?string $description): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE filiere SET nom = ?, description = ? WHERE id_filiere = ?"
        );
        $stmt->execute([$nom, $description, $id]);
    }

    /**
     * Supprime une filière du catalogue.
     * Attention : la contrainte CASCADE supprime toutes les offres liées,
     * leurs conditions d'accès, et toutes les candidatures associées.
     * C'est pourquoi la vue affiche un avertissement avant suppression.
     */
    public function deleteProgram(int $id): void
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM filiere WHERE id_filiere = ?"
        );
        $stmt->execute([$id]);
    }

    // ─────────────────────────────────────────────
    // Vue globale des établissements
    // ─────────────────────────────────────────────

    /**
     * Récupère tous les établissements avec leur localisation et
     * le nombre d'offres publiées.
     * Lecture seule : l'admin observe, il ne modifie pas les profils
     * des établissements (chaque établissement gère le sien).
     */
    public function findAllInstitutions(): array
    {
        $stmt = $this->pdo->query("
            SELECT
                e.id_etablissement,
                e.nom,
                e.type,
                e.site_web,
                l.ville,
                l.region,
                u.email,
                COUNT(off.id_offre_filiere) AS nb_offres
            FROM etablissement e
            JOIN utilisateur u           ON u.id_utilisateur    = e.id_utilisateur
            LEFT JOIN localisation l     ON l.id_etablissement  = e.id_etablissement
            LEFT JOIN offre_filiere off  ON off.id_etablissement = e.id_etablissement
            GROUP BY e.id_etablissement
            ORDER BY e.nom ASC
        ");
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────────
    // Vue globale des candidatures
    // ─────────────────────────────────────────────

    /**
     * Récupère toutes les candidatures de la plateforme.
     * Supporte un filtre optionnel par statut (même logique que pour l'établissement,
     * mais sans restriction d'établissement — l'admin voit tout).
     * @param string|null $status Filtre : 'en_attente' | 'acceptee' | 'refusee' | 'annulee' | null = tous
     */
    public function findAllApplications(?string $status = null): array
    {
        $sql = "
            SELECT
                c.id_candidature,
                c.statut,
                c.date_candidature,
                c.date_traitement,
                et.nom    AS etudiant_nom,
                et.prenom AS etudiant_prenom,
                f.nom     AS filiere_nom,
                e.nom     AS etablissement_nom
            FROM candidature c
            JOIN etudiant      et  ON et.id_etudiant      = c.id_etudiant
            JOIN offre_filiere off ON off.id_offre_filiere = c.id_offre_filiere
            JOIN filiere       f   ON f.id_filiere         = off.id_filiere
            JOIN etablissement e   ON e.id_etablissement   = off.id_etablissement
            WHERE 1=1
        ";
        $params = [];

        // WHERE 1=1 : clause toujours vraie, permet d'enchaîner les AND conditionnels
        // sans vérifier si c'est le premier filtre ou pas
        if ($status !== null) {
            $sql     .= " AND c.statut = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY c.date_candidature DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}