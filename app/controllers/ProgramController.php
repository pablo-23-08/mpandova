<?php
// ═══════════════════════════════════════════════
// CONTROLLER ProgramController (anciennement "FiliereController")
// Gère : liste, ajout, modification et suppression des offres de filières
// ═══════════════════════════════════════════════

require_once __DIR__ . "/../models/Program.php";
require_once __DIR__ . "/../models/Institution.php";

class ProgramController
{
    private PDO $pdo;
    private Program $programModel;
    private Institution $institutionModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo              = $pdo;
        $this->programModel     = new Program($pdo);
        $this->institutionModel = new Institution($pdo);
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

        $institution = $this->institutionModel->findByUserId($_SESSION['id_utilisateur']);
        $offers      = $this->programModel->findOffersByInstitution($institution['id_etablissement']);

        $this->render('layouts/header');
        $this->render('institution/programs', [
            'etablissement' => $institution,
            'offres'        => $offers,
        ]);
        $this->render('layouts/footer');
    }

    // ─────────────────────────────────────────────
    // Ajout d'une nouvelle offre
    // ─────────────────────────────────────────────

    /**
     * GET  -> formulaire vide d'ajout
     * POST -> création de l'offre + ses conditions d'accès
     */
    public function create(): void
    {
        check_role('etablissement');

        $institution = $this->institutionModel->findByUserId($_SESSION['id_utilisateur']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processCreate($institution);
            return;
        }

        // Liste de toutes les filières pour le <select>
        $programs = $this->programModel->findAllPrograms();

        $this->render('layouts/header');
        $this->render('institution/program_form', [
            'etablissement' => $institution,
            'filieres'      => $programs,
            'offre'         => null, // null = mode "ajout" (pas de valeurs pré-remplies)
            'mode'          => 'ajouter',
        ]);
        $this->render('layouts/footer');
    }

    /**
     * Traitement du formulaire d'ajout.
     * Privé : appelé uniquement depuis create() en mode POST.
     */
    private function processCreate(array $institution): void
    {
        // Récupérer et nettoyer les données du formulaire
        $programId  = (int) ($_POST['id_filiere'] ?? 0);
        $tuitionFee = (float) str_replace(',', '.', $_POST['frais_scolarite'] ?? 0);
        $seats      = (int) ($_POST['place_disponible'] ?? 0);
        $duration   = trim($_POST['duree_formation'] ?? '');
        $series     = trim($_POST['serie_bac'] ?? '');
        $minAverage = (float) str_replace(',', '.', $_POST['moyenne_bac'] ?? 0);
        $maxAge     = (int) ($_POST['age_max'] ?? 0);

        // Validation : la filière est obligatoire
        if ($programId <= 0) {
            set_flash('error', 'Veuillez sélectionner une filière');
            header("Location: index.php?route=institution/program/create");
            exit();
        }

        try {
            // Toutes les opérations sont regroupées dans une transaction
            $this->pdo->beginTransaction();

            // 1. Créer l'offre de filière
            $offerId = $this->programModel->createOffer(
                $institution['id_etablissement'],
                $programId,
                $tuitionFee,
                $seats,
                !empty($duration) ? $duration : null
            );

            // 2. Créer les conditions d'accès (toutes optionnelles)
            $this->programModel->upsertAccessCondition(
                $offerId,
                !empty($series) ? $series : null,
                $minAverage > 0 ? $minAverage : null,
                $maxAge > 0 ? $maxAge : null
            );

            $this->pdo->commit();
            set_flash('success', 'Filière ajoutée avec succès');
            header("Location: index.php?route=institution/programs");
            exit();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erreur ajout filière : " . $e->getMessage());
            set_flash('error', 'Une erreur est survenue. Veuillez réessayer');
            header("Location: index.php?route=institution/program/create");
            exit();
        }
    }

    // ─────────────────────────────────────────────
    // Modification d'une offre existante
    // ─────────────────────────────────────────────

    /**
     * GET  -> formulaire pré-rempli avec les données actuelles de l'offre
     * POST -> mise à jour de l'offre + ses conditions d'accès
     * L'ID de l'offre est passé en GET : ?route=etablissement/filiere-modifier&id=5
     */
    public function edit(): void
    {
        check_role('etablissement');

        $institution = $this->institutionModel->findByUserId($_SESSION['id_utilisateur']);

        // Lire l'ID depuis l'URL
        $offerId = (int) ($_GET['id'] ?? 0);
        $offer   = $this->programModel->findOfferById($offerId);

        // Sécurité : vérifier que l'offre existe ET appartient à cet établissement
        // L'opérateur !== compare type ET valeur (les deux viennent de PDO donc même type int)
        if (!$offer || $offer['id_etablissement'] !== $institution['id_etablissement']) {
            set_flash('error', 'Filière introuvable ou accès non autorisé');
            header("Location: index.php?route=institution/programs");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processEdit($institution, $offerId);
            return;
        }

        $programs = $this->programModel->findAllPrograms();

        $this->render('layouts/header');
        $this->render('institution/program_form', [
            'etablissement' => $institution,
            'filieres'      => $programs,
            'offre'         => $offer, // Données pour pré-remplir le formulaire
            'mode'          => 'modifier',
        ]);
        $this->render('layouts/footer');
    }

    /**
     * Traitement de la modification.
     */
    private function processEdit(array $institution, int $offerId): void
    {
        $programId  = (int) ($_POST['id_filiere'] ?? 0);
        $tuitionFee = (float) str_replace(',', '.', $_POST['frais_scolarite'] ?? 0);
        $seats      = (int) ($_POST['place_disponible'] ?? 0);
        $duration   = trim($_POST['duree_formation'] ?? '');
        $series     = trim($_POST['serie_bac'] ?? '');
        $minAverage = (float) str_replace(',', '.', $_POST['moyenne_bac'] ?? 0);
        $maxAge     = (int) ($_POST['age_max'] ?? 0);

        if ($programId <= 0) {
            set_flash('error', 'Veuillez sélectionner une filière.');
            header("Location: index.php?route=institution/program/edit&id={$offerId}");
            exit();
        }

        try {
            $this->pdo->beginTransaction();

            $this->programModel->updateOffer(
                $offerId,
                $programId,
                $tuitionFee,
                $seats,
                !empty($duration) ? $duration : null
            );

            $this->programModel->upsertAccessCondition(
                $offerId,
                !empty($series) ? $series : null,
                $minAverage > 0 ? $minAverage : null,
                $maxAge > 0 ? $maxAge : null
            );

            $this->pdo->commit();
            set_flash('success', 'Filière mise à jour avec succès.');
            header("Location: index.php?route=institution/programs");
            exit();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erreur modification filière : " . $e->getMessage());
            set_flash('error', 'Une erreur est survenue. Veuillez réessayer.');
            header("Location: index.php?route=institution/program/edit&id={$offerId}");
            exit();
        }
    }

    // ─────────────────────────────────────────────
    // Suppression d'une offre
    // ─────────────────────────────────────────────

    /**
     * Supprime une offre de filière.
     * Accès par lien GET : ?route=etablissement/filiere-supprimer&id=5
     * La suppression est CASCADE -> condition_acces et candidatures liées sont aussi supprimées.
     */
    public function delete(): void
    {
        check_role('etablissement');

        $institution = $this->institutionModel->findByUserId($_SESSION['id_utilisateur']);

        $offerId = (int) ($_GET['id'] ?? 0);
        $offer   = $this->programModel->findOfferById($offerId);

        // Double vérification de propriété avant suppression
        if (!$offer || $offer['id_etablissement'] !== $institution['id_etablissement']) {
            set_flash('error', 'Filière introuvable ou accès non autorisé.');
            header("Location: index.php?route=institution/programs");
            exit();
        }

        try {
            $this->programModel->deleteOffer($offerId);
            set_flash('success', 'Filière supprimée avec succès.');
        } catch (PDOException $e) {
            error_log("Erreur suppression filière : " . $e->getMessage());
            set_flash('error', 'Impossible de supprimer cette filière. Des candidatures y sont peut-être liées');
        }

        header("Location: index.php?route=institution/programs");
        exit();
    }

    // ─────────────────────────────────────────────
    // Rendu des vues
    // ─────────────────────────────────────────────

    /**
     * Inclut un fichier de vue en lui rendant disponibles les données fournies.
     * Les clés du tableau $data restent volontairement en français : ce sont
     * les noms de variables attendus par les vues (contrat d'interface inchangé).
     */
    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . "/../views/{$view}.php";
    }
}
