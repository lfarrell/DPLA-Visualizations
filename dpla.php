<?php
include_once 'api_key.php';

abstract class DplaBase {
    public $api_key;
    public $query;

    /**
     * @param $api_key
     * @param $query
     */
    public function __construct($api_key, $query) {
        $this->api_key = $api_key;
        $this->q = $this->clean($query);
    }

    /**
     * @return mixed
     */
    abstract public function curl_call();

    /**
     * @param $response
     * @return mixed
     */
    abstract public function process_json($response);

    /**
     * @param $url
     * @return string
     */
    private function clean($url) {
        $clean = strip_tags(trim($url));

        return preg_replace('/(\s{1,}|,)/', '+AND+', $clean);
    }
}