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
	if ( isset( $_POST['submit'] ) ) {
		$settingsToProcess = new html_import\admin\HtmlImportSettings();
		$settingsToProcess->loadFromDB(); //loads the defaults in case not all settings are passed in the POST

		$settingsToProcess->loadFromPOST();
		$settingsToProcess->saveToDB();
		?>

		<script src="//code.jquery.com/jquery-1.10.2.js"></script>
		<script>
			$(document).ready(function () {
				$('#noLogMessages').click(function () {
					$('#noLogMessages').hide('fast');
					$('#logMessages').show('fast');
				});
				$('#logMessages').click(function () {
					$('#noLogMessages').show('fast');
					$('#logMessages').hide('fast');
				});
			});
		</script>

		<?php

		echo '<div id="noLogMessages" style="display:none;">';
		echo "<b>show log messages</b>";
		echo '</div>';
		echo '<div id="logMessages" style="display:inline;">';

		echo "<b>hide log messages</b>";

		HTMLImportPlugin::get_instance()->importHTMLFiles( $settingsToProcess );


		echo '</div>';
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
		<label for="index-type-xml"><input type="radio" name="index-type" id="index-type-xml" value="xml" <?php checked( strcmp( 'xml', $settings->getIndexType()->getValue() ), 0, true ); ?>/>Confluence XML</label><br>
		<a href="#" target="_self" id="showXML" onclick="javascript: jQuery('#showXML').hide('fast');  jQuery('#hideXML').show('fast'); jQuery('#SampleXML').show('fast');">Show XML Example</a>
		<a href="#" target="_self" id="hideXML" style="display:none;" onclick="javascript: jQuery('#showXML').show('fast');  jQuery('#hideXML').hide('fast'); jQuery('#SampleXML').hide('fast');">Hide XML Example</a>

		<div id="SampleXML" style="display:none;">
				<pre>&lt;knowledgebase version="1.0"&gt;
  &lt;document title="First File" src="first.html" category="category1" order="1"&gt;
  &lt;/document&gt;
  &lt;document title="Second File" src="second.html" category="category2" order="0"&gt;
    &lt;document title="Nested File" src="third.html" category="category2,category3" order="3"&gt;
    &lt;/document&gt;
  &lt;/document&gt;
&lt;/knowledgebase&gt;</pre>
		</div>
		<br>
		<label for="index-type-flare"><input type="radio" name="index-type" id="index-type-flare" value="flare" <?php checked( strcmp( 'flare', $settings->getIndexType()->getValue() ), 0, true ); ?> />MadCap Flare</label><br>
		</p>
		<div style="display:none;">
			<p id="file-type">

			<h3>Select the source file type</h3>
			<label for="file-type-index"><input type="radio" name="file-type" id="file-type-index" value="index"  <?php checked( strcmp( 'index', $settings->getFileType()->getValue() ), 0, true ); ?> />Index File</label><br>
			<label for="file-type-zip"><input type="radio" name="file-type" id="file-type-zip" value="zip" <?php checked( strcmp( 'zip', $settings->getFileType()->getValue() ), 0, true ); ?> />ZIP Archive</label><br>
			</p>
		</div>
		<div>
			<p id="import-source">

			<h3>Select the source of the import</h3>
			<label for="import-source-location"><input type="radio" name="import-source" id="import-source-location" value="location" onclick="javascript: jQuery('#define-upload').hide('fast'); jQuery('#define-location').show('fast');" <?php checked( strcmp( 'location', $settings->getImportSource()->getValue() ), 0, true ); ?> />Location (local, remote, or URL)</label><br>
			<label for="import-source-upload"><input type="radio" name="import-source" id="import-source-upload" value="upload" onclick="javascript: jQuery('#define-upload').show('fast'); jQuery('#define-location').hide('fast');"<?php checked( strcmp( 'upload', $settings->getImportSource()->getValue() ), 0, true ); ?> />Upload ZIP</label><br>
			</p>
		</div>
		<p id="define-location" style="display:<?php echo( strcmp( 'location', $settings->getImportSource()->getValue() ) == 0 ? 'visible' : 'none' ); ?>;">
			<label for="file-location"><?php _e( 'Enter in the absolute file location of the index file:', 'file_location' ); ?></label>
			<input type="text" id="file-location" name="file-location" size="50" value="<?php echo $settings->getFileLocation()->getEscapedAttributeValue(); ?>" />
		</p>

		<p id="define-upload" style="display:<?php echo( strcmp( 'upload', $settings->getImportSource()->getValue() ) == 0 ? 'visible' : 'none' ); ?>;">
			<label for="file-upload"><?php _e( 'Select the file import:', 'file-upload' ); ?></label>
			<input type="file" name="file-upload" id="file-upload" size="35" class="file-upload" />
		</p>

		<p>

		<h3>Select the parent page for the imported files</h3>
		<label for="parent_page"><?php _e( 'Parent Page:', 'import-html-pages' ); ?></label>
		<select name="parent_page">
			<?php
			echo '<option value="0" ' . selected( $settings->getParentPage()->getValue() == 0, true, false ) . '>None</option>';
			$search_args = array(
					'sort_order'   => 'ASC',
					'sort_column'  => 'post_title',
					'hierarchical' => 1,
					'exclude'      => '',
					'include'      => '',
					'meta_key'     => '',
					'meta_value'   => '',
					'authors'      => '',
					'child_of'     => 0,
					'parent'       => - 1,
					'exclude_tree' => '',
					'number'       => '',
					'offset'       => 0,
					'post_type'    => 'page',
					'post_status'  => 'publish'
			);
			$pages       = get_pages( $search_args );
			if ( isset( $pages ) ) {
				foreach ( $pages as $page ) {
					$ancestors = get_ancestors( $page->ID, 'page' );
					$strlen    = strlen( $page->post_title ) + ( 2 * sizeof( $ancestors ) );
					$pageName  = str_pad( $page->post_title, $strlen, " ", STR_PAD_LEFT );
					$pageName  = str_replace( " ", "&nbsp;", $pageName );
					echo '<option value="' . $page->ID . '" ' . selected( $settings->getParentPage()->getValue() == $page->ID, true, false ) . '>' . $pageName . '</option>';
				}
			}
			?>
		</select>
		</p>
		<p>

		<h3>Select the template to use for the imported files</h3>
		<label for="template"><?php _e( 'Template:', 'import-html-pages' ); ?></label>
		<select name="template">
			<?php
			echo '<option value="0" ' . selected( $settings->getTemplate()->getValue() == 0, true, false ) . '>None</option>';
			$templates = wp_get_theme()->get_page_templates();
			if ( isset( $templates ) ) {
				foreach ( $templates as $file => $name ) {
					echo '<option value="' . $file . '" ' . selected( strcmp( $file, $settings->getTemplate()->getValue() ) == 0, true, false ) . '>' . $name . '</option>';
				}
			}
			?>
		</select>
		</p>
		<p id="categories">

		<h3>Select the Categories for the imported files</h3>
		<?php
		if ( !class_exists( "PTCFP" ) ) { // check for the Post Tags and Categories for Pages plugin
			?>
			<h4>*
				<a href="https://wordpress.org/plugins/post-tags-and-categories-for-pages/">Post Tags and Categories for Pages</a> is a required plugin in order to use categories.
			</h4>
		<?php
		}
		?>
		<div id="settings-categories">
			<select id="select-categories" multiple name="category[]" size="8">
				<?php

				/**
				 * Given a category ID return all of the categories, by hierarchy with indenting as OPTIONS for a SELECT.
				 *
				 * @param $category_id
				 * @param $settings
				 */
				function outputChildCategories( $category_id, $settings ) {
					$search_args = array( 'type'         => 'post',
																'child_of'     => 0,
																'parent'       => $category_id,
																'orderby'      => 'name',
																'order'        => 'ASC',
																'hide_empty'   => 0,
																'hierarchical' => 1,
																'exclude'      => '',
																'include'      => '',
																'number'       => '',
																'taxonomy'     => 'category',
																'pad_counts'   => false
					);

					$categories = get_categories( $search_args );

					foreach ( $categories as $category ) {
						$ancestors = get_ancestors( $category->cat_ID, 'category' );
						$strlen    = strlen( $category->name ) + ( 2 * sizeof( $ancestors ) );
						$catName   = str_pad( $category->name, $strlen, " ", STR_PAD_LEFT );
						$catName   = str_replace( " ", "&nbsp;", $catName );

						// TODO: value should be the cat_ID but need to modify back end to support this
						echo '<option value="' . $category->name . '" ' . selected( $settings->getCategories()->testValue( $category->name ), true, false ) . '>' . $catName . '</option>';

						outputChildCategories( $category->cat_ID, $settings );
					}

				}

				outputChildCategories( 0, $settings );

				?>
			</select>
			<br>
		</div>
		</p>

		<input type="hidden" name="action" value="save" />

		<p class="submit">
			<input type="submit" name="submit" class="button-primary" value="<?php echo esc_attr( __( 'Import', 'import-html-pages' ) ); ?>" />
		</p>
		<?php wp_nonce_field( 'html-import' ); ?>
	</form>


</div>
