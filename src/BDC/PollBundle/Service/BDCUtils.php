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

    function bar_chart_data($votes, $answers, $associates) {


        $data = array();
        $labels = array();
        $ykeys = array();
        foreach ($associates as $s) {

            $labels[] = $s->getName();
            $associate_array = array('s' => $s->getName());
            foreach ($answers as $a) {


                $continue = true;
                foreach ($votes as $k => $v) {




                    if (($v['answer'] === $a->answer) && ($v['name'] === $s->getName())) {
                        //$total_votes = $v['total_votes'];

                        if (!in_array($a->answer, $ykeys)) {
                            $ykeys[] = $a->answer;
                        }
                        $associate_array[$a->answer] = $v['total_votes'];

                        break;
                    }
                }
            }
            $data[] = $associate_array;
        }
        $output = array('data' => $data, 'labels' => $labels, 'ykeys' => array_values($ykeys));
        return json_encode($output);
    }

}

/*
 * data: [{
            s: 'Celeste',
            a: 100,
            b: 90
        }, {
            y: 'Dorada',
            a: 75,
            b: 65
        }, {
            y: '2008',
            a: 50,
            b: 40
        }, {
            y: '2009',
            a: 75,
            b: 65
        }, {
            y: '2010',
            a: 50,
            b: 40
        }, {
            y: '2011',
            a: 75,
            b: 65
        }, {
            y: '2012',
            a: 100,
            b: 90
        }],
        xkey: 's',
        ykeys: ['a', 'b'],
        labels: ['Series A', 'Series B'],
 */

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