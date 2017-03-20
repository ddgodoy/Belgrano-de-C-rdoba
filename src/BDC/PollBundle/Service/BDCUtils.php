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

    function generate_form_code($poll, $questions, $answers, $action, $user = null) {

        $new_line = "\r\n";

        $email = $user != null?$user->getEmail():"*|EMAIL|*";
        
        $output = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><table align="center" bgcolor="00BCFF" border="0" cellpadding="0" cellspacing="0" style="width: 100%; font-family: Arial, "Helvetica Neue", Helvetica, sans-serif; font-size: 12px; color: #F8F8F8; margin: 0px; padding: 0px">' . $new_line;
        $output.='<tbody>' . $new_line;
        $output.='<tr align="center">' . $new_line;
        $output.='<td align="center">' . $new_line;
        $output.='<table align="center" border="0" cellpadding="0" cellspacing="0" style="font-family: Arial, "Helvetica Neue", Helvetica, sans-serif; font-size: 12px; color: #F8F8F8; margin: 0px; padding: 0px" width="600">' . $new_line;
        $output.='<tbody>' . $new_line;
        //$output.='<tr align="center"><td align="center" colspan="2" height="10">&nbsp;</td></tr></tbody></table></td></tr>'.$new_line;

        if(strlen($poll->image_header) > 1)
            $img = '/uploads/images/'.$poll->image_header;
            $output.='<tr align="center"><td align="center"><img alt="" height="250" src="'.$img.'" width="600" /></td></tr>'. $new_line;
        else
            $output.='<tr align="center"><td align="center"><img alt="" height="250" src="http://sendder.com.ar/templates/belgrano/img/main.jpg" width="600" /></td></tr><!-- /MAIN_IMG -->' . $new_line;
p


        $output.='<tr>'
                . '<td>'
                . '<table bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #5F5F60; margin: 0px; padding: 0px;" width="600">'
                . '<tbody>'
                . '<tr>'
                . '<td height="10">&nbsp;</td>'
                . '</tr>'
                . '<tr>'
                . '<td height="10" style="padding-left: 15px;">' . $new_line;
        $output.='<table>' . $new_line . '<tr>' . $new_line . '<td>' . $new_line . '<h2 style="color: #009DD4; font-family: arial;">Encuesta: ' . htmlentities($poll->name) . '</h2>' . $new_line . '</td></tr>' . $new_line . '</table>' . $new_line;

        $output.= '<form method="post" action="' . $action . '">' . $new_line . '<input type="hidden" name="email" id="email" value="'.$email.'" /><input type="hidden" name="id_poll" id="id_poll" value="' . $poll->id . '" />' . $new_line;


        foreach ($questions as $q) {
            $output.= '<table style="margin-top: 20px">' . $new_line . '<tr><td style="color: #009DD4; font-family: arial; font-size: 24px;">' . htmlentities($q->question) . '</td></tr>' . $new_line . '</table>' . $new_line;
            $output.='<table>';
            foreach ($answers as $a) {
                if ($a->id_question == $q->id) {
                    $output.= $new_line . '<tr><td><label><input type="radio" name="answers[' . $q->id . ']" value="' . $a->id . '" style="font-family: arial;" />&nbsp;&nbsp;<b>' . htmlentities($a->answer) . '<b></label></td></tr>';
                }
            }
            $output.= $new_line . '</table>';
        }
        $output.= $new_line . '<br/><br/><input type="submit" value="Enviar"></form>' . $new_line;
        $output.='</td></tr>'
                . '<tr>'
                . '<td height="10">&nbsp;</td>'
                . '</tr>';
$output.='<tr align="center">' . $new_line;
$output.='<td align="center">' . $new_line;
$output.='<a href="http://www.belgranocordoba.com/" style="display: block;" target="_blank">' . $new_line;
$output.='<img alt="Club Atlético Belgrano" height="80" src="http://sendder.com.ar/templates/belgrano/img/top.png" style="display: block;" width="600" />' . $new_line;
$output.='</a>' . $new_line;
$output.='</td>' . $new_line;
$output.='</tr>' . $new_line;        
$output.='<tr align="center">
<td align="center" height="10">
&nbsp;</td>
</tr>
<!-- FOOTER_WRAP -->
<tr align="center">
<td align="center" colspan="2" valign="top">
<table align="center" border="0" cellpadding="0" cellspacing="0"
style="font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
font-size: 12px; color: #5F5F60; margin: 0px; padding: 0px; border-top: 1px
dashed #A5A5A5;" width="550">
<tbody>
<tr align="center">
<td align="center" height="10">
&nbsp;</td>
</tr>
<tr align="center">
<td align="center" height="10">
&nbsp;</td>
</tr>
</tbody>
</table>
</td>
</tr>
<!-- /FOOTER_WRAP -->
<tr align="center">
<td align="center" height="10">
&nbsp;</td>
</tr>
</tbody>
</table>
</td>
</tr>
<!-- /MAIN_WRAP -->
</tbody>
</table>' . $new_line;
        return $output;
    }

    function import_users($file, $em) {


        if (($h = fopen($file, "r")) !== false) {
            $added = 0;
            $existent = 0;
            $invalid_email = array();

            while (($data = fgetcsv($h, 1000, ",")) !== false) {
                $total_columns = count($data);
                if ($total_columns > 0) {                    
                    $email = trim(str_replace("'", '', $data[0]));
                    if (filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
                        $name                   = '';
                        $last_name              = '';
                        $sex                    = '';
                        $assciate_code_name     = '';
                        $assciate_name          = '';
                        $exists = $em->getRepository('BDCPollBundle:User')->findOneByEmail($email);
                        
                        if (isset($data[1])) {
                            $name = str_replace("'", '', $data[1]);
                        }

                        if (isset($data[2])) {
                            $last_name = str_replace("'", '', $data[2]);
                        }
                        
                        if (isset($data[3])) {
                            $sex = str_replace("'", '', $data[3]);
                        }
                        
                        if (isset($data[4])) {
                            $assciate_name = str_replace("'", '', $data[4]);
                        }else{
                            $assciate_name = 'Activo sin ubicación';
                        }
                        $assciate_name =  $assciate_name === '' ?'Activo sin ubicación':$assciate_name;
                        $assciate_code_name = $this->slugify($assciate_name);
                        $assciate = $em->getRepository('BDCPollBundle:Associate')->findOneByCode($assciate_code_name);
                        
                        if($assciate){
                           $assciate_id = $assciate->getId();   
                        }else{
                            $assciate = new Associate();
                            $assciate->setName($assciate_name);
                            $assciate->setCode($assciate_code_name);
                            $em->persist($assciate);
                            $em->flush();    
                            $assciate_id = $assciate->getId();  
                        }
                        
                        if(count($exists)>0){
                            $user = $exists;
                            $existent+=1;
                        }else{
                            $user = new User();
                            $added+=1;
                        }    
 
                        $user->setEmail($email);
                        $user->setName($name);
                        $user->setLastName($last_name);
                        $user->setAssociateId($assciate_id);
                        $user->setAssociate($assciate);
                        $user->setDNI(0);
                        $user->setRole('partners');
                        $user->setPassword('1');
                        $user->setSalt('1');
                        $user->setGender($sex);
                        $user->setCreated(new \DateTime());
                        $user->setModified(new \DateTime());

                        $em->persist($user);
                        $em->flush();
                        
                    } else {

                        $invalid_email[] = $email;
                    }
                }
            }
            fclose($h);

            return array('added' => $added, 'invalid_email' => $invalid_email, 'existent' => $existent);
        }
    }
    
    function generate_form_code_web($questions, $answers) {
         $output = '';
         foreach ($questions as $q) {
            $output.= '<h3 class="page-header">'.$q->question.'</h3>';
            
            
            foreach ($answers as $a) {
                if ($a->id_question == $q->id) {
                    $output.= '<div class="form-group">';
                    $output.= '<label class="control-label">';
                    $output.= '<input type="radio" name="answers[' . $q->id . ']" value="' . $a->id . '" style="font-family: arial" />' . htmlentities($a->answer) . '</label></div>';
                }
            }
            
        }
        
        return $output;
        
        
        
    }

    function build_pagination_nav($total_pages, $current_page, $form_id) {

        $current_page = intval($current_page);
        
        
        $total_pages = intval($total_pages);

        $output = '<nav class="pull-right"><ul class="pagination">';
        if ($current_page === 1) {
            $output .= '<li class="disabled"><a href="#"><span aria-hidden="true">&laquo;</span><span class="sr-only">Anterior</span></a></li>';
        } else {
            $previous = $current_page - 1;
            $onclick = "go_to_page($previous, '$form_id')";
            $output .= '<li><a href="#" onclick="' . $onclick . '"><span aria-hidden="true">&laquo;</span><span class="sr-only">Anterior</span></a></li>';
        }

        for ($i = 1; $i <= $total_pages; $i++) {

            if ($i !== $current_page) {
                $onclick = "go_to_page($i, '$form_id')";

                $output .= '<li><a href="#" onclick="' . $onclick . '">' . $i . ' <span class="sr-only"></span></a></li>';
            } else {
                $output.= '<li class="active"><a>' . $i . ' <span class="sr-only">(current)</span></a></li>';
            }
        }

        if ($current_page === $total_pages) {
            $output .= '<li class="disabled"><a href="#"><span aria-hidden="true">&raquo;</span><span class="sr-only">Siguiente</span></a></li>';
        } else {
            $next = $current_page + 1;
            $onclick = "go_to_page($next, '$form_id');";
            $output .= '<li><a href="#" onclick="' . $onclick . '"><span aria-hidden="true">&raquo;</span><span class="sr-only">Anterior</span></a></li>';
        }

        $output.='</ul></nav>';

        return $output;
    }

}
