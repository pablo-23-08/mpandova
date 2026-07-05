<?php
// ═══════════════════════════════════════════════
// CONTROLLER FiliereController
// Gère : liste, ajout, modification, suppression des offres de filières
// Accessible uniquement au rôle 'etablissement'
// ═══════════════════════════════════════════════

require_once __DIR__ . "/../models/Filiere.php";
require_once __DIR__ . "/../models/Etablissement.php";

class FiliereController
{
    private PDO           $pdo;
    private Filiere       $filiereModel;
    private Etablissement $etablissementModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo                = $pdo;
        $this->filiereModel       = new Filiere($pdo);
        $this->etablissementModel = new Etablissement($pdo);
    }

    // ─────────────────────────────────────────────
    // Liste des filières de l'établissement
    // ─────────────────────────────────────────────

    /**
     * Affiche la liste de toutes les offres de filières de l'établissement connecté.
     */
    public function index(): void
    {
        check_role('etablissement');

        $etablissement = $this->etablissementModel->findByIdUtilisateur($_SESSION['id_utilisateur']);
        $offres        = $this->filiereModel->offresParEtablissement($etablissement['id_etablissement']);

        $this->render('layouts/header');
        $this->render('etablissement/filieres', [
            'etablissement' => $etablissement,
            'offres'        => $offres,
        ]);
        $this->render('layouts/footer');
    }

    // ─────────────────────────────────────────────
    // Ajout d'une nouvelle offre
    // ─────────────────────────────────────────────

    /**
     * GET  → formulaire vide d'ajout
     * POST → création de l'offre + ses conditions d'accès
     */
    public function ajouter(): void
    {
        check_role('etablissement');
        $etablissement = $this->etablissementModel->findByIdUtilisateur($_SESSION['id_utilisateur']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->traiterAjout($etablissement);
            return;
        }

        // Liste de toutes les filières pour le <select>
        $filieres = $this->filiereModel->toutesLesFilieres();

        $this->render('layouts/header');
        $this->render('etablissement/filiere_form', [
            'etablissement' => $etablissement,
            'filieres'      => $filieres,
            'offre'         => null,       // null = mode "ajout" (pas de valeurs pré-remplies)
            'mode'          => 'ajouter',
        ]);
        $this->render('layouts/footer');
    }

    /**
     * Traitement du formulaire d'ajout.
     * Privé : appelé uniquement depuis ajouter() en mode POST.
     */
    private function traiterAjout(array $etablissement): void
    {
        // Récupérer et nettoyer les données du formulaire
        $idFiliere  = (int)   ($_POST['id_filiere']       ?? 0);
        $frais      = (float) str_replace(',', '.', $_POST['frais_scolarite'] ?? 0);
        $places     = (int)   ($_POST['place_disponible'] ?? 0);
        $duree      = trim($_POST['duree_formation'] ?? '');
        $serie      = trim($_POST['serie_bac']       ?? '');
        $moyenneMin = (float) str_replace(',', '.', $_POST['moyenne_bac'] ?? 0);
        $ageMax     = (int)   ($_POST['age_max']          ?? 0);

        // Validation : la filière est obligatoire
        if ($idFiliere <= 0) {
            set_flash('error', 'Veuillez sélectionner une filière.');
            header("Location: index.php?route=etablissement/filiere-ajouter");
            exit();
        }

        try {
            // Transaction : créer l'offre ET sa condition d'accès de manière atomique
            $this->pdo->beginTransaction();

            // 1. Créer l'offre de filière
            $idOffre = $this->filiereModel->creerOffre(
                $etablissement['id_etablissement'],
                $idFiliere,
                $frais,
                $places,
                !empty($duree) ? $duree : null
            );

            // 2. Créer les conditions d'accès (toutes optionnelles)
            $this->filiereModel->upsertConditionAcces(
                $idOffre,
                !empty($serie)         ? $serie                : null,
                $moyenneMin > 0        ? $moyenneMin           : null,
                $ageMax > 0            ? $ageMax               : null
            );

            $this->pdo->commit();
            set_flash('success', 'Filière ajoutée avec succès.');
            header("Location: index.php?route=etablissement/filieres");
            exit();

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erreur ajout filière : " . $e->getMessage());
            set_flash('error', 'Une erreur est survenue. Veuillez réessayer.');
            header("Location: index.php?route=etablissement/filiere-ajouter");
            exit();
        }
    }

    // ─────────────────────────────────────────────
    // Modification d'une offre existante
    // ─────────────────────────────────────────────

    /**
     * GET  → formulaire pré-rempli avec les données actuelles de l'offre
     * POST → mise à jour de l'offre + ses conditions d'accès
     * L'ID de l'offre est passé en GET : ?route=etablissement/filiere-modifier&id=5
     */
    public function modifier(): void
    {
        check_role('etablissement');
        $etablissement = $this->etablissementModel->findByIdUtilisateur($_SESSION['id_utilisateur']);

        // Lire l'ID depuis l'URL
        $idOffre = (int) ($_GET['id'] ?? 0);
        $offre   = $this->filiereModel->findOffreById($idOffre);

        // Sécurité : vérifier que l'offre existe ET appartient à cet établissement
        // L'opérateur !== compare type ET valeur (les deux viennent de PDO donc même type int)
        if (!$offre || $offre['id_etablissement'] !== $etablissement['id_etablissement']) {
            set_flash('error', 'Filière introuvable ou accès non autorisé.');
            header("Location: index.php?route=etablissement/filieres");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->traiterModification($etablissement, $idOffre);
            return;
        }

        $filieres = $this->filiereModel->toutesLesFilieres();

        $this->render('layouts/header');
        $this->render('etablissement/filiere_form', [
            'etablissement' => $etablissement,
            'filieres'      => $filieres,
            'offre'         => $offre,     // Données pour pré-remplir le formulaire
            'mode'          => 'modifier',
        ]);
        $this->render('layouts/footer');
    }

    /**
     * Traitement de la modification.
     */
    private function traiterModification(array $etablissement, int $idOffre): void
    {
        $idFiliere  = (int)   ($_POST['id_filiere']       ?? 0);
        $frais      = (float) str_replace(',', '.', $_POST['frais_scolarite'] ?? 0);
        $places     = (int)   ($_POST['place_disponible'] ?? 0);
        $duree      = trim($_POST['duree_formation'] ?? '');
        $serie      = trim($_POST['serie_bac']       ?? '');
        $moyenneMin = (float) str_replace(',', '.', $_POST['moyenne_bac'] ?? 0);
        $ageMax     = (int)   ($_POST['age_max']          ?? 0);

        if ($idFiliere <= 0) {
            set_flash('error', 'Veuillez sélectionner une filière.');
            header("Location: index.php?route=etablissement/filiere-modifier&id={$idOffre}");
            exit();
        }

        try {
            $this->pdo->beginTransaction();

            $this->filiereModel->mettreAJourOffre(
                $idOffre, $idFiliere, $frais, $places,
                !empty($duree) ? $duree : null
            );
            $this->filiereModel->upsertConditionAcces(
                $idOffre,
                !empty($serie)  ? $serie      : null,
                $moyenneMin > 0 ? $moyenneMin : null,
                $ageMax > 0     ? $ageMax     : null
            );

            $this->pdo->commit();
            set_flash('success', 'Filière mise à jour avec succès.');
            header("Location: index.php?route=etablissement/filieres");
            exit();

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erreur modification filière : " . $e->getMessage());
            set_flash('error', 'Une erreur est survenue. Veuillez réessayer.');
            header("Location: index.php?route=etablissement/filiere-modifier&id={$idOffre}");
            exit();
        }
    }

    // ─────────────────────────────────────────────
    // Suppression d'une offre
    // ─────────────────────────────────────────────

    /**
     * Supprime une offre de filière.
     * Accès par lien GET : ?route=etablissement/filiere-supprimer&id=5
     * La suppression est CASCADE → condition_acces et candidatures liées sont aussi supprimées.
     */
    public function supprimer(): void
    {
        check_role('etablissement');
        $etablissement = $this->etablissementModel->findByIdUtilisateur($_SESSION['id_utilisateur']);

        $idOffre = (int) ($_GET['id'] ?? 0);
        $offre   = $this->filiereModel->findOffreById($idOffre);

        // Double vérification de propriété avant suppression
        if (!$offre || $offre['id_etablissement'] !== $etablissement['id_etablissement']) {
            set_flash('error', 'Filière introuvable ou accès non autorisé.');
            header("Location: index.php?route=etablissement/filieres");
            exit();
        }

        try {
            $this->filiereModel->supprimerOffre($idOffre);
            set_flash('success', 'Filière supprimée avec succès.');
        } catch (PDOException $e) {
            error_log("Erreur suppression filière : " . $e->getMessage());
            set_flash('error', 'Impossible de supprimer cette filière. Des candidatures y sont peut-être liées.');
        }

        header("Location: index.php?route=etablissement/filieres");
        exit();
    }

    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . "/../views/{$view}.php";
    }
}