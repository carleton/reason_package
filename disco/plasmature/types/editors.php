<?php

/**
 * HTML editor type library.
 *
 * @package disco
 * @subpackage plasmature
 */

require_once PLASMATURE_TYPES_INC."default.php";
require_once PLASMATURE_TYPES_INC."text.php";

/**
 * Edit HTML using a Loki 2 editor.
 * @package disco
 * @subpackage plasmature
 */
class loki2Type extends defaultType
{
	var $type = 'loki2';
	var $widgets = 'default';
	var $site_id = 0;
	var $paths = array();
	var $allowable_tags = array();
	
	/**
	 * Exists for backwards compatibility with Loki 1
	 *
	 * Proper method to use now is to just pass the source option as a a widget, or not
	 * @deprecated
	 */
	var $user_is_admin;
	var $crash_report_uri;
	/**
	 * Allow for a custom sized text entry box
	 */
	var $rows = 20;
	var $cols = 80;
	var $type_valid_args = array('widgets', 'site_id', 'paths', 'allowable_tags', 'user_is_admin', 'crash_report_uri', 'rows', 'cols');
	function do_includes()
	{
		if (file_exists( LOKI_2_INC.'loki.php' ))
		{
			include_once( LOKI_2_INC.'loki.php' );
		}
		else
		{
			trigger_error('Loki 2 file structure has changed slightly. Please update LOKI_2_INC in package_settings.php to reference the ' . LOKI_2_INC . '/helpers/php/ directory.');
			include_once( LOKI_2_INC.'/helpers/php/inc/options.php' );
		}
	}
	function grab()
	{
		$http_vars = $this->get_request();
		if ( isset( $http_vars[ $this->name ] ) )
		{
			$val = tidy( $http_vars[ $this->name ] );
			if( empty( $val ) )
			{
				$tidy_err = tidy_err( $http_vars[ $this->name ] );
				if( !empty($tidy_err) )
				{
					$tidy_err = nl2br( htmlentities( $tidy_err,ENT_QUOTES,'UTF-8' ) );
					$this->set_error( 'Your HTML appears to be ill-formatted.  Here is what Tidy has to say about it: <br />'.$tidy_err );
					$this->set( $http_vars[ $this->name ] );
				}
				else
					$this->set( $val );
			}
			else
			{
				// this looks like a hack. We could look into removing it.
				// $val = eregi_replace("</table>\n\n<br />\n<br />\n","</table>\n", $val);
				$this->set( $val );
			}
		}
		$length = strlen( $this->value );
		if( ($this->db_type == 'tinytext' AND $length > 255) OR ($this->db_type == 'text' AND $length > 65535) OR ($this->db_type == 'mediumtext' AND $length > 16777215) )
			$this->set_error( 'There is more text in '.$this->display_name.' than can be stored ' );
	}
	function display()
	{
		$loki = new Loki2( $this->name, $this->value, $this->_resolve_widgets($this->widgets) );
		if(!empty($this->paths['image_feed']))
		{
			$loki->set_feed('images',$this->paths['image_feed']);
		}
		if(!empty($this->paths['site_feed']))
		{
			$loki->set_feed('sites',$this->paths['site_feed']);
		}
		if(!empty($this->paths['finder_feed']))
		{
			$loki->set_feed('finder',$this->paths['finder_feed']);
		}
		if(!empty($this->paths['default_site_regexp']))
		{
			$loki->set_default_site_regexp($this->paths['default_site_regexp']);
		}
		if(!empty($this->paths['default_type_regexp']))
		{
			$loki->set_default_type_regexp($this->paths['default_type_regexp']);
		}
		if(!empty($this->paths['css']))
		{
			$loki->add_document_style_sheets($this->paths['css']);
		}
		if(!empty($this->allowable_tags))
		{
			$loki->set_allowable_tags($this->allowable_tags);
		}
		if(!empty($this->crash_report_uri))
		{
			$loki->set_crash_report_uri($this->crash_report_uri);
		}
		$loki->print_form_children($this->rows, $this->cols);
	}
	function _resolve_widgets($widgets)
	{
		$widgets = $this->_flatten_widgets($widgets);
		if($this->user_is_admin)
		{
			$widgets .= ' +source +debug';
		}
		elseif($this->user_is_admin === false)
		{
			$widgets .= ' -source -debug';
		}
		return $widgets;
	}
	function _flatten_widgets($widgets)
	{
		if(is_array($widgets))
			return implode(' ',$widgets);
		else
			return $widgets;
	}
}

