<?php
class BaseController
{
	/**
	 * Si el usuario intenta llamar a una función que no existe, se envía un error 404
	 * 
	 * @param name The name of the method that was called.
	 * @param arguments 
	 */
	public function __call($name, $arguments)
	{
		$this->sendOutput('', array('HTTP/1.1 404 Not Found'));
	}

	/**
	 * Elimina todas las cookies de la respuesta y luego envía la respuesta.
	 * 
	 * @param data The data to be sent to the client.
	 * @param httpHeaders An array of HTTP headers to send to the browser.
	 */
	protected function send_output($data, $httpHeaders = array())
	{
		header_remove('Set-Cookie');

		if (is_array($httpHeaders) && count($httpHeaders)) {
			foreach ($httpHeaders as $httpHeader) {
				header($httpHeader);
			}
		}
		echo $data;
		exit;
	}
}
