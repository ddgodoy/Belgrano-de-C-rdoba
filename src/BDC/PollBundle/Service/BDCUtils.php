<?php

namespace BDC\PollBundle\Service;

use Symfony\Component\HttpFoundation\Session\Session;

class BDCUtils {

    function slugify($text) {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text)) {
            return 'n_a';
        }

        return $text;
    }

    function checkSession() {

        $s = new Session();

        return $s->get('user');
    }

    function pie_chart_data($votes, $answers) {

        $output = array();

        foreach ($answers as $a) {
            $answer_result = array('label' => $a->answer);
            $total_votes = 0;
            foreach ($votes as $v) {
                if ($v['answer'] === $a->answer) {
                    $total_votes += 1;
                }
            }
            $answer_result['data'] = $total_votes;
            $output[] = $answer_result;
        }

        return json_encode($output);
    }

}

/*var pie_data = [{
        label: "Series 0",
        data: 1
    }, {
        label: "Series 1",
        data: 3
    }, {
        label: "Series 2",
        data: 9
    }, {
        label: "Series 3",
        data: 20
    }];*/