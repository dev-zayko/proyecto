<?php
include("../db/database.php");

class Product
{
	protected $connection;
	protected $data = array();

	/**
	 * Crea un nuevo objeto de base de datos, obtiene la cadena de conexión del objeto de base de datos y asigna
	 * a la propiedad de conexión del objeto actual
	 */
	function __construct()
	{
		$db = new Database();
		$connString =  $db->connection();
		$this->connection = $connString;
	}

	/**
	 * Devuelve todos los productos de la base de datos, y si el producto ha sido eliminado, no será
	 * devuelto
	 * 
	 * @return $data datos desde la query.
	 */
	public function get_products()
	{
		$sql = "SELECT 
		id_product,
		 cod_product, name, price, stock, product.created_at, product.deleted_at, store_id, cod_store, store.created_at as store_created_at, store.deleted_at as store_deleted_at FROM product INNER JOIN store ON product.store_id = store.id_store WHERE  product.deleted_at IS NULL  ORDER BY cod_product";
		$queryRecords = pg_query($this->connection, $sql) or die("Error");
		$data = pg_fetch_all($queryRecords);
		return $data;
	}

	/**
	 * Devuelve todos los productos de una Bodega.
	 * 
	 * @param id_store El id de la bodega
	 * 
	 * @return $data array de productos.
	 */
	public function get_products_store($id_store)
	{
		$sql = "SELECT * FROM public.product WHERE store_id = '$id_store' AND deleted_at IS NULL";
		$queryRecords = pg_query($this->connection, $sql) or die("Error");
		$data = pg_fetch_all($queryRecords);
		return $data;
	}

	/**
	 * Devuelve el último producto de la base de datos.
	 * 
	 * @return $data El ultimo producto en la base de datos.
	 */
	private function get_last_product()
	{
		$sql = "SELECT * FROM product WHERE id_product=(SELECT max(id_product) FROM product);";
		$queryRecords = pg_query($this->connection, $sql) or die("Error");
		$data = pg_fetch_all($queryRecords);
		return $data;
	}

	/**
	 * Obtiene el último producto de la base de datos, luego crea una identificación única, luego verifica si el último
	 *  producto es nulo, si lo es, asigna la identificación única a la variable, si no lo es,
	 *  comprueba si el cod_product del último producto es igual a la identificación única, si lo es, crea una nueva
	 *  identificación única, luego asigna la identificación única a la variable, luego asigna la variable luego inserta las variables,
	 *  en la base de datos, luego verifica si 
	 *  la consulta fue exitosa, si lo fue, devuelve 1, si no lo fue, 0.
	 * 
	 * @param request array
	 * 
	 * @return int 1 si tuvo exito la query o 0 si no.
	 */
	public function add_product($request)
	{
		$cod_product = '';
		$product = $this->get_last_product();
		$unique_id = uniqid();
		if (is_null($product)) {
			$cod_product = $unique_id;
		} else {
			if ($product[0]['cod_product'] == $unique_id) {
				$unique_id = uniqid();
			}
			$cod_product = $unique_id;
		}
		$new_cod_product = $cod_product;
		$name = $request['name'];
		$price = $request['price'];
		$stock = $request['stock'];
		$id_store = $request['id_store'];
		$sql = "INSERT INTO product (cod_product, name, price, stock, store_id)
		VALUES ('$new_cod_product','$name', '$price', '$stock', '$id_store')";
		if (pg_query($this->connection, $sql) or die("Error")) {
			return 1;
		} else {
			return 0;
		}
	}
	/**
	 * Actualiza la tabla de productos con los nuevos valores del producto.
	 * 
	 * @param request array
	 * 
	 * @return 1 si la query tuvo exito o 0 si no lo fue.
	 */
	public function edit_product($request)
	{
		$id = $request['id'];
		$name = $request['name'];
		$price = $request['price'];
		$stock = $request['stock'];
		$id_store = $request['id_store'];
		$sql = "UPDATE public.product 
		SET name= '$name', price='$price', stock='$stock', store_id='$id_store'
		WHERE id_product='$id'";
		if (pg_query($this->connection, $sql) or die("Error")) {
			return 1;
		} else {
			return 0;
		}
	}
	/**
	 * Actualiza la columna delete_at de la tabla de productos con la fecha y hora actual
	 * 
	 * @param request Es elrequest que viene desde el controlador
	 * 
	 * @return 1 si la query tuvo exito o 0 si no lo fue.
	 */
	public function delete_product($request)
	{
		$id = $request['id'];
		$data = date('Y-m-d h:i:s');
		$sql = "UPDATE public.product 
		SET  deleted_at='$data'
		WHERE id_product='$id'";
		if (pg_query($this->connection, $sql) or die("Error")) {
			return 1;
		} else {
			return 0;
		}
	}
}
