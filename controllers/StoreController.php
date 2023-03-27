<?php
require_once('./BaseController.php');
require_once('../models/Store.php');

class StoreController extends BaseController
{
    // Variables
    protected $strErrorDesc;
    protected $strErrorHeader;
    protected $store;

    /**
     *La función __construct() es una función constructora que crea una nueva instancia de la clase Store
     */
    function __construct()
    {
        $this->store = new Store();
    }
    /**
     * Metodo para obtener todas las bodegas
     */
    public function get()
    {
        try {
            // Guardamos las bodegas encontradas en una variable
            $stores = $this->store->get_stores();
            //Pasamos la variable al response
            $response = json_encode($stores);
        } catch (Error $e) {
            $this->strErrorDesc = $e->getMessage();
            $this->strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
        }
        // Enviamos el response al metodo send
        $this->send($response);
    }

    /**
     * Metodo para añadir una bodega
     * Este metodo no contiene un $request
     * ya que solo el metodo add_store agrega un id unico
     * al codigo de bodega
     */
    public function add()
    {
        try {
            // Apuntamos al metodo del modelo store
            $response = $this->store->add_store();
            // if ternario para validar la respuesta entregada por el metodo add producto
            $response == 1 ? $this->send(1) : $this->send(0); // si es 1 la query insert tuvo exito de lo contrario se envia un 0
        } catch (Error $e) {
            $this->strErrorDesc = $e->getMessage();
            $this->strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
        }
    }

    /**
     * Función para eliminar logicamente una bodega
     * 
     * @param request 
     */
    public function delete($request)
    {
        try {
            $response = $this->store->delete_store($request);
            $response == 1 ? $this->send(1) : $this->send(0);
        } catch (Error $e) {
            $this->strErrorDesc = $e->getMessage();
            $this->strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
        }
    }

    /**
     * Función para editar una bodega
     * 
     * @param response The response from the client.
     */
    public function edit($response)
    {
        try {
            $response = $this->store->edit_store($response);
            $response == 1 ? $this->send(1) : $this->send(0);
        } catch (Error $e) {
            $this->strErrorDesc = $e->getMessage();
            $this->strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
        }
    }

    /**
     *Si no hay ningún error, se envia la respuesta con un encabezado 200 OK. Si hay un error, se envia el
     *respuesta con el encabezado de error.
     * 
     * @param response La respuesta para enviar de vuelta al cliente
     */
    public function send($response)
    {
        //Send Output
        if (!$this->strErrorDesc) {
            $this->send_output(
                $response,
                array('Content-Type: applicaction/json', 'HTTP/1.1 200 OK')
            );
        } else  if ($response == 0) {
            $this->send_output(
                json_encode(array('error' => $this->strErrorDesc)),
                array('Content-Type: applicaction/json', $this->strErrorHeader)
            );
        }
    }
}

$object = new StoreController();

//Si no hay un request method enviamos un mensaje de error
if (!$_SERVER['REQUEST_METHOD']) {
    $this->strErrorDesc = 'Method not supported';
    $this->strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
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
                    $object->edit($_POST['store']);
                }
                if ($_POST['action'] == 'add') {
                    $object->add();
                }
                if ($_POST['action'] == 'delete') {
                    $object->delete($_POST['store']);
                }
            }
            break;
    }
}
