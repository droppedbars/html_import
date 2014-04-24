<?php
require_once( dirname( __FILE__ ) . '/../includes/HtmlImportSettings.php' );
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   HTMLImportPlugin
 * @author    Patrick Mauro <patrick@mauro.ca>
 * @license   GPL-2.0+
 * @link      http://patrick.mauro.ca
 * @copyright 2014 Patrick Mauro
 */

/*
 * Processing for form submission
 */
if ( ( isset( $_POST['action'] ) ) && ( 'save' == $_POST['action'] ) ) {
	if (isset($_POST['submit'])) {
		$settingsToProcess = new html_import\admin\HtmlImportSettings();
		$settingsToProcess->loadFromDB(); //loads the defaults in case not all settings are passed in the POST
		$settingsToProcess->loadFromPOST();
		$settingsToProcess->saveToDB();
		// TODO: improve support for combinations:
		/*
		 *  location, xml
		 *  location, flare
		 *  upload, xml
		 *  upload, flare
		 */
		if ((strcmp('location', $settingsToProcess->getImportSource()->getValue()) == 0) && (strcmp('xml', $settingsToProcess->getIndexType()->getValue()) == 0) && (strcmp('index', $settingsToProcess->getFileType()->getValue()) == 0)) {
			HTMLImportPlugin::get_instance()->import_html_from_xml_index( $settingsToProcess );
		} else if ((strcmp('upload', $settingsToProcess->getImportSource()->getValue()) == 0) && (strcmp('flare', $settingsToProcess->getIndexType()->getValue()) == 0) && (strcmp('zip', $settingsToProcess->getFileType()->getValue()) == 0)) {
			HTMLImportPlugin::get_instance()->import_html_from_flare( $_FILES['file-upload'], $settingsToProcess );
		} else {
			echo '<H1>Unsupported combination of location/upload</H1>';
		}
	}
}
$settings = new html_import\admin\HtmlImportSettings();
$settings->loadFromDB();

?>

<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<form enctype="multipart/form-data" method="post" action="">

		<p id="index-type">
			<h3>Select the type of import index</h3>
			<label for="index-type-xml"><input type="radio" name="index-type" id="index-type-xml" value="xml" <?php checked(strcmp('xml', $settings->getIndexType()->getValue()),0,true); ?>/>XML</label><br>
			<label for="index-type-flare"><input type="radio" name="index-type" id="index-type-flare" value="flare" <?php checked(strcmp('flare',$settings->getIndexType()->getValue()),0,true); ?> />MadCap Flare</label><br>
			<!-- <label for="import-type-raw"><input type="radio" name="import-type" id="import-type-raw" value="raw" />No Index</label><br> -->
		</p>
		<p id="file-type">
			<h3>Select the source file type</h3>
			<label for="file-type-index"><input type="radio" name="file-type" id="file-type-index" value="index"  <?php checked(strcmp('index', $settings->getFileType()->getValue()),0,true); ?> />Index File</label><br>
			<label for="file-type-zip"><input type="radio" name="file-type" id="file-type-zip" value="zip" <?php checked(strcmp('zip', $settings->getFileType()->getValue()),0,true); ?> />ZIP Archive (index must be at root)</label><br>
		</p>
		<p id="import-source">
			<h3>Select the source of the import</h3>
			<label for="import-source-location"><input type="radio" name="import-source" id="import-source-location" value="location" onclick="javascript: jQuery('#define-upload').hide('fast'); jQuery('#define-location').show('fast');" <?php checked(strcmp('location', $settings->getImportSource()->getValue()),0,true); ?> />Location (local or remote)</label><br>
			<label for="import-source-upload"><input type="radio" name="import-source" id="import-source-upload" value="upload" onclick="javascript: jQuery('#define-upload').show('fast'); jQuery('#define-location').hide('fast');"<?php checked(strcmp('upload', $settings->getImportSource()->getValue()),0,true); ?> />Upload ZIP</label><br>
		</p>
		<p id="define-location" style="display:<?php echo (strcmp('location', $settings->getImportSource()->getValue()) == 0 ? 'visible' : 'none');?>;">
			<label for="file-location"><?php _e( 'Enter in the absolute file location of the index file:', 'file_location' ); ?></label>
			<input type="text" id="file-location" name="file-location" size="50" value="<?php echo $settings->getFileLocation()->getEscapedAttributeValue();?>"/>
		</p>

		<p id="define-upload"  style="display:<?php echo (strcmp('upload', $settings->getImportSource()->getValue()) == 0 ? 'visible' : 'none');?>;">
			<label for="file-upload"><?php _e( 'Select the file import:', 'file-upload' ); ?></label>
			<input type="file" name="file-upload" id="file-upload" size="35" class="file-upload" />
		</p>

		<p>
			<h3>Select the parent page for the imported files</h3>
			<label for="parent_page"><?php _e('Parent Page:', 'import-html-pages');?></label>
			<select name="parent_page">
				<?php
				echo '<option value="0" '.selected($settings->getParentPage()->getValue() == 0, true, false).'>None</option>';
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
						echo '<option value="'.$page->ID.'" '.selected($settings->getParentPage()->getValue() == $page->ID, true, false).'>'.htmlspecialchars($page->post_title).'</option>';
					}
				}
				?>
			</select>
		</p>
		<p>
			<h3>Select the template to use for the imported files</h3>
			<label for="template"><?php _e('Template:', 'import-html-pages');?></label>
			<select name="template">
				<?php
				echo '<option value="0" '.selected($settings->getTemplate()->getValue() == 0, true, false).'>None</option>';
				$templates = wp_get_theme()->get_page_templates();
				if (isset($templates)) {
					foreach ($templates as $file => $name) {
						echo '<option value="'.$file.'" '.selected(strcmp($file, $settings->getTemplate()->getValue()) == 0, true, false).'>'.$name.'</option>';
					}
				}
				?>
			</select>
		</p>
		<p id="categories">
			<h3>Select the categories for the imported files</h3>
			TODO...
		</p>

		<input type="hidden" name="action" value="save" />

		<p class="submit">
			<input type="submit" name="submit" class="button" value="<?php echo esc_attr( __( 'Import', 'import-html-pages' ) ); ?>" />
		</p>
		<?php wp_nonce_field( 'html-import' ); ?>
	</form>


</div>
