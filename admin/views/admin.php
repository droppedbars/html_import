<?php
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

// TODO: Parse out the options into an options class to handle sanitizing, importing, saving, etc.

/*
 * Pre-load default values saved in DB
 */
$plugin_options_arr = get_site_option('htim_importer_options');
// TODO: Sanitize before setting so they don't muck up the HTML
if (isset($plugin_options_arr['file-location'])) {
	$file_location = $plugin_options_arr['file-location'];
} else {
	$file_location = '';
}

$file_upload = '';

if (isset($plugin_options_arr['index-type'])) {
	//TODO:	ensure value is what its supposed to be
	$index_type = $plugin_options_arr['index-type'];
} else {
	$index_type = 'flare';
}
if (isset($plugin_options_arr['file-type'])) {
	//TODO:	ensure value is what it's supposed to be
	$file_type = $plugin_options_arr['file-type'];
} else {
	$file_type = 'zip';
}
if (isset($plugin_options_arr['import-source'])) {
	//TODO:	ensure value is what it's supposed to be
  $import_source = $plugin_options_arr['import-source'];
} else {
	$import_source = 'upload';
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


/*
 * Processing for form submission
 */
if ( ( isset( $_POST['action'] ) ) && ( 'save' == $_POST['action'] ) ) {
	if (isset($_POST['submit'])) {

		if (isset($_POST['index-type'])) {
			if (strcmp($_POST['index-type'], 'xml') == 0) {
				$index_type = 'xml';
			} else {
				$index_type = 'flare';
			}
		}
		if (isset($_POST['file-type'])) {
			if (strcmp($_POST['file-type'], 'index') == 0) {
				$file_type = 'index';
			} else {
				$file_type = 'zip';
			}
		}
		if (isset($_POST['import-source'])) {
			if (strcmp($_POST['import-source'], 'location') == 0) {
				$import_source = 'location';
			} else {
				$import_source = 'upload';
			}
		}

		if (isset($_POST['file-location'])) {
			$file_location = sanitize_text_field(trim($_POST['file-location']));
		}
		if (isset($_POST['parent_page'])) {
			// TODO: returns 0 if it fails?  Better ways to do this and handle errors
			$parent_page = intval(sanitize_text_field($_POST['parent_page']));
		}
		if (isset($_POST['template'])) {
			$template = sanitize_text_field($_POST['template']);
		}

		update_site_option('htim_importer_options', Array('index-type' => $index_type, 'file-type' => $file_type, 'import-source' => $import_source, 'file-location' => $file_location, 'parent_page' => $parent_page, 'template' => $template));

		// TODO: improve support for combinations:
		/*
		 *  location, xml
		 *  location, flare
		 *  upload, xml
		 *  upload, flare
		 */
		if ((strcmp('location', $import_source) == 0) && (strcmp('xml', $index_type) == 0) && (strcmp('index', $file_type) == 0)) {
			HTMLImportPlugin::get_instance()->import_html_from_xml_index( $file_location, $parent_page, $template );
		} else if ((strcmp('upload', $import_source) == 0) && (strcmp('flare', $index_type) == 0) && (strcmp('zip', $file_type) == 0)) {
			HTMLImportPlugin::get_instance()->import_html_from_flare( $_FILES['file-upload'], $parent_page, $template );
		} else {
			echo '<H1>Unsupported combination of location/upload</H1>';
		}
	}
}


?>

<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<form enctype="multipart/form-data" method="post" action="">

		<p id="index-type">
			<h3>Select the type of import index</h3>
			<label for="index-type-xml"><input type="radio" name="index-type" id="index-type-xml" value="xml" <?php checked(strcmp('xml', $index_type),0,true); ?>/>XML</label><br>
			<label for="index-type-flare"><input type="radio" name="index-type" id="index-type-flare" value="flare" <?php checked(strcmp('flare', $index_type),0,true); ?> />MadCap Flare</label><br>
			<!-- <label for="import-type-raw"><input type="radio" name="import-type" id="import-type-raw" value="raw" />No Index</label><br> -->
		</p>
		<p id="file-type">
			<h3>Select the source file type</h3>
			<label for="file-type-index"><input type="radio" name="file-type" id="file-type-index" value="index"  <?php checked(strcmp('index', $file_type),0,true); ?> />Index File</label><br>
			<label for="file-type-zip"><input type="radio" name="file-type" id="file-type-zip" value="zip" <?php checked(strcmp('zip', $file_type),0,true); ?> />ZIP Archive (index must be at root)</label><br>
		</p>
		<p id="import-source">
			<h3>Select the source of the import</h3>
			<label for="import-source-location"><input type="radio" name="import-source" id="import-source-location" value="location" onclick="javascript: jQuery('#define-upload').hide('fast'); jQuery('#define-location').show('fast');" <?php checked(strcmp('location', $import_source),0,true); ?> />Location (local or remote)</label><br>
			<label for="import-source-upload"><input type="radio" name="import-source" id="import-source-upload" value="upload" onclick="javascript: jQuery('#define-upload').show('fast'); jQuery('#define-location').hide('fast');"<?php checked(strcmp('upload', $import_source),0,true); ?> />Upload ZIP</label><br>
		</p>
		<p id="define-location" style="display:<?php echo (strcmp('location', $import_source) == 0 ? 'visible' : 'none');?>;">
			<label for="file-location"><?php _e( 'Enter in the absolute file location of the index file:', 'file_location' ); ?></label>
			<input type="text" id="local-file" name="file-location" size="50" value="<?php echo $file_location;?>"/>
		</p>

		<p id="define-upload"  style="display:<?php echo (strcmp('upload', $import_source) == 0 ? 'visible' : 'none');?>;">
			<label for="file-upload"><?php _e( 'Select the file import:', 'file-upload' ); ?></label>
			<input type="file" name="file-upload" id="file-upload" size="35" class="file-upload" />
		</p>

		<p>
			<h3>Select the parent page for the imported files</h3>
			<label for="parent_page"><?php _e('Parent Page:', 'import-html-pages');?></label>
			<select name="parent_page">
				<?php
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
			<h3>Select the template to use for the imported files</h3>
			<label for="template"><?php _e('Template:', 'import-html-pages');?></label>
			<select name="template">
				<?php
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
