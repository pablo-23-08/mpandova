<?php
// ═══════════════════════════════════════════════
// MODEL Consultation (« Consulter »)
// Gère la table `consulter` : trace des consultations d'une offre
// de filière par un étudiant (fiche détaillée).
// Remarque : le nom de la table SQL reste inchangé
// ═══════════════════════════════════════════════

class Consultation
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Enregistre la consultation d'une offre par un étudiant.
     * Si l'étudiant a déjà consulté cette offre, la date est simplement rafraîchie
     * (clé primaire composite id_etudiant + id_offre_filiere -> pas de doublon).
     */
    public function record(int $studentId, int $offerId): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO consulter (id_etudiant, id_offre_filiere, date_consultation)
            VALUES (?, ?, NOW())
            ON DUPLICATE KEY UPDATE date_consultation = NOW()
        ");
        $stmt->execute([$studentId, $offerId]);
    }

    /**
     * Retourne le nombre de consultations reçues pour chaque offre d'un établissement.
     * Utilisé dans la liste des filières côté établissement pour indiquer leur popularité.
     * @return array<int,int> tableau associatif [id_offre_filiere => nombre de consultations]
     */
    public function countsByInstitution(int $institutionId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT c.id_offre_filiere, COUNT(*) AS nb_vues
            FROM consulter c
            JOIN offre_filiere off ON off.id_offre_filiere = c.id_offre_filiere
            WHERE off.id_etablissement = ?
            GROUP BY c.id_offre_filiere
        ");
        $stmt->execute([$institutionId]);

        $counts = [];
        foreach ($stmt->fetchAll() as $row) {
            $counts[(int) $row['id_offre_filiere']] = (int) $row['nb_vues'];
        }
        return $counts;
    }
}
