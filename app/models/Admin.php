<?php
// ═══════════════════════════════════════════════
// MODEL Admin
// Gère toutes les requêtes SQL spécifiques à l'administration :
// statistiques, utilisateurs, filières (catalogue), établissements,
// formations (offres), candidatures et activités récentes.
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
     */
    public function getStats(): array
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM utilisateur");
        $stats['nb_utilisateurs'] = (int) $stmt->fetchColumn();

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM etudiant");
        $stats['nb_etudiants'] = (int) $stmt->fetchColumn();

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM etablissement");
        $stats['nb_etablissements'] = (int) $stmt->fetchColumn();

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM utilisateur WHERE role = 'admin'");
        $stats['nb_admins'] = (int) $stmt->fetchColumn();

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM filiere");
        $stats['nb_filieres'] = (int) $stmt->fetchColumn();

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM offre_filiere");
        $stats['nb_offres'] = (int) $stmt->fetchColumn();

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM candidature");
        $stats['nb_candidatures'] = (int) $stmt->fetchColumn();

        $stmt = $this->pdo->query(
            "SELECT COUNT(*) FROM candidature WHERE statut = 'en_attente'"
        );
        $stats['nb_en_attente'] = (int) $stmt->fetchColumn();

        return $stats;
    }

    /**
     * Récupère les candidatures récentes (5 dernières).
     */
    public function getRecentApplications(int $limit = 5): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                c.id_candidature,
                c.statut,
                c.date_candidature,
                et.nom    AS etudiant_nom,
                et.prenom AS etudiant_prenom,
                f.nom     AS filiere_nom,
                e.nom     AS etablissement_nom
            FROM candidature c
            JOIN etudiant      et  ON et.id_etudiant      = c.id_etudiant
            JOIN offre_filiere off ON off.id_offre_filiere = c.id_offre_filiere
            JOIN filiere       f   ON f.id_filiere         = off.id_filiere
            JOIN etablissement e   ON e.id_etablissement   = off.id_etablissement
            ORDER BY c.date_candidature DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère les établissements récemment inscrits (5 derniers).
     */
    public function getRecentInstitutions(int $limit = 5): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                e.id_etablissement,
                e.nom,
                e.type,
                u.email,
                COUNT(off.id_offre_filiere) AS nb_offres
            FROM etablissement e
            JOIN utilisateur u ON u.id_utilisateur = e.id_utilisateur
            LEFT JOIN offre_filiere off ON off.id_etablissement = e.id_etablissement
            GROUP BY e.id_etablissement
            ORDER BY e.id_etablissement DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────────
    // Gestion des utilisateurs
    // ─────────────────────────────────────────────

    /**
     * Récupère tous les utilisateurs avec filtre optionnel par rôle et recherche.
     */
    public function findAllUsers(?string $role = null, ?string $search = null): array
    {
        $sql = "
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
            WHERE 1=1
        ";
        $params = [];

        if ($role && $role !== 'tous') {
            $sql     .= " AND u.role = ?";
            $params[] = $role;
        }

        if ($search) {
            $sql .= " AND (u.email LIKE ? OR et.nom LIKE ? OR et.prenom LIKE ? OR e.nom LIKE ?)";
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $sql .= " ORDER BY u.role ASC, u.id_utilisateur DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Récupère un utilisateur précis par son ID avec ses informations de profil.
     */
    public function findUserById(int $userId): array|false
    {
        $stmt = $this->pdo->prepare("
            SELECT
                u.id_utilisateur,
                u.email,
                u.role,
                et.nom          AS etudiant_nom,
                et.prenom       AS etudiant_prenom,
                et.telephone    AS etudiant_telephone,
                et.date_de_naissance,
                e.nom           AS etablissement_nom,
                e.type          AS etablissement_type,
                e.site_web,
                l.ville,
                l.adresse,
                l.region
            FROM utilisateur u
            LEFT JOIN etudiant      et ON et.id_utilisateur = u.id_utilisateur
            LEFT JOIN etablissement e  ON e.id_utilisateur  = u.id_utilisateur
            LEFT JOIN localisation  l  ON l.id_etablissement = e.id_etablissement
            WHERE u.id_utilisateur = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    /**
     * Crée un nouvel utilisateur (admin, étudiant, ou établissement).
     * @return int L'ID de l'utilisateur créé
     */
    public function createUser(string $email, string $password, string $role): int
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare(
            "INSERT INTO utilisateur (email, mot_de_passe_hash, role) VALUES (?, ?, ?)"
        );
        $stmt->execute([$email, $hash, $role]);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Vérifie si un email est déjà utilisé.
     */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        if ($excludeId) {
            $stmt = $this->pdo->prepare(
                "SELECT id_utilisateur FROM utilisateur WHERE email = ? AND id_utilisateur != ?"
            );
            $stmt->execute([$email, $excludeId]);
        } else {
            $stmt = $this->pdo->prepare(
                "SELECT id_utilisateur FROM utilisateur WHERE email = ?"
            );
            $stmt->execute([$email]);
        }
        return (bool) $stmt->fetch();
    }

    /**
     * Met à jour l'email d'un utilisateur.
     */
    public function updateUserEmail(int $userId, string $email): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE utilisateur SET email = ? WHERE id_utilisateur = ?"
        );
        $stmt->execute([$email, $userId]);
    }

    /**
     * Met à jour le rôle d'un utilisateur.
     */
    public function updateUserRole(int $userId, string $role): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE utilisateur SET role = ? WHERE id_utilisateur = ?"
        );
        $stmt->execute([$role, $userId]);
    }

    /**
     * Réinitialise le mot de passe d'un utilisateur.
     */
    public function resetUserPassword(int $userId, string $newPassword): void
    {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare(
            "UPDATE utilisateur SET mot_de_passe_hash = ? WHERE id_utilisateur = ?"
        );
        $stmt->execute([$hash, $userId]);
    }

    /**
     * Supprime un utilisateur (jamais un admin).
     */
    public function deleteUser(int $userId): void
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM utilisateur WHERE id_utilisateur = ? AND role != 'admin'"
        );
        $stmt->execute([$userId]);
    }

    // ─────────────────────────────────────────────
    // Gestion des établissements
    // ─────────────────────────────────────────────

    /**
     * Récupère tous les établissements avec filtre de recherche.
     */
    public function findAllInstitutions(?string $search = null): array
    {
        $sql = "
            SELECT
                e.id_etablissement,
                e.nom,
                e.type,
                e.site_web,
                e.description,
                l.ville,
                l.region,
                l.adresse,
                u.email,
                u.id_utilisateur,
                COUNT(off.id_offre_filiere) AS nb_offres
            FROM etablissement e
            JOIN utilisateur u           ON u.id_utilisateur    = e.id_utilisateur
            LEFT JOIN localisation l     ON l.id_etablissement  = e.id_etablissement
            LEFT JOIN offre_filiere off  ON off.id_etablissement = e.id_etablissement
            WHERE 1=1
        ";
        $params = [];

        if ($search) {
            $sql .= " AND (e.nom LIKE ? OR u.email LIKE ? OR l.ville LIKE ?)";
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $sql .= " GROUP BY e.id_etablissement ORDER BY e.nom ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Récupère un établissement par son ID avec toutes ses informations.
     */
    public function findInstitutionById(int $id): array|false
    {
        $stmt = $this->pdo->prepare("
            SELECT
                e.*,
                l.id_localisation, l.ville, l.adresse, l.region,
                u.email, u.id_utilisateur,
                COUNT(DISTINCT off.id_offre_filiere) AS nb_offres,
                COUNT(DISTINCT c.id_candidature)     AS nb_candidatures
            FROM etablissement e
            JOIN utilisateur u           ON u.id_utilisateur    = e.id_utilisateur
            LEFT JOIN localisation l     ON l.id_etablissement  = e.id_etablissement
            LEFT JOIN offre_filiere off  ON off.id_etablissement = e.id_etablissement
            LEFT JOIN candidature c      ON c.id_offre_filiere  = off.id_offre_filiere
            WHERE e.id_etablissement = ?
            GROUP BY e.id_etablissement
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Récupère les formations d'un établissement.
     */
    public function findInstitutionPrograms(int $institutionId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                off.id_offre_filiere,
                off.frais_scolarite,
                off.place_disponible,
                off.duree_formation,
                f.nom AS filiere_nom,
                f.description AS filiere_description,
                COUNT(c.id_candidature) AS nb_candidatures
            FROM offre_filiere off
            JOIN filiere f ON f.id_filiere = off.id_filiere
            LEFT JOIN candidature c ON c.id_offre_filiere = off.id_offre_filiere
            WHERE off.id_etablissement = ?
            GROUP BY off.id_offre_filiere
            ORDER BY f.nom ASC
        ");
        $stmt->execute([$institutionId]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère les candidatures reçues par un établissement.
     */
    public function findInstitutionApplications(int $institutionId, int $limit = 10): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                c.id_candidature,
                c.statut,
                c.date_candidature,
                et.nom    AS etudiant_nom,
                et.prenom AS etudiant_prenom,
                f.nom     AS filiere_nom
            FROM candidature c
            JOIN etudiant      et  ON et.id_etudiant      = c.id_etudiant
            JOIN offre_filiere off ON off.id_offre_filiere = c.id_offre_filiere
            JOIN filiere       f   ON f.id_filiere         = off.id_filiere
            WHERE off.id_etablissement = ?
            ORDER BY c.date_candidature DESC
            LIMIT ?
        ");
        $stmt->execute([$institutionId, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Met à jour les informations d'un établissement.
     */
    public function updateInstitution(
        int     $id,
        string  $nom,
        string  $type,
        ?string $siteWeb,
        ?string $description
    ): void {
        $stmt = $this->pdo->prepare("
            UPDATE etablissement
            SET nom = ?, type = ?, site_web = ?, description = ?
            WHERE id_etablissement = ?
        ");
        $stmt->execute([$nom, $type, $siteWeb, $description, $id]);
    }

    /**
     * Met à jour ou crée la localisation d'un établissement.
     */
    public function upsertInstitutionLocation(
        int     $institutionId,
        ?string $ville,
        ?string $adresse,
        ?string $region
    ): void {
        $stmt = $this->pdo->prepare("
            INSERT INTO localisation (id_etablissement, ville, adresse, region)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE ville = VALUES(ville), adresse = VALUES(adresse), region = VALUES(region)
        ");
        $stmt->execute([$institutionId, $ville, $adresse, $region]);
    }

    /**
     * Supprime un établissement (et son compte utilisateur via CASCADE).
     */
    public function deleteInstitution(int $institutionId): void
    {
        // Récupère l'id_utilisateur pour supprimer l'utilisateur (cascade supprimera l'établissement)
        $stmt = $this->pdo->prepare(
            "SELECT id_utilisateur FROM etablissement WHERE id_etablissement = ?"
        );
        $stmt->execute([$institutionId]);
        $row = $stmt->fetch();
        if ($row) {
            $stmt = $this->pdo->prepare(
                "DELETE FROM utilisateur WHERE id_utilisateur = ? AND role != 'admin'"
            );
            $stmt->execute([$row['id_utilisateur']]);
        }
    }

    // ─────────────────────────────────────────────
    // Gestion du catalogue de filières
    // ─────────────────────────────────────────────

    /**
     * Récupère toutes les filières du catalogue avec recherche.
     */
    public function findAllPrograms(?string $search = null): array
    {
        $sql = "
            SELECT
                f.id_filiere,
                f.nom,
                f.description,
                COUNT(DISTINCT off.id_offre_filiere) AS nb_offres,
                COUNT(DISTINCT m.id_debouche)        AS nb_debouches
            FROM filiere f
            LEFT JOIN offre_filiere off ON off.id_filiere = f.id_filiere
            LEFT JOIN mener m           ON m.id_filiere   = f.id_filiere
            WHERE 1=1
        ";
        $params = [];

        if ($search) {
            $sql .= " AND f.nom LIKE ?";
            $params[] = '%' . $search . '%';
        }

        $sql .= " GROUP BY f.id_filiere ORDER BY f.nom ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Récupère une filière par son ID.
     */
    public function findProgramById(int $id): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM filiere WHERE id_filiere = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Ajoute une nouvelle filière dans le catalogue.
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
     */
    public function deleteProgram(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM filiere WHERE id_filiere = ?");
        $stmt->execute([$id]);
    }

    // ─────────────────────────────────────────────
    // Gestion des formations (offres)
    // ─────────────────────────────────────────────

    /**
     * Récupère toutes les formations (offres) avec filtres.
     */
    public function findAllOffers(
        ?string $search = null,
        ?int    $institutionId = null,
        ?string $level = null
    ): array {
        $sql = "
            SELECT
                off.id_offre_filiere,
                off.frais_scolarite,
                off.place_disponible,
                off.duree_formation,
                f.nom  AS filiere_nom,
                f.id_filiere,
                e.id_etablissement,
                e.nom  AS etablissement_nom,
                ca.diplome_requis,
                ca.serie_bac,
                ca.moyenne_bac,
                COUNT(c.id_candidature) AS nb_candidatures
            FROM offre_filiere off
            JOIN filiere       f   ON f.id_filiere         = off.id_filiere
            JOIN etablissement e   ON e.id_etablissement   = off.id_etablissement
            LEFT JOIN condition_acces ca ON ca.id_offre_filiere = off.id_offre_filiere
            LEFT JOIN candidature     c  ON c.id_offre_filiere  = off.id_offre_filiere
            WHERE 1=1
        ";
        $params = [];

        if ($search) {
            $sql .= " AND (f.nom LIKE ? OR e.nom LIKE ?)";
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
        }

        if ($institutionId) {
            $sql     .= " AND off.id_etablissement = ?";
            $params[] = $institutionId;
        }

        if ($level) {
            $sql     .= " AND ca.diplome_requis LIKE ?";
            $params[] = '%' . $level . '%';
        }

        $sql .= " GROUP BY off.id_offre_filiere ORDER BY e.nom ASC, f.nom ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Récupère une offre de formation par son ID (détail complet).
     */
    public function findOfferById(int $offerId): array|false
    {
        $stmt = $this->pdo->prepare("
            SELECT
                off.*,
                f.nom AS filiere_nom,
                f.description AS filiere_description,
                e.id_etablissement,
                e.nom AS etablissement_nom,
                e.type AS etablissement_type,
                u.email AS etablissement_email,
                ca.id_condition_acces,
                ca.diplome_requis,
                ca.serie_bac,
                ca.moyenne_bac,
                ca.age_max,
                ca.annee_bac,
                COUNT(c.id_candidature) AS nb_candidatures
            FROM offre_filiere off
            JOIN filiere       f   ON f.id_filiere        = off.id_filiere
            JOIN etablissement e   ON e.id_etablissement  = off.id_etablissement
            JOIN utilisateur   u   ON u.id_utilisateur    = e.id_utilisateur
            LEFT JOIN condition_acces ca ON ca.id_offre_filiere = off.id_offre_filiere
            LEFT JOIN candidature     c  ON c.id_offre_filiere  = off.id_offre_filiere
            WHERE off.id_offre_filiere = ?
            GROUP BY off.id_offre_filiere
        ");
        $stmt->execute([$offerId]);
        return $stmt->fetch();
    }

    /**
     * Récupère les débouchés d'une filière.
     */
    public function findProgramOutlets(int $filiereId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT d.nom, d.description, m.niveau_etude
            FROM mener m
            JOIN debouche d ON d.id_debouche = m.id_debouche
            WHERE m.id_filiere = ?
            ORDER BY d.nom ASC
        ");
        $stmt->execute([$filiereId]);
        return $stmt->fetchAll();
    }

    /**
     * Met à jour une offre de formation (admin peut modifier les infos de base).
     */
    public function updateOffer(
        int    $offerId,
        float  $fraisScolarite,
        int    $placeDisponible,
        string $dureeFormation
    ): void {
        $stmt = $this->pdo->prepare("
            UPDATE offre_filiere
            SET frais_scolarite = ?, place_disponible = ?, duree_formation = ?
            WHERE id_offre_filiere = ?
        ");
        $stmt->execute([$fraisScolarite, $placeDisponible, $dureeFormation, $offerId]);
    }

    /**
     * Supprime une offre de formation.
     */
    public function deleteOffer(int $offerId): void
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM offre_filiere WHERE id_offre_filiere = ?"
        );
        $stmt->execute([$offerId]);
    }

    // ─────────────────────────────────────────────
    // Vue globale des candidatures
    // ─────────────────────────────────────────────

    /**
     * Récupère toutes les candidatures avec filtres multiples.
     */
    public function findAllApplications(
        ?string $status = null,
        ?int    $institutionId = null,
        ?int    $offerId = null,
        ?string $search = null,
        ?string $dateFrom = null,
        ?string $dateTo = null
    ): array {
        $sql = "
            SELECT
                c.id_candidature,
                c.statut,
                c.date_candidature,
                c.date_traitement,
                c.message,
                et.id_etudiant,
                et.nom    AS etudiant_nom,
                et.prenom AS etudiant_prenom,
                eu.email  AS etudiant_email,
                f.nom     AS filiere_nom,
                e.id_etablissement,
                e.nom     AS etablissement_nom,
                off.id_offre_filiere,
                off.duree_formation
            FROM candidature c
            JOIN etudiant      et  ON et.id_etudiant      = c.id_etudiant
            JOIN utilisateur   eu  ON eu.id_utilisateur   = et.id_utilisateur
            JOIN offre_filiere off ON off.id_offre_filiere = c.id_offre_filiere
            JOIN filiere       f   ON f.id_filiere         = off.id_filiere
            JOIN etablissement e   ON e.id_etablissement   = off.id_etablissement
            WHERE 1=1
        ";
        $params = [];

        if ($status !== null && $status !== 'tous') {
            $sql     .= " AND c.statut = ?";
            $params[] = $status;
        }

        if ($institutionId) {
            $sql     .= " AND off.id_etablissement = ?";
            $params[] = $institutionId;
        }

        if ($offerId) {
            $sql     .= " AND c.id_offre_filiere = ?";
            $params[] = $offerId;
        }

        if ($search) {
            $sql .= " AND (et.nom LIKE ? OR et.prenom LIKE ? OR eu.email LIKE ? OR f.nom LIKE ? OR e.nom LIKE ?)";
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        if ($dateFrom) {
            $sql     .= " AND DATE(c.date_candidature) >= ?";
            $params[] = $dateFrom;
        }

        if ($dateTo) {
            $sql     .= " AND DATE(c.date_candidature) <= ?";
            $params[] = $dateTo;
        }

        $sql .= " ORDER BY c.date_candidature DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Récupère une candidature par son ID (détail complet).
     */
    public function findApplicationById(int $id): array|false
    {
        $stmt = $this->pdo->prepare("
            SELECT
                c.*,
                et.nom          AS etudiant_nom,
                et.prenom       AS etudiant_prenom,
                et.telephone    AS etudiant_telephone,
                et.date_de_naissance,
                eu.email        AS etudiant_email,
                f.nom           AS filiere_nom,
                f.description   AS filiere_description,
                f.id_filiere,
                e.id_etablissement,
                e.nom           AS etablissement_nom,
                e.type          AS etablissement_type,
                eu2.email       AS etablissement_email,
                off.duree_formation,
                off.frais_scolarite,
                off.place_disponible,
                ca.diplome_requis,
                ca.serie_bac,
                ca.moyenne_bac
            FROM candidature c
            JOIN etudiant      et  ON et.id_etudiant       = c.id_etudiant
            JOIN utilisateur   eu  ON eu.id_utilisateur    = et.id_utilisateur
            JOIN offre_filiere off ON off.id_offre_filiere  = c.id_offre_filiere
            JOIN filiere       f   ON f.id_filiere          = off.id_filiere
            JOIN etablissement e   ON e.id_etablissement    = off.id_etablissement
            JOIN utilisateur   eu2 ON eu2.id_utilisateur   = e.id_utilisateur
            LEFT JOIN condition_acces ca ON ca.id_offre_filiere = off.id_offre_filiere
            WHERE c.id_candidature = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Statistiques des candidatures par statut.
     */
    public function getApplicationStats(): array
    {
        $stmt = $this->pdo->query("
            SELECT statut, COUNT(*) AS total
            FROM candidature
            GROUP BY statut
        ");
        $rows = $stmt->fetchAll();
        $stats = ['en_attente' => 0, 'acceptee' => 0, 'refusee' => 0, 'annulee' => 0];
        foreach ($rows as $row) {
            $stats[$row['statut']] = (int) $row['total'];
        }
        return $stats;
    }

    /**
     * Récupère tous les établissements (pour les selects/filtres).
     */
    public function getInstitutionsForSelect(): array
    {
        $stmt = $this->pdo->query("SELECT id_etablissement, nom FROM etablissement ORDER BY nom ASC");
        return $stmt->fetchAll();
    }

    /**
     * Récupère toutes les offres d'un établissement (pour les selects/filtres).
     */
    public function getOffersForSelect(?int $institutionId = null): array
    {
        if ($institutionId) {
            $stmt = $this->pdo->prepare("
                SELECT off.id_offre_filiere, f.nom AS filiere_nom, e.nom AS etablissement_nom
                FROM offre_filiere off
                JOIN filiere f ON f.id_filiere = off.id_filiere
                JOIN etablissement e ON e.id_etablissement = off.id_etablissement
                WHERE off.id_etablissement = ?
                ORDER BY f.nom ASC
            ");
            $stmt->execute([$institutionId]);
        } else {
            $stmt = $this->pdo->query("
                SELECT off.id_offre_filiere, f.nom AS filiere_nom, e.nom AS etablissement_nom
                FROM offre_filiere off
                JOIN filiere f ON f.id_filiere = off.id_filiere
                JOIN etablissement e ON e.id_etablissement = off.id_etablissement
                ORDER BY e.nom ASC, f.nom ASC
            ");
        }
        return $stmt->fetchAll();
    }
}
