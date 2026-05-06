# Mpandova

Application web d’orientation académique

## Installation

1. Cloner le projet

```bash
git clone https://github.com/pablo-23-08/mpandova.git
cd mpandova
```

2. Installer les dépendances

```bash
npm install
```

3. Lancer Tailwind en mode développement

```bash
npx @tailwindcss/cli --input assets/css/input.css --output assets/css/output.css
```

4. installer la base de donnée mpandova.sql


5. Modifier les infos de connexions à la bdd dans config/database.php


6. Lancer le serveur PHP

```bash
php -S localhost:8000
```

7. Ouvrir dans le navigateur
   http://localhost:8000/public/index.php
