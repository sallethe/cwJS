#!/bin/bash

set -e

echo "== Ce script a besoin de sudo. Veuillez entrer votre mot de passe."
echo "   Si vous n'êtes pas un sudoer, veuillez appuyer sur Ctrl+C."
sudo echo "-> sudo validé"

DEFAULT_DESTINATION="/var/www/html/cwJS/"

read -rp "> Veuillez entrer la destination [$DEFAULT_DESTINATION]: " destination_path
destination_path=${destination_path:-$DEFAULT_DESTINATION}

if [[ ! -d "$destination_path" ]]; then
  echo "-> '$destination_path' n'existe pas. Création..."
  sudo mkdir -p "$destination_path/cwJS/" || {
    echo "/!\\ Une erreur est survenue pendant la création du dossier. Sortie..."
    exit 1
  }
fi

echo "-> Configuration de la base de données..."
sudo mysql < "./_setup/setup.sql" || {
     echo "/!\\ Une erreur est survenue pendant la configuration de la base de données. Sortie..."
     exit 1
}

echo "-> Déplacement du dossier..."

sudo cp ./* "$destination_path/cwJS/" || {
    echo "/!\\ Une erreur est survenue pendant le déplacement. Sortie..."
  exit 1
}

echo "-> Réussite. Vous pouvez dés maintenant aller sur http://localhost/cwJS/."
