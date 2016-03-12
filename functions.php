<?php
require_once('vendor/autoload.php');

/**
 * Sends a log to the error log or to the globally configured @see $apiLogFunction
 *
 * @param string $msg message
 * @param string $category category of the message (optional, only some implementations)
 */
function apilog($msg, $category = null) {
	global $apiLogFunction;

	if (isset($apiLogFunction)) {
		$apiLogFunction($msg, $category);
	} else {
		error_log($msg);
	}
}

/**
 * Makes a call to the Club Manager API with Guzzle
 *
 * @param string $method HTTP method
 * @param string $url url to query
 * @param array $args additional args to pass along
 * @return stdClass response object
 * @throws MastrackApiException
 */
function makeServiceCall($method, $url, $args = [])
{
	global $cmApiServer;

	if (strpos($url, '/') !== 0)
	{
		$url = '/' . $url;
	}

	apilog($cmApiServer);

	$response = null;
	$client = new \GuzzleHttp\Client([
		'base_uri' => $cmApiServer
	]);

//		$args['access_token' => $oAuthToken];
	apilog("Sending {$method} to {$url} with data " . json_encode($args), __CLASS__);

	$headers = php_sapi_name() == 'cli' ? [] : ['Referer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null];
	$response = $client->request($method, $url, [
		'query' => $args,
		'headers' => $headers
	]);

	$bodytext = $response->getBody();

//	apilog("{$url} returned {$bodytext}.", __CLASS__);

	$type = $response->getHeader('content-type');
	if (is_array($type))
	{
		$type = array_shift($type);
	}
	else
	{
		$type = null;
	}

	if (strpos($type, 'application/json') !== FALSE)
	{
		$decodedbody = json_decode($bodytext);
	}
	else
	{
		throw new Exception("Did not return 'Content-type: application/json' (returned '{$bodytext}')");
	}

	if ($response->getStatusCode() != 200)
	{
		throw new Exception("Error during service call: " . $bodytext);
	}

	if (json_last_error() != JSON_ERROR_NONE)
	{
		throw new Exception("Error during service call (no decoded body): " . $bodytext);
	}

	return $decodedbody;
}


