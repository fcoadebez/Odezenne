<?php

Class O2nApi
{
    /**
     * Curl options
     * @var array
     */
    private $_curlOptions;

    /**
     * Soundcloud API url
     * @var string
     */
    private $_apiEndpoint;

    /**
     * Class constructor
     * @access public
     */
    function __construct()
    {
        $this->_setDefaultCurlOptions();
        $this->_apiEndpoint = getenv('API_HOST') . '/api/v1/';
    }

    protected function _setDefaultCurlOptions()
    {
        $ENV = getenv('ENV');
        switch ($ENV) {
            case 'development':
                $HTTPHEADER = array('Host: lumen.o2n');
                break;
            default:
                $HTTPHEADER = array();
        }

        $this->_curlOptions = array(
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => 'O2N Wordpress',
            CURLOPT_HTTPHEADER => $HTTPHEADER,
        );
    }

    protected function _request($path, $curlOptions = array())
    {
        try {
            $options = $this->_curlOptions;
            $options += $curlOptions;

            $curl = curl_init($path);
            curl_setopt_array($curl, $options);
            $response = curl_exec($curl);

            if (FALSE === $response)
                throw new Exception(curl_error($curl), curl_errno($curl));

            curl_close($curl);

            return $response;

        } catch (Exception $error) {
            trigger_error(sprintf(
                'API request failed with error #%d: %s',
                $error->getCode(), $error->getMessage()),
                E_USER_ERROR);

        }
    }

    function setSettings($settings)
    {
        $formattedUrl = $this->_apiEndpoint . 'settings';

        $params = array(
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $settings
        );

        $data = $this->_request($formattedUrl, $params);

        return $data;
    }
}
