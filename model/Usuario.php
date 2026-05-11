<?php
require_once __DIR__ . '/../config/Database.php';

class Usuario{
    private PDO $pdo;

    public function __construct(){
        $database = new Database();
        $this->pdo = $database->conectar();
    }

    // ✅ LOGIN
    public function login(string $correo, string $clave): ?array{

        $sql = "SELECT * FROM usuario WHERE correo = :correo LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'correo' => $correo
        ]);

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

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

    // ✅ VERIFICAR CORREO
    public function correoExiste(string $correo): bool{

        $sql = "SELECT id_usuario FROM usuario WHERE correo = :correo";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'correo' => $correo
        ]);

        return $stmt->rowCount() > 0;
    }

    // ✅ REGISTRAR USUARIO
    public function registrar(
        string $nombre,
        string $apellido,
        string $correo,
        string $clave
    ): int{

        $passwordHash = password_hash($clave, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuario(nombre, apellido, correo, clave)
                VALUES(:nombre, :apellido, :correo, :clave)";

        $stmt = $this->pdo->prepare($sql);

        if($stmt->execute([
            'nombre' => $nombre,
            'apellido' => $apellido,
            'correo' => $correo,
            'clave' => $passwordHash
        ])){
            return (int)$this->pdo->lastInsertId();
        }

        return 0;
    }
}
?>