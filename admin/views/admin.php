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
$plugin_options_arr = get_site_option('htim_importer_options');

if (isset($plugin_options_arr['import-xml'])) {
	$import_xml = $plugin_options_arr['import-xml'];
} else {
	$import_xml = '';
}
if (isset($plugin_options_arr['parent_page'])) {
	$parent_page = $plugin_options_arr['parent_page'];
} else {
	$parent_page = 0;
}
if (isset($plugin_options_arr['template'])) {
	$template = $plugin_options_arr['template'];
} else {
	$template = 0;
}

if ( ( isset( $_POST['action'] ) ) && ( 'save' == $_POST['action'] ) ) {
	// TODO: statically set path for now, should read from field
	//$_POST['import-xml'] = '/Users/patrick/DevWork/websites/wordpress_ms_3_8_1/GDDN/sample.xml';

	if (isset($_POST['submit'])) {
		// TODO: sanitize values brought in
		if (isset($_POST['import-xml'])) {
			$import_xml = sanitize_text_field(trim($_POST['import-xml']));
		}
		if (isset($_POST['parent_page'])) {
			// TODO: returns 0 if it fails?  Better ways to do this and handle errors
			$parent_page = intval(sanitize_text_field($_POST['parent_page']));
		}
		if (isset($_POST['template'])) {
			$template = sanitize_text_field($_POST['template']);
		}

		update_site_option('htim_importer_options', Array('import-xml' => $import_xml, 'parent_page' => $parent_page, 'template' => $template));
		if (isset($_POST['import-xml'])) {

			HTMLImportPlugin::get_instance()->import_html_from_xml_index( $import_xml, $parent_page, $template );
		}
	}
}


?>

<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<h4><?php _e( 'What are you importing today?' ); ?></h4>

	<form method="post" action="">
		<p>
		<label for="parent_page"><?php _e('Parent Page:', 'import-html-pages');?></label>
		<select name="parent_page">
			<?php
			// TODO: pre-select selected from stored options
			echo '<option value="0" '.selected($parent_page == 0, true, false).'>None</option>';
			$search_args = array(
					'sort_order' => 'ASC',
					'sort_column' => 'post_title',
					'hierarchical' => 1,
					'exclude' => '',
					'include' => '',
					'meta_key' => '',
					'meta_value' => '',
					'authors' => '',
					'child_of' => 0,
					'parent' => -1,
					'exclude_tree' => '',
					'number' => '',
					'offset' => 0,
					'post_type' => 'page',
					'post_status' => 'publish'
			);
			$pages = get_pages($search_args);
			if (isset($pages)) {
				foreach ($pages as $page) {
						echo '<option value="'.$page->ID.'" '.selected($parent_page == $page->ID, true, false).'>'.htmlspecialchars($page->post_title).'</option>';
				}
			}
			?>
			</select>
		</p>
		<p>
		<label for="template"><?php _e('Template:', 'import-html-pages');?></label>
			<select name="template">
				<?php
				// TODO: pre-select selected from stored options
				echo '<option value="0" '.selected($template == 0, true, false).'>None</option>';
				$templates = wp_get_theme()->get_page_templates();
				if (isset($templates)) {
					foreach ($templates as $file => $name) {
						echo '<option value="'.$file.'" '.selected(strcmp($file, $template) == 0, true, false).'>'.$name.'</option>';
					}
				}
				?>
				</select>
	</p>
		<p id="xml">
			<label for="import-xml"><?php _e( 'Enter in the absolute file location of the index XML file:', 'import-html-pages' ); ?></label><br>
			<input type="text" id="import-xml" name="import-xml" size="50" value="<?php echo $import_xml;?>" />
		</p>

		<input type="hidden" name="action" value="save" />

		<p class="submit">
			<input type="submit" name="submit" class="button" value="<?php echo esc_attr( __( 'Import', 'import-html-pages' ) ); ?>" />
		</p>
		<?php wp_nonce_field( 'html-import' ); ?>
	</form>


</div>
