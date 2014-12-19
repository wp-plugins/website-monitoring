<?php
/*
Plugin Name: Super Monitoring
Plugin URI: http://www.supermonitoring.com/p/wordpress-plugin
Description: Monitor your blog's uptime with www.supermonitoring.com services - and have all the charts and tables displayed in your WordPress panel. 
Author: SITEIMPULSE
Author URI: http://www.siteimpulse.com/
Version: 2.4
*/

if ( !defined( 'WP_CONTENT_URL' ) )
	define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( !defined( 'WP_CONTENT_DIR' ) )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( !defined( 'WP_PLUGIN_URL' ) )
	define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( !defined( 'WP_PLUGIN_DIR' ) )
	define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );


	function adminMenu()
	{
	    	$data = get_option("monitoringst");
		if ( file_exists ( dirname ( __FILE__) . '/' . $data['language'] . '.php' ) ) require ( dirname ( __FILE__) . '/' . $data['language'] . '.php' );
		
		$api = 'http://www.supermonitoring.pl/API/';

		add_options_page( "Super Monitoring", "Super Monitoring", 8, __FILE__, "settingsPage" );

		//$data = get_option("monitoringst");

		if ( $data["token"] != '' ) {

	        add_menu_page('Super Monitoring', 'Super Monitoring', 10, 'msmenu',"msservicesPage", WP_PLUGIN_URL . '/website-monitoring/screen.png' );
	        add_submenu_page( 'msmenu', 'Super Monitoring', $lang['menu_1'], 'administrator', 'msmenu', "msservicesPage" );
	        add_submenu_page( 'msmenu', 'Super Monitoring', $lang['menu_2'], 'administrator', 'msmenu1', "mssettingPage" );
		add_submenu_page( 'msmenu', 'Super Monitoring', $lang['menu_3'], 'administrator', 'msmenu2', "mscontactsPage" );

        }

	}

	function activate()
	{
		$data = array( "token" => "", "language" => "EN" );
		if( !get_option("monitoringst") ) {
			add_option( "monitoringst", $data );
		} else {
			update_option( "monitoringst", $data );
		}
	}

	function deactivate() {
		delete_option( "monitoringst" );
	}

	function mscontactsPage () {
		
		$data = get_option("monitoringst");
		if ( file_exists ( dirname ( __FILE__) . '/' . $data['language'] . '.php' ) ) require ( dirname ( __FILE__) . '/' . $data['language'] . '.php' );
		
		echo "<iframe id=\"frame\" width=\"100%\" frameborder=\"0\" src=\"http://" . $lang['service_domain'] . "/index.php?wp_token=$data[token]&s=contacts\"></iframe>";
        ?>
		<script type="text/javascript">
		function resizeIframe() {
		    var height = document.documentElement.clientHeight;
		    height -= document.getElementById('frame').offsetTop;

		    // not sure how to get this dynamically
		    height -= 20; /* whatever you set your body bottom margin/padding to be */

		    document.getElementById('frame').style.height = height +"px";

		};
		document.getElementById('frame').onload = resizeIframe;
		window.onresize = resizeIframe;
		</script>
        <?
	}

	function msservicesPage () {
		
		$data = get_option("monitoringst");
		if ( file_exists ( dirname ( __FILE__) . '/' . $data['language'] . '.php' ) ) require ( dirname ( __FILE__) . '/' . $data['language'] . '.php' );
		
		echo "<iframe id=\"frame\" width=\"100%\" frameborder=\"0\" src=\"http://" . $lang['service_domain'] . "/index.php?wp_token=$data[token]\"></iframe>";
        ?>
		<script type="text/javascript">
		function resizeIframe() {
		    var height = document.documentElement.clientHeight;
		    height -= document.getElementById('frame').offsetTop;

		    // not sure how to get this dynamically
		    height -= 20; /* whatever you set your body bottom margin/padding to be */

		    document.getElementById('frame').style.height = height +"px";

		};
		document.getElementById('frame').onload = resizeIframe;
		window.onresize = resizeIframe;
		</script>
        <?	}
	
	function mssettingPage () {

		$data = get_option("monitoringst");
		if ( file_exists ( dirname ( __FILE__) . '/' . $data['language'] . '.php' ) ) require ( dirname ( __FILE__) . '/' . $data['language'] . '.php' );

		echo "<iframe id=\"frame\" width=\"100%\" frameborder=\"0\" src=\"http://" . $lang['service_domain'] . "/index.php?wp_token=$data[token]&s=settings\"></iframe>";
        ?>
		<script type="text/javascript">
		function resizeIframe() {
		    var height = document.documentElement.clientHeight;
		    height -= document.getElementById('frame').offsetTop;

		    // not sure how to get this dynamically
		    height -= 20; /* whatever you set your body bottom margin/padding to be */

		    document.getElementById('frame').style.height = height +"px";

		};
		document.getElementById('frame').onload = resizeIframe;
		window.onresize = resizeIframe;
		</script>
        <?
	}

    function getApiResponse ( $token )
    {
	$api = 'http://www.supermonitoring.pl/API/';

        $curl = curl_init();
        curl_setopt ( $curl, CURLOPT_URL,$api );
        curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ( $curl, CURLOPT_POST, true );
        $string = 'f=wp_token&token=' . $token;
        curl_setopt ($curl, CURLOPT_POSTFIELDS, $string);
        $result = curl_exec ( $curl );
        curl_close ( $curl );

	return $result;


    }

	function settingsPage()
	{

		$data = get_option("monitoringst");
		if ( file_exists ( dirname ( __FILE__) . '/' . $_POST['monitoringst-language'] . '.php' ) ) require ( dirname ( __FILE__) . '/' . $_POST['monitoringst-language'] . '.php' );
		
		if( $_POST["monitoringst-submit"] )
		{

			$currentLang = $data['language'];
			if ( $currentLang != $_POST['monitoringst-language'] ) {
				if ( file_exists ( dirname ( __FILE__) . '/' . $_POST['monitoringst-language'] . '.php' ) ) require ( dirname ( __FILE__) . '/' . $_POST['monitoringst-language'] . '.php' );
			}
			
			//$isCorrect = getApiResponse ( $_POST["monitoringst-username"], $_POST["monitoringst-password"],$_POST["monitoringst-version"] );
			$isCorrect = getApiResponse ( $_POST["monitoringst-token"] );
			if ( $isCorrect == '0' ) {
			        echo "<div id='ws-warning' class='error'><p>" . $lang['error_1'] . "</p></div>";
			} else {

				list ( $return, $link ) = explode ( '|', $isCorrect );
				echo "<div id='ws-warning' class='updated fade'><p>" . $lang['confirm_1'] . "</p></div>";
					    $data["token"] = attribute_escape( $_POST["monitoringst-token"] );
					    $data["language"] = attribute_escape( $_POST["monitoringst-language"] );
					    //$data["username"] = attribute_escape( $_POST["monitoringst-username"] );
					    //$data["password"] = attribute_escape( $_POST["monitoringst-password"] );
					    $data["version"] = attribute_escape( $_POST["monitoringst-version"] );
					    $data["link"] = $link;
					    update_option( "monitoringst", $data );					    

			}

		} else {
			$data = get_option( "monitoringst" );

		}


		if ( $data['language'] != '' && file_exists ( dirname ( __FILE__) . '/' . $data['language'] . '.php' ) ) require ( dirname ( __FILE__) . '/' . $data['language'] . '.php' );
		else require ( dirname ( __FILE__) . '/EN.php' );

	?>
	<div class="wrap">
		<h2><?php echo $lang['sett_1'] ?></h2>
		<p><?php echo $lang['sett_2'] ?></p>
		<form method="post">

		<table class="form-table">
		<tbody>
		<tr valign="top">
		<th scope="row"><label for="monitoringst-token"><?php echo $lang['sett_3'] ?></label></th>
		<td><input type="text" name="monitoringst-token" class="regular-text code" value="<?php echo $data["token"]; ?>" />
		<p class="description"><?php echo $lang['sett_7'] ?></p></td>
		</tr>

		<tr valign="top">
		<th scope="row"><label for="monitoringst-language"><?php echo $lang['sett_4'] ?></label></th>
		<td><select name="monitoringst-language">
			<option value="EN" <?php if ( $data["language"] == 'EN' ) echo "selected"; ?>>English</option>
			<option value="PL" <?php if ( $data["language"] == 'PL' ) echo "selected"; ?>>polski</option>
			<option value="ES" <?php if ( $data["language"] == 'ES' ) echo "selected"; ?>>español</option>
		</select>
		</td>
		</tr>
		</tbody></table>

		<p class="submit"><input type="submit" value="<?php echo $lang['sett_5'] ?>" class="button button-primary" name="monitoringst-submit"></p>
		
		</form>

		<div style="float: left; background-color: white; padding: 10px; margin-right: 15px; border: 1px solid rgb(221, 221, 221);max-width: 700px;">
		<?php echo $lang['sett_6']; ?>
		</div>

	<?php

	}

	function preparation_message () {		$data = get_option("monitoringst");
		if ( file_exists ( dirname ( __FILE__) . '/' . $data['language'] . '.php' ) ) require ( dirname ( __FILE__) . '/' . $data['language'] . '.php' );
		$data = get_option("monitoringst");
		if ( $data['token'] == '' )
		echo "<div id='ws-warning' class='updated fade'><p>" . $lang['sett_0'] . "</p></div>";
	}

add_action( "admin_menu", "adminMenu" );
add_action('admin_notices', 'preparation_message');
register_activation_hook( __FILE__, "activate" );
register_deactivation_hook( __FILE__, "deactivate" );

?>