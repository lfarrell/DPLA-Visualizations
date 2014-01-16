<?php
include_once 'dpla.php';

class DplaTimeMap extends DplaBase {
    /**
     * @return mixed
     */
    public function curl_call() {
        $full_call = $this->q . "&fields=sourceResource.title,sourceResource.description,sourceResource.identifier,sourceResource.spatial.state&page_size=500&api_key=" . $this->api_key;
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
        $state_list = $this->states();

        $states = array();
        foreach($records['docs'] as $record) {
            $state = $record['sourceResource.spatial.state'];
            if(!in_array($state, $state_list)) {
                continue;
            }

            if(array_key_exists($state, $states)) {
                $states[$state] += 1;
            } else {
                $states[$state] = 1;
            }
        }

        echo json_encode($states);
    }

    private function states() {
        return array('AL'=>"Alabama", 'AK'=>"Alaska", 'AZ'=>"Arizona", 'AR'=>"Arkansas", 'CA'=>"California", 'CO'=>"Colorado", 'CT'=>"Connecticut", 'DE'=>"Delaware", 'DC'=>"District Of Columbia", 'FL'=>"Florida", 'GA'=>"Georgia", 'HI'=>"Hawaii", 'ID'=>"Idaho", 'IL'=>"Illinois", 'IN'=>"Indiana", 'IA'=>"Iowa", 'KS'=>"Kansas", 'KY'=>"Kentucky", 'LA'=>"Louisiana", 'ME'=>"Maine", 'MD'=>"Maryland", 'MA'=>"Massachusetts", 'MI'=>"Michigan", 'MN'=>"Minnesota", 'MS'=>"Mississippi", 'MO'=>"Missouri", 'MT'=>"Montana", 'NE'=>"Nebraska", 'NV'=>"Nevada", 'NH'=>"New Hampshire", 'NJ'=>"New Jersey", 'NM'=>"New Mexico", 'NY'=>"New York", 'NC'=>"North Carolina", 'ND'=>"North Dakota", 'OH'=>"Ohio", 'OK'=>"Oklahoma", 'OR'=>"Oregon", 'PA'=>"Pennsylvania", 'RI'=>"Rhode Island", 'SC'=>"South Carolina", 'SD'=>"South Dakota", 'TN'=>"Tennessee", 'TX'=>"Texas", 'UT'=>"Utah", 'VT'=>"Vermont", 'VA'=>"Virginia", 'WA'=>"Washington", 'WV'=>"West Virginia", 'WI'=>"Wisconsin", 'WY'=>"Wyoming");
    }
}

$img = new DplaTimeMap($api_key, $_GET);
$response = $img->curl_call();
$img->process_json($response);