<?php

/**
 * Plugin Name:       WP Meta Repeater
 * Description:       This is Meta Repeater plugin. Its used to create dynamic meta fields for post ( or ) custon post.
 * Version:           1.0
 * Author:            Anandaraj balu
 * Author URI:        https://profiles.wordpress.org/anand000
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpmetarepeater
 * Domain Path:       /languages
 */

/*  Copyright 2014-2017 WP Smart Plugin

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
function activate_wpmetarepeater() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpmetarepeater-activator.php';
	Wpmetarepeater_Activator::activate();
}

function deactivate_wpmetarepeater() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpmetarepeater-deactivator.php';
	Wpmetarepeater_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wpmetarepeater' );
register_deactivation_hook( __FILE__, 'deactivate_wpmetarepeater' );

require plugin_dir_path( __FILE__ ) . 'includes/class-wpmetarepeater.php';

function run_wpmetarepeater() {

	$plugin = new Wpmetarepeater();
	$plugin->run();

}
run_wpmetarepeater();


//Out Put Functions
function the_wpmr_field( $metakey ){
	echo '<p>'.get_post_meta( get_the_ID(), 'wpmr_'.$metakey, true).'</p>';
}

function get_wpmr_field( $keyid, $metakey ){
	$meta_data = get_post_meta( $keyid, 'wpmr_'.$metakey, true);		
	return $meta_data;
}

function the_wpmr_repeater(){
	
	$the_repeaterkey = get_post_meta( get_the_ID(), 'wpmr_label_key', true );	?>
		<div class="wpmr_repeaterwrapper">
			<?php foreach( $the_repeaterkey as $wpmr_repeater ){ ?>			
					<p> <?php echo get_post_meta( get_the_ID(), 'wpmr_'.$wpmr_repeater, true ); ?> </p>			
			<?php }	?>
		</div>
<?php	
}

function get_wpmr_repeater( $keyid ){
	
	$the_repeaterkey = get_post_meta( $keyid, 'wpmr_label_key', true );	?>
		<div class="wpmr_repeaterwrapper">
			<?php 
				foreach( $the_repeaterkey as $wpmr_repeater ){ 
					$meta_data .= '<p>'.get_post_meta( get_the_ID(), 'wpmr_'.$wpmr_repeater, true ).'</p>' . " ";
				}
				return $meta_data;
			?>
		</div>
<?php	
}