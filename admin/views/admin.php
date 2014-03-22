<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   HTMLImportPlugin
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Your Name or Company Name
 */
if ( ( isset( $_POST['action'] ) ) && ( 'save' == $_POST['action'] ) ) {
	// TODO: statically set path for now, should read from field
	//$_POST['import-xml'] = '/Users/patrick/DevWork/websites/wordpress_ms_3_8_1/GDDN/sample.xml';

	if (isset($_POST['submit'])) {
		if (isset($_POST['import-xml'])) {
			$path = sanitize_text_field(trim($_POST['import-xml']));

			HTMLImportPlugin::get_instance()->import_html_from_xml_index( $path );
		}
	}
}

?>

<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<h4><?php _e( 'What are you importing today?' ); ?></h4>

	<form method="post" action="">
		<label for=""><?php _e('Parent Page:', 'import-html-pages');?></label>
		TODO: dropdown here

		<label for=""><?php _e('Template:', 'import-html-pages');?></label>
		TODO: dropdown here

		<p id="xml">
			<label for="import-xml"><?php _e( 'Enter in the absolute file location of the index XML file:', 'import-html-pages' ); ?></label>
			<input type="text" id="import-xml" name="import-xml" size="50" />
		</p>

		<input type="hidden" name="action" value="save" />

		<p class="submit">
			<input type="submit" name="submit" class="button" value="<?php echo esc_attr( __( 'Import', 'import-html-pages' ) ); ?>" />
		</p>
		<?php wp_nonce_field( 'html-import' ); ?>
	</form>


</div>
