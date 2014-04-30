<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-19
 * Time: 10:13 PM
 */

namespace html_import;


class GridDeveloperHeaderFooterImportStage extends ImportStage{
	protected function isValid(HTMLImportStages $stagesSettings) {
		return $stagesSettings->doesAddGDNHeaderAndFooter();
	}

	protected function performStage(HTMLImportStages $stagesSettings, WPMetaConfigs &$meta, $body) {
		$meta->setPostContent($this->getGridDirectorHeader($meta->getPostTitle()).$body.$this->getGridDirectorFooter());
	}

	private function getGridDirectorHeader($title) {
		$title = 'Grid Director Developer Network : '.$title;
		$subTitle = '';
		$header = '[vc_row][vc_column width="1/1"][mk_fancy_title tag_name="h2" style="true" color="#4a266d" size="24" font_weight="bolder" margin_top="0" margin_bottom="18" font_family="Ubuntu" font_type="google" align="left"]'.$title.'[/mk_fancy_title][mk_fancy_title tag_name="h2" style="false" color="#393836" size="24" font_weight="300" margin_top="0" margin_bottom="18" font_family="Ubuntu" font_type="google" align="left"]'.$subTitle.'[/mk_fancy_title][mk_padding_divider size="10" width="1/1" el_position="first last"][vc_column_text disable_pattern="false" align="left" margin_bottom="0" p_margin_bottom="20" width="1/1" el_position="first last"]';
		return $header;
	}

	private function getGridDirectorFooter() {
		$footer = '[/vc_column_text][mk_padding_divider size="20" width="1/1" el_position="first last"][/vc_column][/vc_row]';
		return $footer;
	}

} 