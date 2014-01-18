<?php
include_once 'dpla.php';

class DplaTimeMap extends DplaBase {
    /**
     * @return mixed
     */
    public function curl_call() {
        $data = $this->base_call();

        $records = $this->get_json($data);
        $total_records = $records['count'];
        $returned = 500;
        $returned_records = $records['docs'];

        if($total_records > $returned) {
            $pages = ceil($total_records / $returned);

            for($i=0; $i<$pages; $i++) {
                $page = $i + 2;
                $next_page = $this->get_json($this->base_call($page));
                $returned_records = array_merge($returned_records, $next_page['docs']);
            }
        }

        return $returned_records;
    }

    private function base_call($page=1) {
        $full_call = $this->q . "&sourceResource.spatial.country=United+States&fields=sourceResource.identifier,sourceResource.spatial.state&page_size=500&page=$page&api_key=" . $this->api_key;
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
        $state_list = $this->states();

        $states = array();
        foreach($response as $record) { // count values
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

$img = new DplaTimeMap($api_key, $_GET);
$response = $img->curl_call();
$img->process_json($response);