/**
 * Edit HTML using the TinyMCE editor advanced theme.
 *
 * The plasmature type allows you to optionally pass in an array of buttons to use.
 *
 * We the advanced themes.
 *
 * @todo all you to specify external config. css?
 * @todo use head items??
 *
 * @package disco
 * @subpackage plasmature
 */
class tiny_mceType extends textareaType
{
	var $type = 'tiny_mce';
	var $type_valid_args = array('buttons', 'buttons2', 'buttons3', 'reason_page_id', 'reason_site_id', 'status_bar_location', 'formatselect_options', 'content_css', 'init_options');
	var $status_bar_location = 'none';
	var $buttons = array('formatselect','bold','italic','hr','blockquote','numlist','bullist','indent','outdent','reasonimage','link','unlink','anchor','media','forecolor');
	var $buttons2 = array();
	var $buttons3 = array();
	var $content_css;
  // TODO: This needs to affect the formats option on init(), no longer
  // works as a theme option.
	var $formatselect_options = array('p','h3','h4','pre');
	var $init_options = array();
	var $base_init_options = array(
		'mode' => 'exact',
		'plugins' => 'image,anchor,link,paste,reasonimage,media,textcolor',
		'dialog_type' => 'modal',
		'theme' => 'modern',
		'convert_urls' => false,
	);
	
	function display()
	{
		$display = $this->get_tiny_mce_javascript();
		$display .= '<script language="javascript" type="text/javascript">'."\n";
		$display .= $this->get_tiny_mce_init_string();
		$display .= '</script>'."\n";
		
		// Why do we do this?
		//$this->set_class_var('rows', $this->get_class_var('rows')+5 );
		echo $display;
		parent::display();
	}
	
	function get_tiny_mce_init_string()
	{
		// Add calculated/passed options to base options
		$options = $this->base_init_options;
		$options['toolbar1'] = implode(" ",$this->buttons);
		$options['toolbar2'] = implode(" ",$this->buttons2);
		$options['toolbar3'] = implode(" ",$this->buttons3);
		// make me conditional on formatselect being in the buttons array and formatselect_options being set.
		//$options['formats'] = "{'p', 'pre', 'h1'}"; //implode(",",$this->formatselect_options);
    $options['elements'] = $this->name;
    if (isset($this->reason_page_id))
      $options['reason_page_id'] = $this->reason_page_id;
    if (isset($this->reason_site_id))
      $options['reason_site_id'] = $this->reason_site_id;

    $options['reason_http_base_path'] = REASON_HTTP_BASE_PATH;

		if ($this->get_class_var('content_css')) $options['content_css'] = $this->get_class_var('content_css');
		
		// Merge in custom options
		foreach($this->init_options as $option => $val) $options[$option] = $val;
		
		// Format the options
		foreach ($options as $option => $val) $parts[] = sprintf('%s : "%s"', $option, $val);
			
		return 'tinymce.init({'."\n" . implode(",\n", $parts) . "\n});\n";
	}
	/**
	 * We return the main javascript for TinyMCE - we use a static variable to keep track such that we include it only once.
	 */
	function get_tiny_mce_javascript()
	{
		// we only want to load the main js file once.
		static $loaded_an_instance;
		if (!isset($loaded_an_instance))
		{
			$js = '<script language="javascript" type="text/javascript" src="'.TINYMCE_HTTP_PATH.'tinymce.js"></script>'."\n";
			$loaded_an_instance = true;
		}
		return (!empty($js)) ? $js : '';
	}
}

/**
 * Edit HTML using an unlabeled TinyMCE editor.
 * @package disco
 * @subpackage plasmature
 */
class tiny_mce_no_labelType extends tiny_mceType // {{{
{
	var $_labeled = false;
}
