<?php
require_once __DIR__ . '/../config/Database.php';

class Usuario{
    private PDO $pdo;

    public function __construct(){
        $database = new Database();
        $this->pdo = $database->conectar();
    }

    public function login(string $nombre, string $clave): ?array{

        $sql = "SELECT * FROM usuario WHERE nombre = :nombre LIMIT 1";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'nombre' => $nombre
        ]);

        $usuario = $stmt->fetch();

        if(!$usuario){
            return null;
        }

        if(
            $clave === $usuario['clave'] ||
            password_verify($clave, $usuario['clave'])
        ){
            return $usuario;
        }

        return null;
    }
}
?>