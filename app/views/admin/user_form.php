<?php
// $modeEdition : null (ajout) | tableau (modification)
$isEdit  = !is_null($modeEdition);
$title   = $isEdit ? 'Modifier l\'utilisateur' : 'Ajouter un utilisateur';
$action  = $isEdit ? 'index.php?route=admin/user/edit' : 'index.php?route=admin/user/add';

$email = htmlspecialchars($modeEdition['email'] ?? '');
$role  = $modeEdition['role'] ?? 'etudiant';
?>
<main class="mx-auto w-full max-w-2xl flex-1 px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-white/20 bg-white/95 p-6 shadow-2xl shadow-[#071d3b]/25 sm:p-8">

        <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-[#071d3b] sm:text-3xl">
                    <?= $title ?>
                </h1>
                <?php if ($isEdit): ?>
                    <p class="mt-1 text-xs text-slate-400">ID #<?= $modeEdition['id_utilisateur'] ?></p>
                <?php endif; ?>
            </div>
            <a href="index.php?route=admin/users"
               class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-[#071d3b] hover:border-[#f1b456]">
                Retour
            </a>
        </div>

        <!-- Règle importante -->
        <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
            <strong>⚠️ Règle de sécurité :</strong> La création d'un compte <strong>Administrateur</strong> n'est possible
            que depuis cet espace. L'inscription publique est limitée aux rôles Étudiant et Établissement.
        </div>

        <form method="POST" action="<?= $action ?>" novalidate class="mt-6 space-y-5">

            <?php if ($isEdit): ?>
                <input type="hidden" name="id_utilisateur" value="<?= $modeEdition['id_utilisateur'] ?>">
            <?php endif; ?>

            <!-- Email -->
            <div>
                <label for="email" class="mb-2 block text-sm font-semibold text-[#071d3b]">
                    Adresse email <span class="text-rose-500">*</span>
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                    value="<?= $email ?>"
                    placeholder="exemple@domaine.com"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30"
                >
            </div>

            <!-- Rôle -->
            <div>
                <label for="role" class="mb-2 block text-sm font-semibold text-[#071d3b]">
                    Rôle <span class="text-rose-500">*</span>
                </label>
                <select
                    id="role"
                    name="role"
                    required
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30"
                >
                    <option value="etudiant"       <?= $role === 'etudiant'       ? 'selected' : '' ?>>Étudiant</option>
                    <option value="etablissement"  <?= $role === 'etablissement'  ? 'selected' : '' ?>>Établissement</option>
                    <option value="admin"          <?= $role === 'admin'          ? 'selected' : '' ?>>Administrateur</option>
                </select>
            </div>

            <!-- Mot de passe -->
            <div>
                <label for="password" class="mb-2 block text-sm font-semibold text-[#071d3b]">
                    <?= $isEdit ? 'Nouveau mot de passe' : 'Mot de passe' ?>
                    <?= !$isEdit ? '<span class="text-rose-500">*</span>' : '' ?>
                    <?= $isEdit ? '<span class="text-xs font-normal text-slate-400">(laisser vide pour ne pas changer)</span>' : '' ?>
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    <?= !$isEdit ? 'required' : '' ?>
                    minlength="8"
                    placeholder="Minimum 8 caractères"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30"
                >
            </div>

            <!-- Confirmation mot de passe -->
            <div>
                <label for="password_confirm" class="mb-2 block text-sm font-semibold text-[#071d3b]">
                    Confirmer le mot de passe
                    <?= !$isEdit ? '<span class="text-rose-500">*</span>' : '' ?>
                </label>
                <input
                    type="password"
                    id="password_confirm"
                    name="password_confirm"
                    <?= !$isEdit ? 'required' : '' ?>
                    placeholder="Répétez le mot de passe"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#f1b456] focus:ring-2 focus:ring-[#f1b456]/30"
                >
            </div>

            <div class="flex flex-wrap gap-3 pt-2">
                <button
                    type="submit"
                    class="rounded-lg bg-[#f1b456] px-6 py-3 text-sm font-bold text-[#071d3b] hover:bg-[#e4a744]"
                >
                    <?= $isEdit ? 'Enregistrer les modifications' : 'Créer le compte' ?>
                </button>
                <a href="index.php?route=admin/users"
                   class="rounded-lg border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 hover:border-[#f1b456]">
                    Annuler
                </a>
            </div>
        </form>

    </section>
</main>
