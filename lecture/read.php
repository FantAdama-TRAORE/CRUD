
<?php
//L'entête Access-Control-Allow-Origin renvoie une réponse indiquant si les ressources peuvent être partagées avec une origine(le schéma (protocole)) donnée.
header("Access-Control-Allow-Origin: *");
//L’en-tête de réponseAccess-Control-Allow-Headersest utilisé en réponse à une demande decontrôle en amontqui inclut lesen-têtes Access-Control-Request-Headers pour indiquer quels en-têtesHTTP peuvent être utilisés pendant la demande réelle.
header("Access-Control-Allow-Headers: access");
//En-tête de réponseAccess-Control-Allow-Methods Spécifie une ou plusieurs méthodes autorisées lors de l’accès à une ressource en réponse à unedemande
header("Access-Control-Allow-Methods: GET");
//En-tête de réponseAccess-Control-Allow-Credentials indique aux navigateurs s’ils doivent exposer la réponse au code JavaScript frontal lorsque le le mode d’informations d’identification de la demande (Request.credentials) est.include
header("Access-Control-Allow-Credentials: true");
//Content-Typeest utilisé pour indiquer le type de médiad ( format)origine de la ressource (avant tout codage de contenu appliqué pour l’envoi)
header("Content-Type: application/json; charset=UTF-8");

//REQUEST_METHODvariable peut être utilisée chaque fois que vous devez déterminer le type de requête HTTP.
if ($_SERVER['REQUEST_METHOD'] !== 'GET') :
    //Les erreurs HTTP 405 sont causées lorsqu'une méthode HTTP n'est pas autorisée par un serveur web pour une URL demandée. Cette condition est souvent observée lorsqu'un gestionnaire particulier a été défini pour un verbe spécifique et que ce gestionnaire substitue le gestionnaire que vous attendez à traiter la requête.
    http_response_code(405);
    //Retourne une chaîne contenant la représentation JSON de la valeur value.
    echo json_encode([
        'status' => 0,
        'message' => 'La méthode de demande non valide. La méthode HTTP doit être GET',
    ]);
    exit;
endif;
//Pour inclure un fichier dans un autre à l'aide de PHP, on place l'instruction require 
require '../Database.php';

$database = new Database();
$conn = $database->dbConnection();
$post_id = null;
// if(isset)=utilisé pour récupérer une valeur d’un tableau étant donné que isset retourne true
if (isset($_GET['matricule'])) {
    $post_id = filter_var($_GET['matricule'], FILTER_VALIDATE_INT, [
        'options' => [
            'default' => 'all_posts',
            'min_range' => 1
        ]
    ]);
}

try {

    $sql = "SELECT * FROM etudiants";

    $stmt = $conn->prepare($sql);

    $stmt->execute();

    if ($stmt->rowCount() > 0) :

        $data = null;
        if (is_numeric($post_id)) {
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode([
            'status' => 1,
            'data' => $data,
        ]);

    else :
        echo json_encode([
            'status' => 0,
            'message' => 'Aucun resultat !',
        ]);
    endif;
    //catch est utlisé seulement en cas d'erreur;PDOExeception capture l erreur 
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 0,
        'message' => $e->getMessage()
    ]);
    exit;
}