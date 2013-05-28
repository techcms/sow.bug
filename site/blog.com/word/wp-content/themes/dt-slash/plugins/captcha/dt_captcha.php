<?php
/*
Plugin Name: Captcha
Plugin URI:  http://bestwebsoft.com/plugin/
Description: Plugin Captcha intended to prove that the visitor is a human being and not a spam robot. Plugin asks the visitor to answer a math question.
Author: BestWebSoft
Version: 2.24
Author URI: http://bestwebsoft.com/
License: GPLv2 or later
*/

/*  © Copyright 2011  BestWebSoft  ( admin@bestwebsoft.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/*
Modified by DT
*/

// start session
function dt_start_session() {
    if( !session_id() ) {
        session_start();
    }
    Dt_Captcha::init();
}
add_action('init', 'dt_start_session');

if( !defined('DT_CAPTCHA_KEY') ) {
    define('DT_CAPTCHA_KEY', '123');
}

class Dt_Captcha {
    static $hide_register = false;
    static $math_action_plus = true;
    static $math_action_minus = true;
	static $math_action_increase = true;
	static $label_form = '';
	static $difficulty_number = true;
	static $difficulty_word = true;
    static $contact_form = true;
    static $contact_widget = true;
    private $whoami = '';
    
    function init() {
        if( function_exists('of_get_option') ) {
            Dt_Captcha::$hide_register = of_get_option('captcha_hide_register', true);
//            Dt_Captcha::$math_action_plus = of_get_option('captcha_math_action_plus', true);
            Dt_Captcha::$math_action_minus = of_get_option('captcha_math_action_minus', true);
            Dt_Captcha::$math_action_increase = of_get_option('captcha_math_action_increase', true);
            Dt_Captcha::$label_form = of_get_option('captcha_label_form', '');
            Dt_Captcha::$difficulty_number = of_get_option('captcha_difficulty_number', true);
            Dt_Captcha::$difficulty_word = of_get_option('captcha_difficulty_word', true);
            Dt_Captcha::$contact_form = of_get_option('captcha_contact_form', true);
            Dt_Captcha::$contact_widget = of_get_option('captcha_contact_widget', true);
        }
    }
    
    function __construct( $str ) {
        $this->whoami = $str;
    }
    
    function get_captcha() {
        $out = '';
        
        // skip captcha if user is logged in and the settings allow
        if ( is_user_logged_in() && true == Dt_Captcha::$hide_register ) {
            return $out;
        }
        
        if( ('form' == $this->whoami && !Dt_Captcha::$contact_form) ||
            ('widget' == $this->whoami && !Dt_Captcha::$contact_widget) ) {
            return $out;
        }
        
        // captcha html - comment form
        /* $out .= '<p class="dt_cptch_block">';*/
        if( !empty(Dt_Captcha::$label_form) ) {	
            /* $out .= '<label>'. stripslashes( Dt_Captcha::$label_form ) .'</label>'; */
			$out .= '<p>'. stripslashes( Dt_Captcha::$label_form ) .'</p>';
        }
        /* $out .= '<br />'; */
		$out .= '<div>';
        $out .= $this->generate_captcha();
		$out .= '</div>';
        /* $out .= '</p>'; */
        
        return $out;
    }
    
