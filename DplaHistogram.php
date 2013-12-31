<?php
include_once 'dpla.php';

class DplaHistogram extends DplaBase {
    /**
     * @return mixed
     */
    public function curl_call() {
        if(!$this->decade) {
            $full_call = $this->q . "&fields=sourceResource.date.displayDate&page_size=500&api_key=" . $this->api_key;
        } else {
            $decade_end = $this->decade + 9;
            $full_call = $this->q . "&sourceResource.temporal.begin=$this->decade&sourceResource.temporal.end=$decade_end&api_key=" . $this->api_key;
        }

        $ch = curl_init($full_call);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data =  curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    private function get_all_results($response) {
        $start = 0;
        $records = json_decode($response, true);

        $total_records = $records['count'];
    }

    /**
     * @param $response
     * @return mixed|void
     */
    public function process_json($response) {
        $records = $this->get_json($response);


        // Get rid of entries with no date
        $years = array();
        foreach($records['docs'] as $record) {
            if(!empty($record['sourceResource.date.displayDate'])) {
                $years[] = $record['sourceResource.date.displayDate'];
            }
        }

        // Sort records by decade
        $decades = array();
        foreach($years as $year) {
            preg_match('/\d{4}/', $year, $matches); // data can vary

            if($matches[0]) {
                $year_base = substr($matches[0], 0, 3) . "0";

                if(array_key_exists($year_base, $decades)) {
                    $decades[$year_base] += 1;
                } else {
                    $decades[$year_base] = 1;
                }
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

    /**
     * Get query terms
     * @return mixed
     */
    private function get_terms() {
        $q = preg_split('/q=/', $this->q);

        return $q[1];
    }

    /**
     * @param $response
     */
    public function get_record_sample($response) {
        $records = $this->get_json($response);

        $html = "<h2>Sample records for (" . $this->get_terms() . ") for the decade $this->decade's</h2>";
        $html .= "<ul>";
        foreach($records['docs'] as $record) {
            $title = (is_array($record['sourceResource']['title'])) ? $record['sourceResource']['title'][0] : $record['sourceResource']['title'];
                $html .= '<li><a href="http://dp.la/item/' . $record['id'] . '" target="_blank">' . $title . '</a></li>';
        }
        $html .= "</ul>";

        echo $html;
    }
}

if($_GET) {
    $img = new DplaHistogram($api_key, $_GET);
    $response = $img->curl_call();
}

if(!$_GET['decade']) {
    $img->process_json($response);
} else {
    $img->get_record_sample($response);
}
