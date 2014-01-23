<?php
include_once 'dpla.php';

class DplaHistogram extends DplaBase {
    /**
     * @return mixed|void
     */
    public function curl_call() {
        if(!$this->decade) {
            $returned_records = array();
            $decades =  range(1800, 2010, 10);

            $i = 0;
            foreach($decades as $decade) {
                $page = $i + 1;
                $data = $this->base_call($decade, $decade + 9);

                $records = $this->get_json($data);

                $returned_records[$i]['decade'] = $decade;
                $returned_records[$i]['count'] = $records['count'];

                $i++;
            }
        } else {
            $records = $this->base_call();
            $returned_records = $this->get_record_sample($records);
        }

        echo is_array($returned_records) ? json_encode($returned_records) : $returned_records;
    }


    /**
     * @param int $page
     * @param string $decade_start
     * @param string $decade_end
     * @return mixed
     */
    private function base_call($decade_start = '', $decade_end = '') {
        if($this->decade) {
            $decade_start = $this->decade;
            $decade_end = $this->decade + 9;
        }

        $full_call = $this->q . "&sourceResource.temporal.begin=$decade_start&sourceResource.temporal.end=$decade_end&api_key=" . $this->api_key;
        $ch = curl_init($full_call);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data =  curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    /**
     * @todo Not sure this is still needed
     * @param $records
     * @return mixed|void
     */
    public function process_json($records) {
        // Get rid of entries with no date
        $years = array();
        foreach($records as $record) {
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

        $html .= '<a href="http://dp.la/search?' . $this->terms . '">View all results for the selected decade</a>';

        echo $html;
    }
}

$img = new DplaHistogram($api_key, $_GET);
$response = $img->curl_call();