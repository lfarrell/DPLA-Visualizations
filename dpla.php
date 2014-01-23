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
        $this->terms = $this->clean($query['q']);
        $this->q = "http://api.dp.la/v2/items?q=" . $this->terms;
        $this->decade = (isset($query['decade'])) ? $this->clean($query['decade']) : false;
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
     * @param $response
     * @return mixed
     */
    protected function get_json($response) {
        return json_decode($response, true);
    }

    /**
     * @param $url
     * @return string
     */
    protected function clean($url) {
        $clean = strip_tags(trim($url));

        return preg_replace('/(\s{1,}|,)/', '+AND+', $clean);
    }
}