<?php
include_once 'dpla.php';

class DplaMap extends DplaBase {
    /**
     * @return mixed
     */
    public function curl_call() {
        $full_call = "http://api.dp.la/v2/items?q=" . $this->q . "&fields=sourceResource.title,sourceResource.description,sourceResource.identifier,isShownAt,sourceResource.spatial.coordinates&page_size=500&api_key=" . $this->api_key;
        $ch = curl_init($full_call);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data =  curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    /**
     * @param $response
     * @return mixed|void
     */
    public function process_json($response) {
        $records = $this->get_json($response);
        $values = array();
        $i = 0;
        foreach($records['docs'] as $record) {
            $coords = preg_split('/,/', $record['sourceResource.spatial.coordinates']);

            $values[$i]['title'] = $record['sourceResource.title'];
            $values[$i]['link'] = $record['isShownAt'];
            $values[$i]['lat'] = $coords[0];
            $values[$i]['lon'] = $coords[1];

            $i++;
        }

        echo json_encode($values);
    }
}

$img = new DplaMap($api_key, $_GET['q']);
$response = $img->curl_call();
$img->process_json($response);