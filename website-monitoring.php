<?php
/*
Plugin Name: Website Monitoring
Plugin URI: http://www.website-monitoring.com/?ps=10
Description: Start to monitor your blog's uptime with www.website-monitoring.com services - and have all the charts and tables displayed in your WordPress panel.
Author: SITEIMPULSE
Author URI: http://www.siteimpulse.eu/
Version: 1.1
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
	    	$api = 'http://www.website-monitoring.com/API/';

		add_options_page( "Website Monitoring", "Website Monitoring", 8, __FILE__, "settingsPage" );

		$data = get_option("wsmonitoring");

		if ( $data["username"] != '' ) {

	        add_menu_page('Website Monitoring', 'Website Monitoring', 10, 'wmmenu',"wmservicesPage", WP_PLUGIN_URL . '/website-monitoring/wm2.png' );
	        add_submenu_page( 'wmmenu', 'Website Monitoring', 'Your Services', 'administrator', 'wmmenu', "wmservicesPage" );
	        add_submenu_page( 'wmmenu', 'Website Monitoring', 'Your Settings', 'administrator', 'wmmenu1', "wmsettingsPage" );

        }

	}

	function activate()
	{
		$data = array( "username" => "", "password" => "" );
		if( !get_option("wsmonitoring") ) {
			add_option( "wsmonitoring", $data );
		} else {
			update_option( "wsmonitoring", $data );
		}
	}

	function deactivate() {
		delete_option( "wsmonitoring" );
	}

	function wmservicesPage () {
	    $data = get_option("wsmonitoring");

	    echo "<iframe id=\"frame\" width=\"100%\" frameborder=\"0\" src=\"http://www.website-monitoring.com/index.php?action=dologin&u1=$data[username]&p1=$data[password]&wp=1\"></iframe>";
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
	function wmsettingsPage () {

	    $data = get_option("wsmonitoring");

	    echo "<iframe id=\"frame\" width=\"100%\" frameborder=\"0\" src=\"http://www.website-monitoring.com/index.php?action=dologin&u1=$data[username]&p1=$data[password]&s=settings&wp=1\"></iframe>";
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

    function getApiResponse ( $u, $p )
    {
        $api = 'http://www.website-monitoring.com/API/';

        $curl = curl_init();
        curl_setopt ( $curl, CURLOPT_URL,$api );
        curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $curl, CURLOPT_POST, 1 );
        $string = 'f=ue&u1=' . $u . '&p1=' . $p;
        curl_setopt ($curl, CURLOPT_POSTFIELDS, $string);
        $result= curl_exec ( $curl );
        curl_close ( $curl );

        return $result;


    }

	function settingsPage()
	{

		if( $_POST["wsmonitoring-submit"] )
		{

			$isCorrect = getApiResponse ( $_POST["wsmonitoring-username"], $_POST["wsmonitoring-password"] );
			if ( $isCorrect == '0' )
			     echo "<div id='ws-warning' class='updated fade'><p><strong>Incorrect Login and/or Password.</strong><br />If you forgot your password, please click <a href=\"http://www.website-monitoring.com/index.php?s=preset\" target=\"_blank\">here</a> to reset it.</p></div>";
			else {

    		    list ( $return, $link ) = explode ( '|', $isCorrect );
    		    echo "<div id='ws-warning' class='updated fade'><p>Login and password have been confirmed and saved.</p></div>";
				$data["username"] = attribute_escape( $_POST["wsmonitoring-username"] );
				$data["password"] = attribute_escape( $_POST["wsmonitoring-password"] );
				$data["link"] = $link;
				update_option( "wsmonitoring", $data );

            }

		} else {
			$data = get_option( "wsmonitoring" );

		}


	?>
	<div class="wrap">
		<h2>Website Monitoring settings</h2>
		<p>If you already have a service at www.website-monitoring.com, just enter your login and password to have it integrated with WordPress panel.</p>
		<form method="post">
		<p>Login:<br />
		<input type="text" name="wsmonitoring-username" value="<?php echo $data["username"]; ?>" /></p>
		<p>Password:<br />
		<input type="password" name="wsmonitoring-password" value="<?php echo $data["password"]; ?>" /></p>
		<p class="submit"><input type="submit" name="wsmonitoring-submit" value="SAVE" /></p>
		</form>

		<? if ( $data['link'] != '' ) { ?>
		<div style="float: left; background-color: #ffffe0; padding: 10px; margin-right: 10px; margin-bottom: 10px; border: 1px solid #e6db55;">
		<div>
		<h3>Important</h3>
		It seems you are using a free service.<br />
		Please remember, that using a free service requires publishing a microbanner (<img src="http://free.website-monitoring.com/images/website-monitoring.gif" alt="website monitoring" />) on the monitored website. Otherwise - after a few days - the service will automatically cease to function.<br />
		Please just copy the following code, go to <a href="./theme-editor.php">Theme Editor</a> and paste it into your templates - for example at the bottom of footer.php (but before the <i>&lt;/body&gt;</i> tag).<br />
		<br />
		<i>&lt;a href="http://www.website-monitoring.com/" target="_blank" title="website monitoring"&gt;&lt;img src="<?echo $data['link']?>" border="0" alt="website monitoring" /&gt;&lt;/a&gt;</i><br />
		<br />
		</div>
		</div>
		<? } ?>

		<p>Otherwise, sign up for it by choosing one of the following options:</p>

		<div style="float: left; background-color: white; padding: 10px; margin-right: 15px; border: 1px solid rgb(221, 221, 221);">
		<div style="width: 400px">
		<h3>Commercial service</h3>
		&bull; tests every 1 minute<br />
		&bull; 3 monitoring servers (North America, Europe, Asia)<br />
    		&bull; watching different services/protocols (http, https, pop3, smtp, imap, telnet, ssh, dns, connect, sip)<br />
    		&bull; no installation (script) needed<br />
    		&bull; instant email and SMS alerts<br />
    		&bull; RSS feed with notifications<br />
	        &bull; full uptime/downtime history<br />
    		&bull; data export to XLS<br />
    		&bull; integration with Google Analytics panel<br />
    		&bull; 200 SMS reserve included<br />
    		&bull; price: only $59/year (less than $5/month)<br />
    		<br />
		<a href="http://www.website-monitoring.com/?s=order" target="_blank">sign up for a <b>14-day free trial period</b></a>
		</div>
		</div>

		<div style="float: left; background-color: white; padding: 10px; border: 1px solid rgb(221, 221, 221);">
		<div style="width: 400px">
		<h3>Free Service</h3>
    		&bull; checks every 5 minutes<br />
    		&bull; 3 monitoring servers (North America, Europe, Asia)<br />
    		&bull; instant email alerts<br />
	        &bull; full uptime/downtime history<br />
	        &bull; requires publishing a microbanner (<img src="http://free.website-monitoring.com/images/website-monitoring.gif" alt="website monitoring" />) on your website<br />
		<br />
		<a href="http://free.website-monitoring.com/" target="_blank">sign up for a <b>free service</b></a></p>
		</div>
		</div>

	<?php

	}

	function preparation_message () {		$data = get_option("wsmonitoring");
		if ( $data['username'] == '' )
		echo "<div id='ws-warning' class='updated fade'><p><strong>Website Monitoring</strong> plugin is almost ready. Please update <a href=\"options-general.php?page=website-monitoring/website-monitoring.php\">the configuration</a>.</p></div>";
	}

add_action( "admin_menu", "adminMenu" );
add_action('admin_notices', 'preparation_message');
register_activation_hook( __FILE__, "activate" );
register_deactivation_hook( __FILE__, "deactivate" );

?>