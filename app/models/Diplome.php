<?php
// ═══════════════════════════════════════════════
// MODEL Diplome
// Gère les tables `diplome` et `bac`
// ═══════════════════════════════════════════════

class Diplome
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Crée un diplôme vierge pour un étudiant lors de son inscription.
     * annee_obtention et les infos bac seront complétées dans le profil.
     * @return int L'ID du diplôme créé
     */
    public function creerVierge(int $idEtudiant): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO diplome (nom, annee_obtention, id_etudiant) VALUES (?, ?, ?)"
        );
        // 'Baccalauréat' est le nom par défaut, l'année est null pour l'instant
        $stmt->execute(['Baccalauréat', null, $idEtudiant]);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Crée une entrée bac vide liée à un diplôme.
     * La série est fournie dès l'inscription, moyenne et mention seront ajoutées plus tard.
     */
    public function creerBacVierge(string $serie, int $idDiplome): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO bac (serie, moyenne, mention, id_diplome) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$serie, null, null, $idDiplome]);
    }

    /**
     * Met à jour l'année d'obtention dans la table diplome.
     * Null si l'année n'est pas encore renseignée (valeur 0 du formulaire).
     */
    public function mettreAJourAnnee(int $idDiplome, int $annee): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE diplome SET annee_obtention = ? WHERE id_diplome = ?"
        );
        $stmt->execute([$annee > 0 ? $annee : null, $idDiplome]);
    }

    /**
     * Met à jour les détails du bac (série, moyenne, mention).
     * La mention est calculée par Etudiant::calculerMention() avant d'être passée ici.
     */
    public function mettreAJourBac(int $idBac, string $serie, float $moyenne, ?string $mention): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE bac SET serie = ?, moyenne = ?, mention = ? WHERE id_bac = ?"
        );
        $stmt->execute([$serie, $moyenne, $mention, $idBac]);
    }
}