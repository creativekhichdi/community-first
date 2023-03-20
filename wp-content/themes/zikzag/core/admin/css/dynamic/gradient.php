<?php
if ( !defined( 'ABSPATH' ) ) { exit; }

$css .= '
.theme-gradient input[type="submit"]{';
if ( (bool)$use_gradient_switch ) {
	$css .= '
		background: -webkit-linear-gradient(left, '.$theme_gradient_from.' 0%, '.$theme_gradient_to.' 50%, '.$theme_gradient_from.' 100%);
		background-size: 300%, 1px;
		background-position: 0%;
	}';
} else {
	$css .= 'background-color:'.$theme_primary_color.';}';
}

?>