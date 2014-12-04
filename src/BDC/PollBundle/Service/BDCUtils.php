<?php

namespace BDC\PollBundle\Service;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use BDC\PollBundle\Entity\User;
use BDC\PollBundle\Entity\Associate;

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

    function check_session() {

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
                    $answer_result['data'] = $v['total_votes'];
                    break;
                }
            }

            $output[] = $answer_result;
        }

        return json_encode($output);
    }

    function bar_chart_data($votes, $answers, $associates) {


        $data = array();
        $labels = array();
        $ykeys = array();
        foreach ($associates as $s) {


            $associate_array = array('s' => $s->getName());
            foreach ($answers as $a) {


                $continue = true;
                foreach ($votes as $k => $v) {




                    if (($v['answer'] === $a->answer) && ($v['name'] === $s->getName())) {
                        //$total_votes = $v['total_votes'];

                        if (!in_array($a->answer, $ykeys)) {
                            $ykeys[] = $a->answer;
                            $labels[] = $a->answer;
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

    function generate_form_code($poll, $questions, $answers, $action) {

        $new_line = "\r\n";
        $output = '<table>' . $new_line . '<tr>' . $new_line . '<td>' . $new_line . '<h2 style="color: #009DD4; font-family: arial;">Encuesta: ' . $poll->name . '</h2>' . $new_line . '</td></tr>' . $new_line . '</table>' . $new_line;
        $output.= '<form method="post" action="' . $action . '">' . $new_line . '<input type="hidden" name="email" id="email" value="*|EMAIL|*" /><input type="hidden" name="id_poll" id="id_poll" value="' . $poll->id . '" />' . $new_line;

        foreach ($questions as $q) {
            $output.= '<table style="margin-top: 20px">' . $new_line . '<tr><td style="color: #009DD4; font-family: arial;">' . htmlentities($q->question) . '</td></tr>' . $new_line . '</table>' . $new_line;
            $output.='<table>';
            foreach ($answers as $a) {
                if ($a->id_question == $q->id) {
                    $output.= $new_line . '<tr><td><label><input type="radio" name="answers[' . $q->id . ']" value="' . $a->id . '" style="font-family: arial" />' . htmlentities($a->answer) . '</label></td></tr>';
                }
            }
            $output.= $new_line . '</table>';
        }
        $output.= $new_line . '<br/><br/><input type="submit" value="Enviar"></form>';
        return $output;
    }

    function import_users($file, $em) {


        if (($h = fopen($file, "r")) !== false) {

            $associate = $em->getRepository('BDCPollBundle:Associate')->findOneBy(array('name' => 'Sin Categorizar'));


            $associate_id = $associate->getId();
           
            $added = 0;
            
            $existent = array();
            $invalid_email = array();
      
            while (($data = fgetcsv($h, 1000, ",")) !== false) {
                

                $total_columns = count($data);
                if ($total_columns > 0) {
                    $email = str_replace("'", '', $data[0]);
                                  
                    if (filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
                        $name = '';
                        $last_name = '';
                        $exists = $em->getRepository('BDCPollBundle:User')->findBy(array('email' => $email));

                        if (count($exists) === 0) {

                            $added+=1;

                            if (isset($data[1])) {
                                $name = str_replace("'", '', $data[1]);
                            }

                            if (isset($data[2])) {
                                $last_name = str_replace("'", '', $data[2]);
                            }

                            $user = new User();
                            $user->setEmail($email);
                            $user->setName($name);
                            $user->setLastName($last_name);
                            //$user->setAssociateId($associate_id);
                            $user->setAssociate($associate);
                            $user->setDNI(0);
                            $user->setRole('partners');
                            $user->setPassword('1');
                            $user->setSalt('1');
                            $user->setCreated(new \DateTime());
                            $user->setModified(new \DateTime());
                            
                            $em->persist($user);
                            $em->flush();
                        } else {
                            $existent[] = $email;
                        }
                    } else {

                        $invalid_email[] = $email;
                    }
                }
            }
            fclose($h);
         
            return array('added' => $added, 'invalid_email' => $invalid_email, 'existent' => $existent);            
            
        }
        return false;
    }

}