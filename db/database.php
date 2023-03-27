<?php
class Database
{
	protected $connection = null;
	protected $HOST = null;
	protected $PORT = null;
	protected $DB = null;
	protected $USER = null;
	protected $PASSWORD = null;

	/**
	 * Es una función constructora que establece los valores de las variables de clase.
	 */
	public function __construct()
	{
		//TODO cambiar las variables
		$this->HOST = "localhost";
		$this->PORT = "5432";
		$this->DB = "prueba_bd";
		$this->USER = "postgres";
		$this->PASSWORD = "root";
	}

	/**
	 * Se conecta a la base de datos usando las credenciales almacenadas en las variables de clase
	 */
	public function connection()
	{
		//Conexión BD
		$conn = pg_connect("host=" . $this->HOST . " port=" . $this->PORT . " dbname=" . $this->DB . " user=" . $this->USER . " password=" . $this->PASSWORD . "") or die("Connection failed: " . pg_last_error());

		return $conn;
	}
}