    function generate_captcha(){
    
        // In letters presentation of numbers 0-9
        $number_string = array(); 
        $number_string[0] = __( 'null', 'captcha' );
        $number_string[1] = __( 'one', 'captcha' );
        $number_string[2] = __( 'two', 'captcha' );
        $number_string[3] = __( 'three', 'captcha' );
        $number_string[4] = __( 'four', 'captcha' );
        $number_string[5] = __( 'five', 'captcha' );
        $number_string[6] = __( 'six', 'captcha' );
        $number_string[7] = __( 'seven', 'captcha' );
        $number_string[8] = __( 'eight', 'captcha' );
        $number_string[9] = __( 'nine', 'captcha' ); 
        // In letters presentation of numbers 11 -19
        $number_two_string = array();
        $number_two_string[1] = __( 'eleven', 'captcha' );
        $number_two_string[2] = __( 'twelve', 'captcha' );
        $number_two_string[3] = __( 'thirteen', 'captcha' );
        $number_two_string[4] = __( 'fourteen', 'captcha' );
        $number_two_string[5] = __( 'fifteen', 'captcha' );
        $number_two_string[6] = __( 'sixteen', 'captcha' );
        $number_two_string[7] = __( 'seventeen', 'captcha' );
        $number_two_string[8] = __( 'eighteen', 'captcha' );
        $number_two_string[9] = __( 'nineteen', 'captcha' );
        // In letters presentation of numbers 10, 20, 30, 40, 50, 60, 70, 80, 90
        $number_three_string = array();
        $number_three_string[1] = __( 'ten', 'captcha' );
        $number_three_string[2] = __( 'twenty', 'captcha' );
        $number_three_string[3] = __( 'thirty', 'captcha' );
        $number_three_string[4] = __( 'forty', 'captcha' );
        $number_three_string[5] = __( 'fifty', 'captcha' );
        $number_three_string[6] = __( 'sixty', 'captcha' );
        $number_three_string[7] = __( 'seventy', 'captcha' );
        $number_three_string[8] = __( 'eighty', 'captcha' );
        $number_three_string[9] = __( 'ninety', 'captcha' );
        // The array of math actions
        $math_actions = array();

        // If value for Plus on the settings page is set
        if( true == Dt_Captcha::$math_action_plus ) {
            $math_actions[] = '&#43;';
        }
        // If value for Minus on the settings page is set
        if( true == Dt_Captcha::$math_action_minus ) {
            $math_actions[] = '&minus;';
        }
        // If value for Increase on the settings page is set
        if( true == Dt_Captcha::$math_action_increase ) {
            $math_actions[] = '&times;';
        }
            
        // Which field from three will be the input to enter required value
        $rand_input = rand( 0, 2 );
        // Which field from three will be the letters presentation of numbers
        $rand_number_string = rand( 0, 2 );
        // If don't check Word in setting page - $rand_number_string not display
        if( false == Dt_Captcha::$difficulty_word) {
            $rand_number_string = -1;
        }
        // Set value for $rand_number_string while $rand_input = $rand_number_string
        while($rand_input == $rand_number_string) {
            $rand_number_string = rand( 0, 2 );
        }
        // What is math action to display in the form
        $rand_math_action = rand( 0, count($math_actions) - 1 );

        $array_math_expretion = array();

        // Add first part of mathematical expression
        $array_math_expretion[0] = rand( 1, 9 );
        // Add second part of mathematical expression
        $array_math_expretion[1] = rand( 1, 9 );
        // Calculation of the mathematical expression result
        switch( $math_actions[$rand_math_action] ) {
            case "&#43;":
                $array_math_expretion[2] = $array_math_expretion[0] + $array_math_expretion[1];
                break;
            case "&minus;":
                // Result must not be equal to the negative number
                if($array_math_expretion[0] < $array_math_expretion[1]) {
                    $number										= $array_math_expretion[0];
                    $array_math_expretion[0]	= $array_math_expretion[1];
                    $array_math_expretion[1]	= $number;
                }
                $array_math_expretion[2] = $array_math_expretion[0] - $array_math_expretion[1];
                break;
            case "&times;":
                $array_math_expretion[2] = $array_math_expretion[0] * $array_math_expretion[1];
                break;
        }
        
        // String for display
        $str_math_expretion = "";
        // First part of mathematical expression
        if( 0 == $rand_input )
            $str_math_expretion .= "<input type=\"text\" name=\"cptch_number\" value=\"\" maxlength=\"1\" size=\"1\" style=\"width:20px;margin-bottom:0;display:inline;\" />";
        else if ( 0 == $rand_number_string || false == Dt_Captcha::$difficulty_number )
            $str_math_expretion .= $number_string[$array_math_expretion[0]];
        else
            $str_math_expretion .= $array_math_expretion[0];
        
        // Add math action
        $str_math_expretion .= " ".$math_actions[$rand_math_action];
        
        // Second part of mathematical expression
        if( 1 == $rand_input )
            $str_math_expretion .= " <input type=\"text\" name=\"cptch_number\" value=\"\" maxlength=\"1\" size=\"1\" style=\"width:20px;margin-bottom:0;display:inline;\" />";
        else if ( 1 == $rand_number_string || false == Dt_Captcha::$difficulty_number )
            $str_math_expretion .= " ".$number_string[$array_math_expretion[1]];
        else
            $str_math_expretion .= " ".$array_math_expretion[1];
        
        // Add =
        $str_math_expretion .= " = ";
        
        // Add result of mathematical expression
        if( 2 == $rand_input ) {
            $str_math_expretion .= " <input type=\"text\" name=\"cptch_number\" value=\"\" maxlength=\"2\" size=\"1\" style=\"width:20px;margin-bottom:0;display:inline;\" />";
        } else if ( 2 == $rand_number_string || false == Dt_Captcha::$difficulty_number ) {
            if( $array_math_expretion[2] < 10 )
                $str_math_expretion .= " ".$number_string[$array_math_expretion[2]];
            else if( $array_math_expretion[2] < 20 && $array_math_expretion[2] > 10 )
                $str_math_expretion .= " ".$number_two_string[ $array_math_expretion[2] % 10 ];
            else {
                if ( get_bloginfo( 'language', 'Display' ) == "nl-NL" ) {
                    $str_math_expretion .= " ".( 0 != $array_math_expretion[2] % 10 ? $number_string[ $array_math_expretion[2] % 10 ]. __( "and", 'captcha' ) : '').$number_three_string[ $array_math_expretion[2] / 10 ];
                } else {
                    $str_math_expretion .= " ".$number_three_string[ $array_math_expretion[2] / 10 ]." ".( 0 != $array_math_expretion[2] % 10 ? $number_string[ $array_math_expretion[2] % 10 ] : '');
                }
            }
        } else {
            $str_math_expretion .= $array_math_expretion[2];
        }
        
        $_SESSION['dt_captcha'][$this->whoami] = self::encode( $array_math_expretion[$rand_input], DT_CAPTCHA_KEY );
        return $str_math_expretion;
    }
    
