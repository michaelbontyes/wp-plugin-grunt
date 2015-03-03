<?php  
/* 
Plugin Name: Wordpress Plugin Grunt
Plugin URI: https://github.com/michaelbontyes/wp-plugin-grunt
Version: 0.1
Author: Michael Bontyes
Description: This plugin helps you to manage your Grunt Tasks from your Wordpress project.
*/

add_action('admin_menu', 'grunt_plugin_setup_menu');

function grunt_plugin_setup_menu(){
    add_menu_page( 'Wordpress Grunt', 'Wordpress Grunt', 'manage_options', 'wordpress-grunt', 'grunt_init' , 'http://i.imgur.com/DfqTMfQ.png');
}

function grunt_init(){
    echo '<h1>Grunt Interface <img src="//i.imgur.com/DfqTMfQ.png"></h1>';


    echo '<button id="syncdb" type="button">DB Sync!</button><p id="response"></p>';

    extra_post_info_page();
}

add_action( 'admin_footer', 'my_action_javascript' ); // Write our JS below here

function my_action_javascript() { ?>
    <script type="text/javascript" >
        var $j = jQuery.noConflict();
        $j('#syncdb').on('click' , function() {
            $j('#response').html('<img src="http://upload.wikimedia.org/wikipedia/commons/5/53/Loading_bar.gif">');
            var environment = $j('#environment').val();
            var command = 'wp core ' + environment;

            var data = {
                'action': 'my_action',
                'command': command,
                'environment': environment
            };

            $j.post(ajaxurl, data, function(response) {
                $j('#response').html(response);
            });
        });
    </script> <?php
}

add_action( 'wp_ajax_my_action', 'my_action_callback' );

function my_action_callback() {
    global $wpdb; // this is how you get access to the database
    $environment = $_POST['environment'];
    $response = shell_exec( $_POST['command']);
    echo $environment .' '. $response .' '. get_option( 'extra_post_info' );
    wp_die(); // this is required to terminate immediately and return a proper response
}

function extra_post_info_page(){
    ?>
    <form method="post" action="options.php">
        <?php settings_fields( 'extra-post-info-settings' ); ?>
        <?php do_settings_sections( 'extra-post-info-settings' ); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Extra post info:</th>
                <td><input type="text" id="environment" name="extra_post_info" value="<?php echo get_option( 'extra_post_info' ); ?>"/></td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>

<?php
}


if( !function_exists("update_extra_post_info") ) {
    function update_extra_post_info() {
        register_setting( 'extra-post-info-settings', 'extra_post_info' );
    }
}
add_action( 'admin_init', 'update_extra_post_info' );

if( !function_exists("extra_post_info") )
{

    function extra_post_info($content)
    {
        $extra_info = get_option( 'extra_post_info' );
        return $content . $extra_info;
    }

    add_filter( 'the_content', 'extra_post_info' );

}

?>  