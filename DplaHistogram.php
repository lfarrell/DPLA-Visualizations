<?php
include_once 'dpla.php';

class DplaHistogram extends DplaBase {
    /**
     * @return mixed
     */
    public function curl_call() {
        $full_call = "http://api.dp.la/v2/items?q=" . $this->q . "&fields=sourceResource.date.begin,sourceResource.date.end&page_size=500&api_key=" . $this->api_key;
        $ch = curl_init($full_call);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data =  curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    public function process_json($response) {
        $records = json_decode($response, true);

        // Get rid of entries with no date
        $years = array();
        foreach($records['docs'] as $record) {
            if(!empty($record['sourceResource.date.begin'])) {
                $years[] = $record['sourceResource.date.begin'];
            }
        }

        // Sort records by decade
        $decades = array();
        foreach($years as $year) {
            $year_normalize = preg_split('/-/', $year); // data can vary
            $year_base = substr($year_normalize[0], 0, 3) . "0";

            if(array_key_exists($year_base, $decades)) {
                $decades[$year_base] += 1;
            } else {
                $decades[$year_base] = 1;
            }
        }
        ksort($decades);

        $d3_decades = array();
        $i = 0;
        foreach($decades as $key => $value) {
            $d3_decades[$i]['decade'] = $key;
            $d3_decades[$i]['count'] = $value;
            $i++;
        }

        echo json_encode($d3_decades);
    }
}

$img = new DplaHistogram($api_key, $_GET['q']);
$response = $img->curl_call();
$img->process_json($response);