<?php
// ═══════════════════════════════════════════════
// MODEL Degree (anciennement "Diplome")
// Gère les tables `diplome` et `bac`
// Remarque : les noms de tables/colonnes SQL restent inchangés
// ═══════════════════════════════════════════════

class Degree
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
    public function createBlank(int $studentId): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO diplome (nom, annee_obtention, id_etudiant) VALUES (?, ?, ?)"
        );
        // 'Baccalauréat' est le nom par défaut, l'année est null pour l'instant
        $stmt->execute(['Baccalauréat', null, $studentId]);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Crée une entrée bac vide liée à un diplôme.
     * La série est fournie dès l'inscription, moyenne et mention seront ajoutées plus tard.
     */
    public function createBlankExam(string $series, int $degreeId): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO bac (serie, moyenne, mention, id_diplome) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$series, null, null, $degreeId]);
    }

    /**
     * Met à jour l'année d'obtention dans la table diplome.
     * Null si l'année n'est pas encore renseignée (valeur 0 du formulaire).
     */
    public function updateYear(int $degreeId, int $year): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE diplome SET annee_obtention = ? WHERE id_diplome = ?"
        );
        $stmt->execute([$year > 0 ? $year : null, $degreeId]);
    }

    /**
     * Met à jour les détails du bac (série, moyenne, mention).
     * La mention est calculée par Student::computeHonors() avant d'être passée ici.
     */
    public function updateExam(int $examId, string $series, float $average, ?string $honors): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE bac SET serie = ?, moyenne = ?, mention = ? WHERE id_bac = ?"
        );
        $stmt->execute([$series, $average, $honors, $examId]);
    }
}
