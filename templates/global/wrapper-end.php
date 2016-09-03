<?php
/**
 * Content wrappers.
 *
 * This template can be overridden by copying it to yourtheme/real-estate/global/wrapper-end.php.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Templates
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

$template = get_option( 'template' );

switch ( $template ) {
	case 'twentyeleven' :
		echo '</div>';
		get_sidebar( 'shop' );
		echo '</div>';
		break;

	case 'twentytwelve' :
		echo '</div></div>';
		break;

	case 'twentythirteen' :
		echo '</div></div>';
		break;

	case 'twentyfourteen' :
		echo '</div></div></div>';
		get_sidebar( 'content' );
		break;

	case 'twentyfifteen' :
		echo '</div></div>';
		break;

	case 'twentysixteen' :
		echo '</main></div>';
		break;

	default :
		echo '</div></div>';
		break;
}
