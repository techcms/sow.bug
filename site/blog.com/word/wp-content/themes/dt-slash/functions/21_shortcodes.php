<?php
/* shortcodes: begin */

function clear() {
    return '<br class="clear" />';
}
add_shortcode('clear', 'clear');


function one_fourth($atts, $content = null, $align = null) {
	extract(shortcode_atts(array(
		"align" => '',
		"last" => '',
		"frame" => ''
	), $atts)); 
	
	foreach (explode(" ", "align frame last") as $k)
	{
	   if ( !isset($atts[$k]) )
	      $atts[$k] = "";
	   if ( !isset($$k) )
	      $$k = "";
	}
	
	if ($atts['align']=='right') $align=' right_align';
	if ($atts['last']=='true' or $atts['last']=='yes' or $atts['last']=='t' or $atts['last']=='y'  or $atts['last']=='1') $align=$align.' last_col';
	if ($atts['frame']=='true' or $atts['frame']=='yes' or $atts['frame']=='t' or $atts['frame']=='y'  or $atts['frame']=='1') $frame=' framed';
    return '<div class="one-fourth'.$align.''.$frame.'">'.$content.'</div>';
}
add_shortcode('one-fourth', 'one_fourth');


function three_fourth($atts, $content = null, $align = null) {
	extract(shortcode_atts(array(
		"align" => '',
		"last" => '',
		"frame" => ''
	), $atts)); 
	
	foreach (explode(" ", "align frame last") as $k)
	{
	   if ( !isset($atts[$k]) )
	      $atts[$k] = "";
	   if ( !isset($$k) )
	      $$k = "";
	}
	
	$atts = (array)$atts;
	if ($atts['align']=='right') $align=' right_align';
	if ($atts['last']=='true' or $atts['last']=='yes' or $atts['last']=='t' or $atts['last']=='y'  or $atts['last']=='1') $align=$align.' last_col';
	if ($atts['frame']=='true' or $atts['frame']=='yes' or $atts['frame']=='t' or $atts['frame']=='y'  or $atts['frame']=='1') $frame=' framed';
     return '<div class="three-fourth'.$align.''.$frame.'">'.$content.'</div>';
}
add_shortcode('three-fourth', 'three_fourth');


function one_third($atts, $content = null, $align = null) {
	extract(shortcode_atts(array(
		"align" => '',
		"last" => '',
		"frame" => ''
	), $atts)); 
	
	foreach (explode(" ", "align frame last") as $k)
	{
	   if ( !isset($atts[$k]) )
	      $atts[$k] = "";
	   if ( !isset($$k) )
	      $$k = "";
	}
	
	if ($atts['align']=='right') $align=' right_align';
	if ($atts['last']=='true' or $atts['last']=='yes' or $atts['last']=='t' or $atts['last']=='y'  or $atts['last']=='1') $align=$align.' last_col';
	if ($atts['frame']=='true' or $atts['frame']=='yes' or $atts['frame']=='t' or $atts['frame']=='y'  or $atts['frame']=='1') $frame=' framed';
    return '<div class="one-third'.$align.''.$frame.'">'.$content.'</div>';
}
add_shortcode('one-third', 'one_third');


function two_third($atts, $content = null, $align = null) {
	extract(shortcode_atts(array(
		"align" => '',
		"last" => '',
		"frame" => ''
	), $atts)); 
	
	foreach (explode(" ", "align frame last") as $k)
	{
	   if ( !isset($atts[$k]) )
	      $atts[$k] = "";
	   if ( !isset($$k) )
	      $$k = "";
	}
	
	if ($atts['align']=='right') $align=' right_align';
	if ($atts['last']=='true' or $atts['last']=='yes' or $atts['last']=='t' or $atts['last']=='y'  or $atts['last']=='1') $align=$align.' last_col';
	if ($atts['frame']=='true' or $atts['frame']=='yes' or $atts['frame']=='t' or $atts['frame']=='y'  or $atts['frame']=='1') $frame=' framed';
    return '<div class="two-third'.$align.''.$frame.'">'.$content.'</div>';
}
add_shortcode('two-third', 'two_third');