    function encode( $String, $Password ) {
        $Salt = 'BGuxLWQtKweKEMV4';
        $String = substr( pack( "H*", sha1( $String ) ), 0, 1 ).$String;
        $StrLen = strlen( $String );
        $Seq = $Password;
        $Gamma	= '';
        while ( strlen( $Gamma ) < $StrLen ) {
                $Seq = pack( "H*", sha1( $Seq . $Gamma . $Salt ) );
                $Gamma.=substr( $Seq, 0, 8 );
        }

        return base64_encode( $String ^ $Gamma );
    }
    
    function decode( $String, $Key ) {
        $Salt =	'BGuxLWQtKweKEMV4';
        $StrLen = strlen( $String );
        $Seq = $Key;
        $Gamma = '';
        while( strlen( $Gamma ) < $StrLen ) {
                $Seq = pack( "H*", sha1( $Seq . $Gamma . $Salt ) );
                $Gamma.= substr( $Seq, 0, 8 );
        }

        $String = base64_decode( $String );
        $String = $String^$Gamma;

        $dt_decodedString = substr( $String, 1 );
        $Error = ord( substr( $String, 0, 1 ) ^ substr( pack( "H*", sha1( $dt_decodedString ) ), 0, 1 )); 

        if ( $Error ) 
            return false;
        else 
            return $dt_decodedString;
    }
    
    function check( $namber, $place ) {
        if( (is_user_logged_in() && true == Dt_Captcha::$hide_register) ||
            ('form' == $place && !Dt_Captcha::$contact_form) ||
            ('widget' == $place && !Dt_Captcha::$contact_widget) ) {
            return 1;
        }
        
        if( !empty($namber) && !empty($place) && isset($_SESSION['dt_captcha'][$place]) ) {
            
            if( 0 == strcasecmp( trim( self::decode( $_SESSION['dt_captcha'][$place], DT_CAPTCHA_KEY ) ), $namber ) ) {
                unset($_SESSION['dt_captcha'][$place]);
                return 1;
            }else {
                return 2;
            }
        
        }else {
            return 3;
        }
    }
}// end class

