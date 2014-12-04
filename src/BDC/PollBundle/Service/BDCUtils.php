<?php

namespace BDC\PollBundle\Service;


use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
        $output= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><table align="center" bgcolor="00BCFF" border="0" cellpadding="0" cellspacing="0" style="width: 100%; font-family: Arial, "Helvetica Neue", Helvetica, sans-serif; font-size: 12px; color: #F8F8F8; margin: 0px; padding: 0px">'.$new_line;
        $output.='<tbody>'.$new_line;
        $output.='<tr align="center">'.$new_line;
        $output.='<td align="center">'.$new_line;
        $output.='<table align="center" border="0" cellpadding="0" cellspacing="0" style="font-family: Arial, "Helvetica Neue", Helvetica, sans-serif; font-size: 12px; color: #F8F8F8; margin: 0px; padding: 0px" width="600">'.$new_line;
        $output.='<tbody>'.$new_line;
        $output.='<tr align="center">'.$new_line;
        $output.='<td align="center">'.$new_line;
        $output.='<a href="http://www.belgranocordoba.com/" style="display: block;" target="_blank">'.$new_line;
        $output.='<img alt="Club Atlético Belgrano" height="80" src="http://sendder.com.ar/templates/belgrano/img/top.png" style="display: block;" width="600" />'.$new_line;
        $output.='</a>'.$new_line;
        $output.='</td>'.$new_line;
        $output.='</tr>'.$new_line;
        $output.='<tr bgcolor="#121211">'.$new_line;
        $output.='<td align="center" valign="middle">'.$new_line;
        $output.='<font style="font-family: "Trebuchet MS", "Lucida Grande", "Lucida Sans Unicode", "Lucida Sans", Tahoma, sans-serif; font-size: 15px; color: #FFFFFF; display: block; padding: 5px;">Bolet&iacute;n de Noticias para socios de Belgrano</font>'.$new_line;
        $output.='</td>'.$new_line;
        $output.='</tr>'.$new_line;
        //$output.='<tr align="center"><td align="center" colspan="2" height="10">&nbsp;</td></tr></tbody></table></td></tr>'.$new_line;
        $output.='<tr align="center"><td align="center"><img alt="" height="250" src="http://sendder.com.ar/templates/belgrano/img/main.jpg" width="600" /></td></tr><!-- /MAIN_IMG -->'.$new_line;
        $output.='<tr>'
                . '<td>'
                . '<table bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #5F5F60; margin: 0px; padding: 0px;" width="600">'
                . '<tbody>'
                . '<tr>'
                . '<td height="10">&nbsp;</td>'
                . '</tr>'
                . '<tr>'
                . '<td height="10" style="padding-left: 15px;">'.$new_line;      
        $output.='<table>'.$new_line.'<tr>'.$new_line.'<td>'.$new_line.'<h2 style="color: #009DD4; font-family: arial;">Encuesta: '.$poll->name.'</h2>'.$new_line.'</td></tr>'.$new_line.'</table>'.$new_line;
        
        $output.= '<form method="post" action="'.$action.'">'.$new_line.'<input type="hidden" name="email" id="email" value="*|EMAIL|*" /><input type="hidden" name="id_poll" id="id_poll" value="'.$poll->id.'" />'.$new_line;
        
        foreach ($questions as $q) {
            $output.= '<table style="margin-top: 20px">'.$new_line.'<tr><td style="color: #009DD4; font-family: arial;">'.htmlentities($q->question).'</td></tr>'.$new_line.'</table>'.$new_line;
            $output.='<table>';
            foreach ($answers as $a) {
                if ($a->id_question == $q->id) {
                    $output.= $new_line.'<tr><td><label><input type="radio" name="answers['.$q->id.']" value="'.$a->id.'" style="font-family: arial" />'.htmlentities($a->answer).'</label></td></tr>';
                }
            }
            $output.= $new_line.'</table>';
            
            
        }
        $output.= $new_line.'<br/><br/><input type="submit" value="Enviar"></form>'.$new_line;
        $output.='</td>
</tr>
<tr align="center">
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
<td align="left">
<p style="margin: 0; padding: 0;">
<b><font style="font-family: "Trebuchet MS", "Lucida Grande", "Lucida Sans
Unicode", "Lucida Sans", Tahoma, sans-serif; font-size: 14px; color:
#009DD4;">Seguinos en: <a href="https://www.facebook.com/ClubBelgranoCordoba"
target="_blank"><img alt="Facebook" src="
http://sendder.com.ar/templates/belgrano/img/facebook.png"
style="margin-bottom: 2px; vertical-align: middle;" /></a> <a href="
https://twitter.com/BelgranoCbaOk" target="_blank"><img alt="Twitter" src="
http://sendder.com.ar/templates/belgrano/img/twitter.png"
style="margin-bottom: 2px; vertical-align: middle;" /></a></font></b></p>
</td>
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
</table>'.$new_line;
        return $output;
        
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