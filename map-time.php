<?php
include_once 'dpla.php';

class DplaTimeMap extends DplaBase {
    /**
     * @return mixed
     */
    public function curl_call() {
        $full_call = $this->q . "&sourceResource.spatial.country=United+States&fields=sourceResource.title,sourceResource.description,sourceResource.identifier,sourceResource.spatial.state&page_size=500&api_key=" . $this->api_key;
//echo $full_call; exit;
        $ch = curl_init($full_call);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data =  curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    private function get_all_results($response, $full_call) {
        $max_returned = 500;
        $records = json_decode($response, true);
        $total_records = $records['count'];

        if($total_records > $max_returned) {
            $page = 0;

            while($max_returned < $total_records) {
                $full_call += $full_call . "&page=$page";
                $page++;
                $max_returned += 500;
            }
        }
    }

    /**
     * @param $response
     * @return mixed|void
     */
    public function process_json($response) {
        $records = $this->get_json($response);
        $state_list = $this->states();

        $states = array();
        foreach($records['docs'] as $record) { // count values
            $state = $record['sourceResource.spatial.state'];

            if(is_array($state)) { // can have multiple states listed
                foreach($state as $sub_state) {
                    $sub_state = trim($sub_state);

                    if(!in_array($sub_state, $state_list)) {
                        continue;
                    }

                    if(array_key_exists($sub_state, $states)) {
                        $states[$sub_state] += 1;
                    } else {
                        $states[$sub_state] = 1;
                    }
                }
            } else {
                if(!in_array($state, $state_list)) {
                    continue;
                }

                if(array_key_exists($state, $states)) {
                    $states[$state] += 1;
                } else {
                    $states[$state] = 1;
                }
            }
        }

        $d3_states = array();
        $i = 0;
        foreach($states as $key => $value) { // js objectify
            $d3_states[$i]['state'] = $key;
            $d3_states[$i]['value'] = $value;
            $i++;
        }
        echo json_encode($d3_states);
    }

    private function states() {
        return array('AL'=>"Alabama", 'AK'=>"Alaska", 'AZ'=>"Arizona", 'AR'=>"Arkansas", 'CA'=>"California", 'CO'=>"Colorado", 'CT'=>"Connecticut", 'DE'=>"Delaware", 'DC'=>"District Of Columbia", 'FL'=>"Florida", 'GA'=>"Georgia", 'HI'=>"Hawaii", 'ID'=>"Idaho", 'IL'=>"Illinois", 'IN'=>"Indiana", 'IA'=>"Iowa", 'KS'=>"Kansas", 'KY'=>"Kentucky", 'LA'=>"Louisiana", 'ME'=>"Maine", 'MD'=>"Maryland", 'MA'=>"Massachusetts", 'MI'=>"Michigan", 'MN'=>"Minnesota", 'MS'=>"Mississippi", 'MO'=>"Missouri", 'MT'=>"Montana", 'NE'=>"Nebraska", 'NV'=>"Nevada", 'NH'=>"New Hampshire", 'NJ'=>"New Jersey", 'NM'=>"New Mexico", 'NY'=>"New York", 'NC'=>"North Carolina", 'ND'=>"North Dakota", 'OH'=>"Ohio", 'OK'=>"Oklahoma", 'OR'=>"Oregon", 'PA'=>"Pennsylvania", 'RI'=>"Rhode Island", 'SC'=>"South Carolina", 'SD'=>"South Dakota", 'TN'=>"Tennessee", 'TX'=>"Texas", 'UT'=>"Utah", 'VT'=>"Vermont", 'VA'=>"Virginia", 'WA'=>"Washington", 'WV'=>"West Virginia", 'WI'=>"Wisconsin", 'WY'=>"Wyoming");
    }
}
$_GET['q'] = 'Carolina';
$img = new DplaTimeMap($api_key, $_GET);
$response = $img->curl_call();
$img->process_json($response);