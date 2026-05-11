<?php
require_once __DIR__ . '/../config/Database.php';
class Cliente

{

  private PDO $pdo;



  public function __construct()

  {

    $database = new Database();

    $this->pdo = $database->conectar();

  }



  public function listar(): array

  {

    $sql = "SELECT * FROM cliente ORDER BY id_cliente DESC";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute();



    return $stmt->fetchAll();

  }



  public function obtenerPorId(int $idCliente): ?array

  {

    $sql = "SELECT * FROM cliente WHERE id_cliente = :id_cliente LIMIT 1";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute([

      'id_cliente' => $idCliente

    ]);



    $cliente = $stmt->fetch();



    return $cliente ?: null;

  }



  public function correoExiste(string $correo, ?int $idExcluir = null): bool

  {

    if ($idExcluir !== null) {

      $sql = "SELECT id_cliente 

          FROM cliente 

          WHERE correo = :correo 

          AND id_cliente != :id_cliente 

          LIMIT 1";



      $stmt = $this->pdo->prepare($sql);

      $stmt->execute([

        'correo' => $correo,

        'id_cliente' => $idExcluir

      ]);

    } else {

      $sql = "SELECT id_cliente 

          FROM cliente 

          WHERE correo = :correo 

          LIMIT 1";



      $stmt = $this->pdo->prepare($sql);

      $stmt->execute([

        'correo' => $correo

      ]);

    }



    return (bool) $stmt->fetch();

  }



  public function registrar(array $datos): int

  {

    $sql = "INSERT INTO cliente (

          nombre,

          apellido,

          correo,

          dni,

          telefono,

          direccion,

          edad

        ) VALUES (

          :nombre,

          :apellido,

          :correo,

          :dni,

          :telefono,

          :direccion,

          :edad

        )";



    $stmt = $this->pdo->prepare($sql);



    $stmt->execute([

      'nombre' => $datos['nombre'],

      'apellido' => $datos['apellido'],

      'correo' => $datos['correo'],

      'dni' => $datos['dni'],

      'telefono' => $datos['telefono'],

      'direccion' => $datos['direccion'],

      'edad' => $datos['edad']

    ]);



    return (int) $this->pdo->lastInsertId();

  }



  public function actualizar(int $idCliente, array $datos): bool

  {

    $sql = "UPDATE cliente SET

          nombre = :nombre,

          apellido = :apellido,

          correo = :correo,

          dni = :dni,

          telefono = :telefono,

          direccion = :direccion,

          edad = :edad

        WHERE id_cliente = :id_cliente";



    $stmt = $this->pdo->prepare($sql);



    return $stmt->execute([

      'nombre' => $datos['nombre'],

      'apellido' => $datos['apellido'],

      'correo' => $datos['correo'],

      'dni' => $datos['dni'],

      'telefono' => $datos['telefono'],

      'direccion' => $datos['direccion'],

      'edad' => $datos['edad'],

      'id_cliente' => $idCliente

    ]);

  }



  public function eliminar(int $idCliente): bool

  {

    $sql = "DELETE FROM cliente WHERE id_cliente = :id_cliente";

    $stmt = $this->pdo->prepare($sql);



    return $stmt->execute([

      'id_cliente' => $idCliente

    ]);

  }

}

