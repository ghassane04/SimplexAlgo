<?php
// Inclure la définition de classe Simplex depuis un autre fichier.
require_once('simplex.php');

// Vérifier si le formulaire a été soumis via une requête POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et convertir l'entrée de la fonction objectif d'une chaîne de caractères séparée par des virgules en un tableau de nombres flottants.
    $objective = explode(',', $_POST['objective']);
    foreach ($objective as $key => $value) {
        $objective[$key] = floatval($value);
    }

    // Récupérer et traiter les contraintes saisies dans le formulaire.
    $constraintsInput = str_replace("\r", "", $_POST['constraints']); // Remplacer les retours chariot Windows par des retours Unix.
    $constraintsInput = explode("\n", $constraintsInput); // Diviser l'entrée en lignes distinctes pour chaque contrainte.

    // Convertir chaque ligne de contraintes d'une chaîne de caractères séparée par des virgules en un tableau de nombres flottants.
    $constraints = [];
    foreach ($constraintsInput as $line) {
        $lineConstraints = explode(',', $line);
        foreach ($lineConstraints as $key => $value) {
            $lineConstraints[$key] = floatval($value);
        }
        $constraints[] = $lineConstraints;
    }

    // Créer un nouvel objet Simplex en utilisant la fonction objectif et les contraintes.
    $simplex = new Simplex($objective, $constraints);

    // Résoudre le problème simplex.
    $result = $simplex->solve();

    // Gérer éventuellement le résultat, par exemple en l'affichant à l'utilisateur.
} else {
    // Rediriger vers la page principale du formulaire si la page est accédée sans envoi de données de formulaire.
    header("Location: index.php");
    exit;
}
?>
