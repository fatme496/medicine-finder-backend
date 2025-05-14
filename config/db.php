<?php
require_once __DIR__ . '/../vendor/autoload.php'; // corrected path

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$conn = null;

if ($_SERVER["HTTP_HOST"] === "localhost") {

    $hostname = $_ENV["DB_HOST"];
    $username = $_ENV["DB_USER"];
    $password = $_ENV["DB_PASS"];
    $dbname = $_ENV["DB_NAME"];

    try {
        $conn = new mysqli($hostname, $username, $password, $dbname);

        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        // else {
        //     echo "✅ Local MySQL connected successfully!";
        // }

    } catch (Exception $e) {
        die("Local DB Connection Error: " . $e->getMessage());
    }
} else {

    $uri = $_ENV["DB_URI"];

    $fields = parse_url($uri);

    // build the DSN including SSL settings
    // $conn = "mysql:";
    // $conn .= "host=" . $fields["host"];
    // $conn .= ";port=" . $fields["port"];;
    // $conn .= ";dbname=".$_ENV["DBNAME"];
    // $conn .= $_ENV["SSL_MODE"];
    $hostname = $fields["host"];
    $username = $fields["user"];
    $password = $fields["pass"];
    $dbname = ltrim($fields["path"], '/');
    $port = $fields["port"];


    try {
        // $db = new PDO($conn, $fields["user"], $fields["pass"]);
        $conn = new mysqli($hostname, $username, $password, $dbname, $port);

        //Testing Connection
        // $stmt = $db->query("SELECT VERSION()");
        // print($stmt->fetch()[0]);
        // echo "✅ Online DB connected successfully!";

        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }else {
            echo "✅ Online MySQL connected successfully!";
        }

    } catch (Exception $e) {
        die("Online DB Connection Error: " . $e->getMessage());
    }
}

return $conn;

?>