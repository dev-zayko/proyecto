<?php
require_once('./BaseController.php');
require_once('../models/Product.php');
require_once('../models/Store.php');

class ProductController extends BaseController
{
	// Variables
	protected $str_error_desc;
	protected $str_error_header;
	protected $product;
	protected $store;

	/**
	 *La función __construct() es una función constructora que crea una nueva instancia del Producto y la clase Store
	 */
	function __construct()
	{
		$this->product = new Product();
		$this->store = new Store();
	}
	/**
	 * Metodo para obtener los productos desde modelo porducto
	 */
	public function get()
	{
		try {
			// Apuntamos al metodo get_products
			$products = $this->product->get_products();
			$stores = $this->store->get_stores();
			//Guardamos los productos y las bodegas
			$response = json_encode([$products, $stores]);
		} catch (Error $e) {
			$this->str_error_desc = $e->getMessage();
			$this->str_error_header = 'HTTP/1.1 500 Internal Server Error';
		}
		// Enviamos el response al metodo send
		$this->send($response);
	}

	/**
	 * Metodo para añadir un producto la cual $request
	 * contiene los datos del nuevo producto
	 * 
	 * @param request Objeto request.
	 */
	public function add($request)
	{
		try {
			$response = $this->product->add_product($request);
			// if ternario para validar la respuesta entregada por el metodo add producto
			$response == 1 ? $this->send(1) : $this->send(0); // si es 1 la query insert tuvo exito de lo contrario se envia un 0
		} catch (Error $e) {
			$this->str_error_desc = $e->getMessage();
			$this->str_error_header = 'HTTP/1.1 500 Internal Server Error';
		}
	}

	/**
	 * Metodo para mostrar los productos asociados a una bodega en particular
	 *  
	 * @param request Objeto request
	 */
	public function show($request)
	{
		try {
			$products = $this->product->get_products_store($request);
			$response = json_encode($products);
		} catch (Error $e) {
			$this->str_error_desc = $e->getMessage();
			$this->str_error_header = 'HTTP/1.1 500 Internal Server Error';
		}
		$this->send($response);
	}

	/**
	 * Metodo para editar un producto en particular
	 * 
	 * @param request Objeto request
	 */
	public function edit($request)
	{
		try {
			$response = $this->product->edit_product($request);
			$response == 1 ? $this->send(1) : $this->send(0);
		} catch (Error $e) {
			$this->str_error_desc = $e->getMessage();
			$this->str_error_header = 'HTTP/1.1 500 Internal Server Error';
		}
	}

	/**
	 * Metodo para eliminar logicamente un producto
	 * 
	 * @param request Objeto request
	 */
	public function delete($request)
	{
		try {
			$response = $this->product->delete_product($request);
			$response == 1 ? $this->send(1) : $this->send(0);
		} catch (Error $e) {
			$this->str_error_desc = $e->getMessage();
			$this->str_error_header = 'HTTP/1.1 500 Internal Server Error';
		}
	}

	/**
	 * Metodo el cual se utiliza para enviar una respuesta 
	 * en caso de ser 0 envia error y si es 1 envia un codigo de estado junto con el request
	 * pasado por parametro
	 * 
	 * @param request Los datos a enviar al cliente
	 */
	public function send($request)
	{
		//Send Output
		if (!$this->str_error_desc) {
			$this->send_output(
				$request,
				array('Content-Type: applicaction/json', 'HTTP/1.1 200 OK')
			);
		} else  if ($request == 0) {
			$this->send_output(
				json_encode(array('error' => $this->str_error_desc)),
				array('Content-Type: applicaction/json', $this->str_error_header)
			);
		}
	}
}

$object = new ProductController();

//Si no hay un request method enviamos un mensaje de error
if (!$_SERVER['REQUEST_METHOD']) {
	$this->str_error_desc = 'Method not supported';
	$this->str_error_header = 'HTTP/1.1 422 Unprocessable Entity';
	$this->send(0);
} else {
	//Switch para revisar los verbos http y poder dirigir la llamada al metodo segun la petición del action
	switch ($_SERVER['REQUEST_METHOD']) {
		case 'GET':
			if (isset($_GET['action']) && $_GET['action'] == 'get')
				$object->get();
			break;
		case 'POST':
			// if para determinar si $_POST['action] esta definida y no es nulla
			if (isset($_POST['action'])) {
				if ($_POST['action'] == 'edit') {
					$object->edit($_POST['product']);
				}
				if ($_POST['action'] == 'show') {
					$object->show($_POST['id_store']);
				}
				if ($_POST['action'] == 'add') {
					$object->add($_POST['product']);
				}
				if ($_POST['action'] == 'delete') {
					$object->delete($_POST['product']);
				}
			}
			break;
	}
}
