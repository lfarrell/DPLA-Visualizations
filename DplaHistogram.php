<?php
include_once 'dpla.php';

class DplaHistogram extends DplaBase {
    /**
     * @return mixed
     */
    public function curl_call() {
        if(!$this->decade) {
            $full_call = $this->q . "&fields=sourceResource.temporal.begin&page_size=500&api_key=" . $this->api_key;
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
            if(!empty($record['sourceResource.temporal.begin'][0])) {
                $years[] = $record['sourceResource.temporal.begin'][0];
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

    /**
     * @param $response
     */
    public function get_record_sample($response) {
        $records = $this->get_json($response);
        $html = "<ul>";
        foreach($records['docs'] as $record) {
            $title = '';
            foreach($record['sourceResource']['title'] as $titles) {
                $title .= $titles . "\s";
            }
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
