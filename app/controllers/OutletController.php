<?php
// ═══════════════════════════════════════════════
// CONTROLLER OutletController
// Gère : liste, ajout, modification et suppression des débouchés professionnels
// rattachés à une filière (tables `debouche` et `mener`).
// Séparé de ProgramController pour respecter le principe de responsabilité
// unique : ProgramController gère l'offre (offre_filiere), OutletController
// gère uniquement les débouchés de la filière associée à cette offre.
// ═══════════════════════════════════════════════

require_once __DIR__ . "/../models/Outlet.php";
require_once __DIR__ . "/../models/Program.php";
require_once __DIR__ . "/../models/Institution.php";

class OutletController
{
    private PDO $pdo;
    private Outlet $outletModel;
    private Program $programModel;
    private Institution $institutionModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo              = $pdo;
        $this->outletModel      = new Outlet($pdo);
        $this->programModel     = new Program($pdo);
        $this->institutionModel = new Institution($pdo);
    }

    // ─────────────────────────────────────────────
    // Liste des débouchés d'une filière
    // ─────────────────────────────────────────────

    /**
     * Affiche la liste des débouchés professionnels de la filière rattachée
     * à l'offre passée en paramètre (?id=<id_offre_filiere>), ainsi que le
     * formulaire d'ajout.
     */
    public function index(): void
    {
        check_role('etablissement');

        $offer = $this->getOwnedOfferOrRedirect();

        $outlets = $this->outletModel->findByProgram((int) $offer['id_filiere']);

        $this->render('layouts/header');
        $this->render('institution/outlets', [
            'offre'    => $offer,
            'debouches' => $outlets,
            'modeEdition' => null, // aucun débouché en cours de modification
        ]);
        $this->render('layouts/footer');
    }

    // ─────────────────────────────────────────────
    // Ajout d'un débouché
    // ─────────────────────────────────────────────

    /**
     * Traite le formulaire d'ajout d'un débouché (nom, description, niveau d'étude).
     */
    public function store(): void
    {
        check_role('etablissement');

        $offer = $this->getOwnedOfferOrRedirect();
        [$nom, $description, $niveau] = $this->readOutletInput();

        if ($nom === '' || $niveau === '') {
            set_flash('error', 'Le nom du débouché et le niveau d\'étude sont obligatoires.');
            header("Location: index.php?route=institution/program/outlets&id={$offer['id_offre_filiere']}");
            exit();
        }

        try {
            $this->outletModel->createForProgram((int) $offer['id_filiere'], $nom, $description !== '' ? $description : null, $niveau);
            set_flash('success', 'Débouché ajouté avec succès.');
        } catch (PDOException $e) {
            error_log("Erreur ajout débouché : " . $e->getMessage());
            set_flash('error', 'Une erreur est survenue. Veuillez réessayer.');
        }

        header("Location: index.php?route=institution/program/outlets&id={$offer['id_offre_filiere']}");
        exit();
    }

    // ─────────────────────────────────────────────
    // Modification d'un débouché
    // ─────────────────────────────────────────────

    /**
     * GET  -> ré-affiche la liste avec le formulaire pré-rempli pour le débouché ciblé
     * POST -> enregistre les modifications (nom, description, niveau d'étude)
     */
    public function edit(): void
    {
        check_role('etablissement');

        $offer    = $this->getOwnedOfferOrRedirect();
        $outletId = (int) ($_GET['id_debouche'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$nom, $description, $niveau] = $this->readOutletInput();

            if ($nom === '' || $niveau === '') {
                set_flash('error', 'Le nom du débouché et le niveau d\'étude sont obligatoires.');
                header("Location: index.php?route=institution/program/outlets/edit&id={$offer['id_offre_filiere']}&id_debouche={$outletId}");
                exit();
            }

            try {
                $this->outletModel->updateForProgram(
                    (int) $offer['id_filiere'],
                    $outletId,
                    $nom,
                    $description !== '' ? $description : null,
                    $niveau
                );
                set_flash('success', 'Débouché mis à jour avec succès.');
            } catch (PDOException $e) {
                error_log("Erreur modification débouché : " . $e->getMessage());
                set_flash('error', 'Une erreur est survenue. Veuillez réessayer.');
            }

            header("Location: index.php?route=institution/program/outlets&id={$offer['id_offre_filiere']}");
            exit();
        }

        $outlet = $this->outletModel->findOneByProgram((int) $offer['id_filiere'], $outletId);

        if (!$outlet) {
            set_flash('error', 'Débouché introuvable pour cette filière.');
            header("Location: index.php?route=institution/program/outlets&id={$offer['id_offre_filiere']}");
            exit();
        }

        $outlets = $this->outletModel->findByProgram((int) $offer['id_filiere']);

        $this->render('layouts/header');
        $this->render('institution/outlets', [
            'offre'       => $offer,
            'debouches'   => $outlets,
            'modeEdition' => $outlet, // débouché actuellement en cours de modification
        ]);
        $this->render('layouts/footer');
    }

    // ─────────────────────────────────────────────
    // Suppression d'un débouché
    // ─────────────────────────────────────────────

    /**
     * Retire le débouché de la filière (et le supprime définitivement s'il
     * n'est plus rattaché à aucune autre filière).
     */
    public function delete(): void
    {
        check_role('etablissement');

        $offer    = $this->getOwnedOfferOrRedirect();
        $outletId = (int) ($_GET['id_debouche'] ?? 0);

        try {
            $this->outletModel->removeFromProgram((int) $offer['id_filiere'], $outletId);
            set_flash('success', 'Débouché supprimé avec succès.');
        } catch (PDOException $e) {
            error_log("Erreur suppression débouché : " . $e->getMessage());
            set_flash('error', 'Impossible de supprimer ce débouché. Veuillez réessayer.');
        }

        header("Location: index.php?route=institution/program/outlets&id={$offer['id_offre_filiere']}");
        exit();
    }

    // ─────────────────────────────────────────────
    // Aides internes
    // ─────────────────────────────────────────────

    /**
     * Récupère l'offre de filière ciblée (?id=<id_offre_filiere>) en vérifiant
     * qu'elle appartient bien à l'établissement connecté. Redirige sinon.
     */
    private function getOwnedOfferOrRedirect(): array
    {
        $institution = $this->institutionModel->findByUserId($_SESSION['id_utilisateur']);
        $offerId     = (int) ($_GET['id'] ?? 0);
        $offer       = $this->programModel->findOfferById($offerId);

        if (!$offer || $offer['id_etablissement'] !== $institution['id_etablissement']) {
            set_flash('error', 'Filière introuvable ou accès non autorisé.');
            header("Location: index.php?route=institution/programs");
            exit();
        }

        return $offer;
    }

    /**
     * Lit et nettoie les 3 champs du formulaire de débouché : nom, description, niveau d'étude.
     * @return array{0: string, 1: string, 2: string}
     */
    private function readOutletInput(): array
    {
        $nom         = trim($_POST['nom'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $niveau      = trim($_POST['niveau_etude'] ?? '');

        return [$nom, $description, $niveau];
    }

    // ─────────────────────────────────────────────
    // Rendu des vues
    // ─────────────────────────────────────────────

    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . "/../views/{$view}.php";
    }
}
