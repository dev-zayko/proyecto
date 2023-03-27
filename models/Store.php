<?php
require_once('../db/database.php');

class Store
{
    protected $connection;
    protected $data = array();

    /**
     * Esta función crea un nuevo objeto de base de datos, crea una cadena de conexión y asigna el
     * cadena de conexión a la propiedad de conexión.
     */
    function __construct()
    {
        $db = new Database();
        $connString =  $db->connection();
        $this->connection = $connString;
    }

    /**
     * Devuelve todos los registros de la tabla de almacenamiento donde la columna delete_at es nula.
     * 
     * @return $data Una matriz de matrices asociativas.
     */
    public function get_stores()
    {
        $sql = "SELECT * FROM store WHERE deleted_at IS NULL ORDER BY cod_store";
        $queryRecords = pg_query($this->connection, $sql) or die("Error");
        $data = pg_fetch_all($queryRecords);
        return $data;
    }

    /**
     * Obtiene la última tienda de la base de datos.
     * 
     * @return $data La última bodega en la base de datos.
     */
    private function get_last_store()
    {
        $sql = "SELECT * FROM store WHERE id_store=(SELECT max(id_store) FROM store)";
        $queryRecords = pg_query($this->connection, $sql) or die("Error");
        $data = pg_fetch_all($queryRecords);
        return $data;
    }

    /**
     * Obtiene la última Bodega de la base de datos y, si es nula, crea una nueva Bodega con un único
     * identificación. Si no es nulo, verifica si la identificación única es la misma que la identificación única de la última Bodega, y si
     * es, crea una nueva identificación única. Si no es así, crea una nueva Bodega con la identificación única.
     * 
     * @return 1 si la query tuvo exito o 0 si no lo fue.
     */
    public function add_store()
    {
        $cod_store = '';
        $store = $this->get_last_store();
        $unique_id = uniqid();
        if (is_null($store)) {
            $cod_store = $unique_id;
        } else {
            if ($store[0]['cod_store'] == $unique_id) {
                $unique_id = uniqid();
            }
            $cod_store = $unique_id;
        }
        $new_cod_store = $cod_store;
        $sql = "INSERT INTO store (cod_store) VALUES ('$new_cod_store')";
        if (pg_query($this->connection, $sql) or die("Error")) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Actualiza la tabla de la Bodega con el nuevo valor disponible.
     * 
     * @param request es un array de los datos que estoy enviando desde la interfaz.
     * 
     * @return 1 si la query tuvo exito o 0 si no lo fue.
     */
    public function edit_store($request)
    {
        $id = $request['id'];
        $available = $request['available'];

        $sql = "UPDATE public.store 
		SET available= '$available'
		WHERE id_store='$id'";
        if (pg_query($this->connection, $sql) or die("Error")) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Actualiza la tabla de Bodega configurando la columna delete_at en la fecha y hora actual, y el
     * columna disponible a falso
     * 
     * @param request request que viene desde el controlador
     * 
     * @return 1 si la query tuvo exito o 0 si no lo fue.
     */
    public function delete_store($request)
    {
        $id = $request['id'];
        $data = date('Y-m-d h:i:s');
        $sql = "UPDATE public.store 
		SET  deleted_at='$data', 
        available=false
		WHERE id_store='$id'";
        if (pg_query($this->connection, $sql) or die("Error")) {
            return 1;
        } else {
            return 0;
        }
    }
}
