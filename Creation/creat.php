<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


if ($_SERVER['REQUEST_METHOD'] !== 'POST') :
    http_response_code(405);
    echo json_encode([
        'success' => 0,
        'message' => 'La méthode de demande non valide. La méthode HTTP doit être POST',
    ]);
    exit;
endif;

require '../Database.php';

$database = new Database();
$conn = $database->dbConnection();

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->matricule) || !isset($data->nom) || !isset($data->prenom) || !isset($data->filiere) || !isset($data->note)) :

    echo json_encode([
        'success' => 0,
        'message' => 'Veuillez remplir tous les champs',
    ]);
    exit;

elseif (empty(trim($data->matricule)) || empty(trim($data->nom)) || empty(trim($data->prenom)) || empty(trim($data->filiere)) || empty(trim($data->note)) ) :

    echo json_encode([
        'success' => 0,
        'message' => 'Champ vide.Veuillez remplir tous les champs.',
    ]);
    exit;

endif;

try {

    $matricule = htmlspecialchars(trim($data->matricule));
    $nom = htmlspecialchars(trim($data->nom));
    $prenom = htmlspecialchars(trim($data->prenom));
	$filiere = htmlspecialchars(trim($data->filiere));
    $note = htmlspecialchars(trim($data->note));
    


    $query = "INSERT INTO `etudiants`(matricule,nom,prenom,filiere,note) VALUES(:matricule, :nom, :prenom, :filiere, :note)";

    $stmt = $conn->prepare($query);

    $stmt->bindValue(':matricule', $matricule, PDO::PARAM_STR);
    $stmt->bindValue(':nom', $nom, PDO::PARAM_STR);
    $stmt->bindValue(':prenom', $prenom, PDO::PARAM_STR);
	$stmt->bindValue(':filiere', $filiere, PDO::PARAM_STR);
    $stmt->bindValue(':note', $note, PDO::PARAM_STR);

    if ($stmt->execute()) {

        http_response_code(200);
        echo json_encode([
            'success' => 1,
            'message' => 'Données insérées.'
        ]);
        exit;
    }
    
    echo json_encode([
        'success' => 0,
        'message' => 'Données non insérées.'
    ]);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => 0,
        'message' => $e->getMessage()
    ]);
    exit;
}