function dt_add_captcha_to_form( $whoami ) {
    $c_form = new Dt_Captcha($whoami);
    echo '<div class="dt_captcha">' . $c_form->get_captcha() . '</div>';
}
add_action( 'dt_contact_form_captcha_place', 'dt_add_captcha_to_form', 10, 1 );

function dt_check_current_date() {
    $place = isset($_POST['send_contacts'])?trim($_POST['send_contacts']):'';
    $honey_msg = isset($_POST['send_message'])?trim($_POST['send_message']):'';
    $name = isset($_POST['f_name'])?trim(strip_tags($_POST['f_name'])):'';
    $email = isset($_POST['f_email'])?trim(strip_tags($_POST['f_email'])):'';
    $phone = isset($_POST['f_phone'])?trim(strip_tags($_POST['f_phone'])):'';
    $msg = isset($_POST['f_comment'])?trim(strip_tags($_POST['f_comment'])):'';
    $captcha = isset($_POST['cptch_number'])?trim(strip_tags($_POST['cptch_number'])):'';
    $pid = isset($_POST['pid'])?intval($_POST['pid']):false;
    $nonce = isset($_POST['nonce'])?$_POST['nonce']:false;
    
    
    $send = false;
    $errors = '';
    $check = Dt_Captcha::check($captcha, $place);
    
    if( !wp_verify_nonce($nonce,'dt_contact_'.$place) ) {
        $errors = __('Nonce do not match', LANGUAGE_ZONE);
    }elseif( 2 == $check ) {
        $errors = __('Captcha filled incorectly', LANGUAGE_ZONE);
    }elseif( 3 == $check ){
        $errors = __('Fill the captcha', LANGUAGE_ZONE);
    }elseif( $name && $email && $msg && 1 == $check && !$honey_msg ) {
        
        if( $pid ) {
            $data = get_post_meta( $pid, 'contact_options', true );
            $em = !empty($data['target_email'])?strip_tags($data['target_email']):get_bloginfo('admin_email');
        }else {
            $data = get_option('widget_sakura_Feedback');
            $em = !empty($data['email'])?strip_tags($data['email']):get_bloginfo('admin_email');
        }
		
		$headers = 'From: ' . stripslashes(strip_tags($name)) . '<' . stripslashes(strip_tags($email)) . '>' . "\r\n";
        $headers .= 'Reply-To: ' . stripslashes(strip_tags($email)) . "\r\n";
        
		$send = wp_mail(
            $em,
            'Feedback',
            "Someone wrote this to you:
            Name: ".stripslashes(strip_tags($name))."
            Email: ".stripslashes(strip_tags($email))."
            ". (isset($phone) && $phone?'Telephone: '.stripslashes(strip_tags($phone)):''). "
            Message: ".stripslashes(strip_tags($msg))."",
			$headers
        );
        if( $send ) {
            $errors = __('Feedback has been sent to the administrator', LANGUAGE_ZONE);
        }else {
            $errors = __('The message has not been sent', LANGUAGE_ZONE);
        }
        
        $nonce = wp_create_nonce('dt_contact_'.$place);
        
    }elseif( $honey_msg ) {
        $errors = __('Sorry, we suspect that you are bot', LANGUAGE_ZONE);
    }
    
    $c_form = new Dt_Captcha($place);
    $captcha = $c_form->get_captcha();
    
    //wp_nonce_field('dt_contact_'.$place,'dt_contact_form_nonce', false, false);
    
    $response = json_encode(
		array(
			'success'		=> $send ,
			'errors'        => $errors,
            'captcha'       => $captcha,
            'nonce'         => $nonce
		)
	);

	// response output
    header( "Content-Type: application/json" );
    echo $response;

    // IMPORTANT: don't forget to "exit"
    exit;
}
add_action( 'wp_ajax_nopriv_dt_check_current_date', 'dt_check_current_date' );
add_action( 'wp_ajax_dt_check_current_date', 'dt_check_current_date' );

?>