<?php

$contributors = array();
$contributors['Mark Smalley']['twitter'] = 'm_smalley';
$contributors['Ali Watters']['twitter'] = 'ali_watters';
$contributors['Sundar']['twitter'] = 'sundardotmy';
$contributors['Ross Affandy']['twitter'] = 'rossaffandy';
$contributors['Dattas Moonchaser']['twitter'] = 'dattas';

$contribution = '';
foreach($contributors as $person =>$details){
	if(empty($contribution)){
		$contribution = '<a href="http://twitter.com/'.$details['twitter'].'">'.$person.'</a>';
	}else{
		$contribution.= ', <a href="http://twitter.com/'.$details['twitter'].'">'.$person.'</a>';
	}
}