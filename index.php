<?php
# Vérification du protocole
if($_SERVER["HTTPS"] != "on") {
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}

# Aller chercher les liens
require_once "../secrets.php";

try {
    $connexion = new PDO(
        "mysql:host=" . getenv("SECRET_SQL_SERVER") . ';dbname=' . getenv("SECRET_SQL_DB"),
        getenv("SECRET_SQL_USER"),
        getenv("SECRET_SQL_PASSWORD")
    );
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connexion->query('SET NAMES UTF8MB4'); // UTF8mb4 : Pour pouvoir encoder des émojis
    $prepare = $connexion->prepare(
        "
        SELECT
            idLien AS id,
            titreLien AS titre,
            urlLien AS url
        FROM
            website_liens
        ORDER BY
            idLien
        ");
    $prepare->execute();

    $retour = array();
    foreach ($prepare->fetchAll() as $objectKey => $objectValue) {
        $array = array();
        foreach ($objectValue as $key => $val) {
            $array[$key] = is_string($val) ? htmlentities($val, ENT_QUOTES, 'UTF-8') : $val;
        }
        $retour[$objectKey] = $array;
    }

    $prepare->closeCursor();
} catch (Exception $e) {
    $retour = array();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Liens | Tribu-Terre</title>

    <!-- Meta tags essentiels -->
    <meta property="og:title" content="Liens">
    <meta property="og:image" content="imgLogoMini.png">
    <meta
        property="og:description"
        content="Tribu-Terre, Association des Étudiants en Sciences de l'Université d'Orléans."
    >
    <meta
        name="description"
        content="Tribu-Terre, Association des Étudiants en Sciences de l'Université d'Orléans."
    >
    <meta property="og:url" content="https://link.bde-tribu-terre.fr/">
    <meta name="twitter:card" content="summary_large_image">

    <!-- Meta tags recommandés -->
    <meta property="og:site_name" content="BDE Tribu-Terre">
    <meta name="twitter:image:alt" content="Logo de Tribu-Terre">

    <!-- Meta tags recommandés -->
    <!-- <meta property="fb:app_id" content="your_app_id"> <- Il faut un token pour avoir l'ID de la page -->
    <meta name="twitter:site" content="@tributerre45">

    <!-- Bootstrap -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <!-- Feuille de style -->
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div class="page-complete">
    <header>
        <div class="text-center">
            <img
                src="imgLogoMini.png"
                alt="Logo"
            >
        </div>
    </header>
    <main>
        <div class="container text-center liens">
            <div class="row">
                <div class="col-sm-3"></div>
                <div class="col-sm-6">
                    <div class="well">
                        <?php foreach ($retour as $lien): ?>
                            <a class="btn btn-var btn-block btn-wrap-text" href="<?= $lien['url'] ?>"><?= $lien['titre'] ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-sm-3"></div>
            </div>
        </div>
    </main>
    <footer>
        <div class="container-fluid text-center" style="font-size: xx-small; line-height: 30%">
            <p>Tribu-Terre 2022 | 1A Rue de la Férollerie, 45071, Orléans Cedex 2</p>
            <p><strong>Site Tribu-Terre LINK version <?= file_get_contents("./version.txt") ?></strong></p>
            <p><small>Développé avec ❤️ par Anaël BARODINE</small></p>
        </div>
    </footer>
</div>
</body>
</html>

