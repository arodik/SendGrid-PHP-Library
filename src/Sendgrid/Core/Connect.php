<?php

namespace Sendgrid\Core;

/**
 * All the methods returns an array of data or one string / int
 * If false returned you can run getLastResponseError() to see the error information
 * If error information == NULL then no error accrued (like deleting a record returns 0 records deleted if no record found)
 */

class Connect
{
    private $apiEndpoint;
    private $authUser;
    private $authKey;
    private $lastResponseError;
    private $debug;
    private $curlSSLVerify;

    /**
     * Timeout in seconds for an API call to respond
     * @var integer
     */
    const TIMEOUT = 20;

    /**
     *
     * user agent ...
     * @var string
     */
    const USER_AGENT = 'Sendgrid Newsletter PHP API';

    /**
     *
     * sendgrid endpoint ...
     * @var string
     */
    const SG_ENDPOINT = 'https://api.sendgrid.com/api';

    /**
     * Creates a new SendGrid Newsletter API object to make calls with
     *
     * Your API key needs to be generated using SendGrid Management
     * Authentication is done automatically when making the first API call
     * using this object.
     *
     * @param string $user The username of the account to use
     * @param string $key The API key to use
     * @param boolean $debug Set to true to get debug information (development)
     * @param boolean $curlSSLVerify set false to disable CURL ssl cert verification
     */
    public function __construct($user, $key, $debug = false, $curlSSLVerify = true)
    {
        $this->authUser = $user;
        $this->authKey = $key;
        $this->apiEndpoint = self::SG_ENDPOINT;
        $this->debug = $debug;
        $this->curlSSLVerify = $curlSSLVerify;
    }

    /**
     * For clients with custom API endpoint
     * @param $endpoint
     */
    public function setCustomApiEndpoint($endpoint)
    {
        $this->apiEndpoint = $endpoint;
    }

    /**
     * Makes a call to an API
     *
     * @param string $url The relative URL to call (example: "/server")
     * @param string|array $postData (Optional) The JSON string to send
     * @param string $method (Optional) The HTTP method to use
     * @return array The parsed response, or NULL if there was an error
     */
    protected function makeApiCall($url, $postData = null, $method = 'POST')
    {
        $postData['api_user'] = $this->authUser;
        $postData['api_key'] = $this->authKey;

        $this->debugCall('DEBUG - Post Data: ', $postData);

        $url .= '.json';

        $jsonUrl = $this->apiEndpoint . '/' . $url;
        // Generate curl request
        $session = curl_init($jsonUrl);

        $this->debugCall('DEBUG - Curl Session: ', $session);

        //Set to FALSE to stop cURL from verifying the peer's certificate (needed for local hosts development mostly)
        if (!$this->curlSSLVerify) {
            curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
        }

        // Tell curl to use HTTP POST
        curl_setopt($session, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        // Tell curl that this is the body of the POST
        $postfields = $postData;
        if (is_array($postData)) {
            $postfields = http_build_query($postData);
            // Sendgrid don't understand arrays with indexes, (lists/email/add with many recipients for example)
            // remove them.
            // Shame on you, Sendgrid
            $postfields = preg_replace('/%5B[0-9]+%5D/simU', '%5B%5D', $postfields);
        }
        curl_setopt($session, CURLOPT_POSTFIELDS, $postfields);
        // Tell curl not to return headers, but do return the response
        curl_setopt($session, CURLOPT_HEADER, false);
        curl_setopt($session, CURLOPT_USERAGENT, self::USER_AGENT);
        curl_setopt($session, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($session, CURLOPT_TIMEOUT, self::TIMEOUT);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

        // obtain response
        $jsonResponse = curl_exec($session);
        curl_close($session);

        $this->debugCall('DEBUG - Json Response: ', $jsonResponse);

        $results = json_decode($jsonResponse, true);

        $this->debugCall('DEBUG - Results: ', $results);

        $this->lastResponseError = isset($results['error']) ? $results['error'] : null;

        return $this->lastResponseError ? false : $results;
    }


    /**
     * Makes a print out of every step of makeApiCall for DEBUGGING
     *
     * @param string $text The text to show before the actual debug information EX: DEBUG - Results:
     * @param string / array $data the actual debug data to show
     */
    private function debugCall($text = 'DEBUG : ', $data)
    {
        if (!$this->debug) {
            return;
        }

        $newLine = array_key_exists('HTTP_USER_AGENT', $_SERVER) ? '<br/>' : "\n";

        echo $newLine . $text;
        //print_r($data);
        if (is_array($data)) {
            foreach ($data as $name => $value) {
                if ($name === 'api_user' || $name === 'api_key') {
                    continue;
                }
                echo $newLine . $name . ' => ' . is_array($value) ? json_encode($value) : $value;
            }
            echo $newLine;
        } else {
            echo $data . $newLine;
        }
    }

    public function getLastResponseError()
    {
        return $this->lastResponseError;
    }

}