function one_half($atts, $content = null) {
	extract(shortcode_atts(array(
		"align" => '',
		"last" => '',
		"frame" => ''
	), $atts)); 
	
	foreach (explode(" ", "align frame last") as $k)
	{
	   if ( !isset($atts[$k]) )
	      $atts[$k] = "";
	   if ( !isset($$k) )
	      $$k = "";
	}
	
	if ($atts['align']=='right') $align=' right_align';
	if ($atts['last']=='true' or $atts['last']=='yes' or $atts['last']=='t' or $atts['last']=='y'  or $atts['last']=='1') $align=$align.' last_col';
	if ($atts['frame']=='true' or $atts['frame']=='yes' or $atts['frame']=='t' or $atts['frame']=='y'  or $atts['frame']=='1') $frame=' framed';
     return '<div class="one-half'.$align.''.$frame.'">'.$content.'</div>';
}
add_shortcode('one-half', 'one_half');


function frame($atts, $content = null, $align = null) {
	extract(shortcode_atts(array(
		"align" => ''
	), $atts)); 
	
	foreach (explode(" ", "align frame last") as $k)
	{
	   if ( !isset($atts[$k]) )
	      $atts[$k] = "";
	   if ( !isset($$k) )
	      $$k = "";
	}
	
	if ($atts['align']=='right') $align=' right_align';
	if ($atts['align']=='left') $align=' left_align';
    return '<div class="framed'.$align.'">'.$content.'</div>';
}
add_shortcode('frame', 'frame');


function toggle($atts, $content = null) {
	extract(shortcode_atts(array(
		"title" => ''
	), $atts)); 
    return '<div class="toggle"><a href="#" class="question"><i class="q_a"></i>'.$title.'</a><div class="answer" style="display: none;">'.$content.'</div></div>';
}
add_shortcode('toggle', 'toggle');


function question($atts, $content = null) {
	extract(shortcode_atts(array(
		"frame" => ''
	), $atts)); 
	if ($atts['frame']=='true' or $atts['frame']=='yes' or $atts['frame']=='t' or $atts['frame']=='y'  or $atts['frame']=='1') $frame=' framed';
    return '<div class="question'.$frame.'"><div class="que_ico"></div>'.$content.'</div>';
}
add_shortcode('question', 'question');

function alert($atts, $content = null) {
	extract(shortcode_atts(array(
		"frame" => ''
	), $atts)); 
	if ($atts['frame']=='true' or $atts['frame']=='yes' or $atts['frame']=='t' or $atts['frame']=='y'  or $atts['frame']=='1') $frame=' framed';
    return '<div class="alert'.$frame.'"><div class="alert_ico"></div>'.$content.'</div>';
}
add_shortcode('alert', 'alert');

function approved($atts, $content = null) {
	extract(shortcode_atts(array(
		"frame" => ''
	), $atts)); 
	if ($atts['frame']=='true' or $atts['frame']=='yes' or $atts['frame']=='t' or $atts['frame']=='y'  or $atts['frame']=='1') $frame=' framed';
    return '<div class="approved'.$frame.'"><div class="approved_ico"></div>'.$content.'</div>';
}
add_shortcode('approved', 'approved');


function tooltip($atts, $content = null) {
	extract(shortcode_atts(array(
		"title" => '',
		"href" => ''
	), $atts)); 
	if ( !isset($atts['frame']) )
	   $atts['frame'] = "";
	if ( !isset($frame) )
	   $frame = "";
	if ( !isset($atts['href']) )
	   $atts['href'] = "";
	if ($atts['frame']=='true' or $atts['frame']=='yes' or $atts['frame']=='t' or $atts['frame']=='y'  or $atts['frame']=='1') $frame=' framed';
	if (!$atts['href']) { $tag='span'; $href='';} else { $tag='a';  $href='href="'.$atts['href'].'"';}
    return '<'.$tag.' '.$href.' class="tooltip'.$frame.'">'.$title.'<span class="tooltip_c">'.$content.'<span class="tooltip-b"></span></span></'.$tag.'>';
}
add_shortcode('tooltip', 'tooltip');

//Image with caption filter
function fb_img_caption_shortcode($attr, $content = null) {
	// Allow plugins/themes to override the default caption template.
	$output = apply_filters('img_caption_shortcode', '', $attr, $content);
	if ( $output != '' )
		return $output;
	extract(shortcode_atts(array(
		'id'	=> '',
		'align'	=> 'alignnone',
		'width'	=> '',
		'caption' => ''
	), $attr));
 $id = 'id="' . $id . '" ';
	return '<div ' . $id . 'class="wp-caption ' . $align . '" style="width: ' . ((int) $width) . 'px">'
	. do_shortcode( $content ) . '<p class="wp-caption-text">' . $caption . '</p></div>';
}
add_shortcode('wp_caption', 'fb_img_caption_shortcode');
add_shortcode('caption', 'fb_img_caption_shortcode');
add_shortcode('img_caption_shortcode', 'fb_img_caption_shortcode');

/* shortcodes: end */