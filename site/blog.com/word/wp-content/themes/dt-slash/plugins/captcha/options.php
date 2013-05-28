<?php
$options[] = array( "name" => __( "Captcha", 'dt-options-name'),
					"type" => "heading" );
// enable for
$options[] = array(	"name" => __( 'Enable CAPTCHA on the:', 'dt-options-name'),
					"type" => "block_begin" );                    

// hide for register
$options[] = array( "name" => "",
                    "desc" => __( "hide CAPTCHA for register users", 'dt-options-desc' ),
                    "id" => "captcha_hide_register",
                    "std" => "1",
                    "type" => "checkbox" );
                    
// for contact form
$options[] = array( "name" => "",
                    "desc" => __( "contact form", 'dt-options-desc' ),
                    "id" => "captcha_contact_form",
                    "std" => "1",
                    "type" => "checkbox" );

// for contact form
$options[] = array( "name" => "",
                    "desc" => __( "get in touch widget", 'dt-options-desc' ),
                    "id" => "captcha_contact_widget",
                    "std" => "1",
                    "type" => "checkbox" );

$options[] = array(	"type" => "block_end" );

$options[] = array( "name"  => '',
                    "desc"  => "label vor CAPTCHA in form",
                    "id"    => "captcha_label_form",
                    "std"   => '',
                    "type"  => "text" );

// arithmetic
$options[] = array(	"name" => __( 'Arithmetic actions for CAPTCHA:', 'dt-options-name'),
					"type" => "block_begin");
/*
// plus
$options[] = array( "name" => "",
                    "desc" => __( "plus (+)", 'dt-options-desc' ),
                    "id" => "captcha_math_action_plus",
                    "std" => "1",
                    "type" => "checkbox" );
*/
// minus
$options[] = array( "name" => "",
                    "desc" => __( "minus (-)", 'dt-options-desc' ),
                    "id" => "captcha_math_action_minus",
                    "std" => "1",
                    "type" => "checkbox" );
                    
// multiply
$options[] = array( "name" => "",
                    "desc" => __( "multiply (x)", 'dt-options-desc' ),
                    "id" => "captcha_math_action_increase",
                    "std" => "1",
                    "type" => "checkbox" );
                    
$options[] = array(	"type" => "block_end");

// difficulty
$options[] = array(	"name" => __( 'Difficulty for CAPTCHA:', 'dt-options-name'),
					"type" => "block_begin");
                    
// numbers
$options[] = array( "name" => "",
                    "desc" => __( "numbers", 'dt-options-desc' ),
                    "id" => "captcha_difficulty_number",
                    "std" => "1",
                    "type" => "checkbox" );
                    
// words
$options[] = array( "name" => "",
                    "desc" => __( "words", 'dt-options-desc' ),
                    "id" => "captcha_difficulty_word",
                    "std" => "1",
                    "type" => "checkbox" );
                    
$options[] = array(	"type" => "block_end");
?>