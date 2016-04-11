<?php
/**
 * Events markup class -- the default item markup
 * @package reason
 * @subpackage events_markup
 */
 /**
  * Include dependencies & register the class
  */
reason_include_once('classes/media/factory.php');

reason_include_once('minisite_templates/modules/events_markup/interfaces/events_item_interface.php');
$GLOBALS['events_markup']['minisite_templates/modules/events_markup/default/events_item.php'] = 'defaultEventsItemMarkup';
/**
 * Markup class for showing the single item
 */
class defaultEventsItemMarkup implements eventsItemMarkup
{
	/**
	 * The function bundle
	 * @var object
	 */
	protected $bundle;
	/**
	 * Modify the page's head items, if desired
	 * @param object $head_items
	 * @return void
	 */
	public function modify_head_items($head_items, $event = null)
	{
		$media = $this->bundle->media_works($event);
		if(!empty($media))
		{
			if(count($media) > 1)
			{
				$head_items->add_javascript(JQUERY_URL, true);
				$head_items->add_javascript(REASON_HTTP_BASE_PATH.'modules/events/media_gallery.js');
			}
			$head_items->add_stylesheet(REASON_HTTP_BASE_PATH.'modules/events/media_gallery.css');	
		}
	}	
	/**
	 * Set the function bundle for the markup to use
	 * @param object $bundle
	 * @return void
	 */
	public function set_bundle($bundle)
	{
		$this->bundle = $bundle;
	}
	/**
	 * Get the markup for a given event
	 * @param object $event
	 * @return string markup
	 */
	public function get_markup($event)
	{
		if(empty($this->bundle))
		{
			trigger_error('Call set_bundle() before calling get_markup()');
			return '';
		}
		$ret = '<p class="back"><a href="'.$this->bundle->back_link().'">Back to event listing</a></p>';
		$ret .= $this->get_image_markup($event);
		$ret .= '<h3>'.$event->get_value('name').'</h3>'."\n";
		$ret .= $this->get_ownership_markup($event);
		if ($event->get_value('description'))
			$ret .= '<p class="description">'.$event->get_value( 'description' ).'</p>'."\n";
		$ret .= $this->get_repetition_info_markup($event);
		if ($this->bundle->request_date() && strstr($event->get_value('dates'), $this->bundle->request_date()))
			$ret .= '<p class="date"><strong>Date:</strong> '.prettify_mysql_datetime( $this->bundle->request_date(), "l, F jS, Y" ).'</p>'."\n";
		if(!$this->bundle->is_all_day_event($event))
			$ret .= '<p class="time"><strong>Time:</strong> '.prettify_mysql_datetime( $event->get_value( 'datetime' ), "g:i a" ).'</p>'."\n";
		if ($event->get_value( 'hours' ) || $event->get_value( 'minutes' ))
		{
			$ret .= '<p class="duration"><strong>Duration:</strong> ';
			$ret .= $this->bundle->prettify_duration($event);
			$ret .= '</p>'."\n";
		}
		$ret .= $this->get_location_markup($event);
		if ($event->get_value('sponsor'))
			$ret .= '<p class="sponsor"><strong>Sponsored by:</strong> '.$event->get_value('sponsor').'</p>'."\n";
		$ret .= $this->get_contact_info_markup($event);
		$ret .= $this->get_item_export_link_markup($event);
		$ret .= $this->get_media_work_markup($event);
		if ($event->get_value('content'))
			$ret .= '<div class="eventContent">'.$event->get_value( 'content' ).'</div>'."\n";
		$ret .= $this->get_dates_markup($event);
		if ($event->get_value('url'))
			$ret .= '<div class="eventUrl"><strong>For more information, visit:</strong> <a href="'.reason_htmlspecialchars($event->get_value( 'url' )).'">'.$event->get_value( 'url' ).'</a>.</div>'."\n";
		$ret .= $this->get_categories_markup($event);
		$ret .= $this->get_audiences_markup($event);
		$ret .= $this->get_keywords_markup($event);
		$ret .= $this->bundle->registration_markup($event);
		$ret .= $this->get_admin_markup($event);
		return $ret;
	}
	/**
	 * Get the images markup for a given event
	 * @param object $event
	 * @return string markup
	 */
	protected function get_image_markup($event)
	{
		$ret = '';
		if ($images = $this->bundle->images($event))
		{
		    $ret .= '<div class="images">';
		    foreach( $images AS $image )
		    {
				 $ret .= '<div class="tnBlock">';
				 $ret .= get_show_image_html( $image, false, true, true, '' );
				 $ret .=  "</div>";
		    }
		    $ret .=  "</div>";
		}
		return $ret;
	}
	protected function get_media_work_markup($event)
	{
		$ret = '';
		$media_works = $this->bundle->media_works($event);
		if(!empty($media_works))
		{
			$ret .= '<div class="mediaWorks">';
			$ret .= $this->get_media_section($media_works);
			$ret .= "</div>";
		}
		return $ret;
	}
	/**
	 * Get the markup for the media section
	 * @todo support classic media somehow
	 */
	function get_media_section($media_works)
	{
		$class = count($media_works) > 1 ? 'mediaGallery' : 'basicMedia';
		$str = '<ul class="'.$class.'">';
		foreach($media_works as $media)
		{
			$str .= '<li>';
			$str .= '<div class="titleBlock">';
			if($placard_info = $this->get_media_placard_info($media))
				$str .= '<img src="'.$placard_info['url'].'" alt="Placeholder image for '.reason_htmlspecialchars($media->get_value('name')).'" class="placard" width="'.$placard_info['width'].'" height="'.$placard_info['height'].'" style="display:none;" />';
			$str .= '<div class="mediaName">'.$media->get_value('name').'</div>';
			$str .= '</div>';
			//$str .= $media->get_value('integration_library').'<br />';
			$displayer_chrome = MediaWorkFactory::displayer_chrome($media, 'default');
			if ($displayer_chrome)
			{
				$str .= '<div class="mediaDisplay">';
				$displayer_chrome->set_media_work($media);
				
				if($height = $this->get_media_display_height());
					$displayer_chrome->set_media_height($height);
				
				if($width = $this->get_media_display_width());
					$displayer_chrome->set_media_width($width);
				
				//$str .= get_class($displayer_chrome);
	
				$str .= $displayer_chrome->get_html_markup();
				$str .= '</div>';
			}
			$str .= '</li>';
		}
		$str .= '</ul>';
		return $str;
	}
	protected function get_media_display_height()
	{
		return NULL;
	}
	protected function get_media_display_width()
	{
		return 480;
	}
	protected function get_media_placard_info($media)
	{
		if($placards = $media->get_left_relationship('av_to_primary_image'))
		{
			$placard = current($placards);
			$placard_url = reason_get_image_url($placard, 'tn');
			list($width, $height) = getimagesize(reason_get_image_path($placard, 'tn'));
		}	
		else
		{
			$placard_url =  REASON_HTTP_BASE_PATH.'modules/publications/media_placeholder_thumbnail.png';
			$width = 125;
			$height = 70;
		}
		return array(
			'url' => $placard_url,
			'width' => $width,
			'height' => $height,
		);
	}
	/**
	 * Get the ownership information markup for a given event
	 * @param object $event
	 * @return string markup
	 */
	protected function get_ownership_markup($event)
	{
		$ret = '';
		if($owner_site = $this->bundle->owner_site($event))
		{
			if($owner_site->id() != $this->bundle->current_site_id())
			{
				$sitelink = $owner_site->get_value('name');
				if($owner_site->get_value('_link'))
					$sitelink = '<a href="'.reason_htmlspecialchars($owner_site->get_value('_link')).'">'.$sitelink.'</a>';
				$ret .= '<p class="ownerInfo">From site: '.$sitelink.'</p>'."\n";
			}
		}
		return $ret;
	}
	/**
	 * Get the repetition information markup for a given event
	 * @param object $event
	 * @return string markup
	 */
	protected function get_repetition_info_markup($event)
	{
		$ret = '';
		$rpt = $event->get_value('recurrence');
		$freq = '';
		$words = array();
		$dates_text = '';
		$occurence_days = array();
		if (!($rpt == 'none' || empty($rpt)))
		{
			$words = array('daily'=>array('singular'=>'day','plural'=>'days'),
							'weekly'=>array('singular'=>'week','plural'=>'weeks'),
							'monthly'=>array('singular'=>'month','plural'=>'months'),
							'yearly'=>array('singular'=>'year','plural'=>'years'),
					);
			if ($event->get_value('frequency') <= 1)
				$sp = 'singular';
			else
			{
				$sp = 'plural';
				$freq = $event->get_value('frequency').' ';
			}
			if ($rpt == 'weekly')
			{
				$days_of_week = array('sunday','monday','tuesday','wednesday','thursday','friday','saturday');
				foreach($days_of_week as $day)
				{
					if($event->get_value($day))
						$occurence_days[] = $day;
				}
				$last_day = array_pop($occurence_days);
				$dates_text = ' on ';
				if (!empty( $occurence_days ) )
				{
					$comma = '';
					if(count($occurence_days) > 2)
						$comma = ',';
					$dates_text .= ucwords(implode(', ', $occurence_days)).$comma.' and ';
				}
				$dates_text .= prettify_string($last_day);
			}
			elseif ($rpt == 'monthly')
			{
				$suffix = array(1=>'st',2=>'nd',3=>'rd',4=>'th',5=>'th');
				if ($event->get_value('week_of_month'))
				{
					$dates_text = ' on the '.$event->get_value('week_of_month');
					$dates_text .= $suffix[$event->get_value('week_of_month')];
					$dates_text .= ' '.$event->get_value('month_day_of_week');
				}
				else
					$dates_text = ' on the '.prettify_mysql_datetime($event->get_value('datetime'), 'jS').' day of the month';
			}
			elseif ($rpt == 'yearly')
			{
				$dates_text = ' on '.prettify_mysql_datetime($event->get_value('datetime'), 'F jS');
			}
			$ret .= '<p class="repetition">This event takes place each ';
			$ret .= $freq;
			$ret .= $words[$rpt][$sp];
			$ret .= $dates_text;
			$ret .= ' from '.prettify_mysql_datetime($event->get_value('datetime'), 'F jS, Y').' to '.prettify_mysql_datetime($event->get_value('last_occurence'), 'F jS, Y').'.';
			
			$ret .= '</p>'."\n";
		}
		return $ret;
	}
	/**
	 * Get the location information markup for a given event
	 * @param object $event
	 * @return string markup
	 */
	protected function get_location_markup($event)
	{
		$ret = '';
		$lat = ($event->has_value('latitude')) ? $event->get_value('latitude') : false;
		$lon = ($event->has_value('longitude')) ? $event->get_value('longitude') : false;
		$address = ($event->has_value('address')) ? $event->get_value('address') : false;
		$location = ($event->has_value('location')) ? $event->get_value('location') : false;
		
		if ( (!empty($lat) && !empty($lon)) || !empty($address) )
		{
			$ret .= '<div class="eventLocation">'."\n";
			if (!empty($lat) && !empty($lon))
			{
				$ret .= $this->get_map_markup($event);
			}
			$ret .= '<strong>Location:</strong>';
			if ($location)
			{
				$ret .= '<p class="location">'.$event->get_value('location').'</p>'."\n";
			}	
			if ($address)
			{
				$ret .= '<p class="address">'.$event->get_value('address').'</p>'."\n";
			}
			$ret .= '</div>'."\n";
		}
		elseif (!empty($location))
		{
			$ret .= '<p class="location"><strong>Location:</strong> '.$event->get_value('location').'</p>'."\n"; // this is what we used to do.
		}
		return $ret;
	}
	/**
	 * Get a map for a given event
	 * @param object $event
	 * @return string markup
	 */
	protected function get_map_markup($event)
	{
		$ret = '';
		$lat = ($event->has_value('latitude')) ? $event->get_value('latitude') : false;
		$lon = ($event->has_value('longitude')) ? $event->get_value('longitude') : false;
		$address = ($event->has_value('address')) ? $event->get_value('address') : false;
		
		if (!empty($lat) && !empty($lon)) // if we have a location, lets show it with a google static map.
		{
			$ret .= '<div class="eventMap">';
			$static_map_base_url = 'https://maps.googleapis.com/maps/api/staticmap';
			$params['size'] = '100x100';
			$params['markers'] = 'color:0xFF6357|'.$lat.','.$lon;
			$params['sensor'] = 'false';
			
			// lets add zoom level if it is set
			if ($zoom = $this->bundle->map_zoom_level($event)) 
			{
				$params['zoom'] = $zoom;
			}
			$qs = carl_make_query_string($params);
			$static_map_url = $static_map_base_url . $qs;
			
			$google_maps_base_url = 'https://maps.google.com/maps/';
			if ($address) $google_maps_params['saddr'] = $event->get_value('address');
			else $google_maps_params['q'] = $lat.','.$lon;
			$google_maps_qs = carl_construct_query_string($google_maps_params);
			$google_maps_link = $google_maps_base_url . $google_maps_qs;
			$ret .= '<a href="'.$google_maps_link.'"><img src="'.$static_map_url.'" alt="map of '.reason_htmlspecialchars($event->get_value('name')).'" /></a>';	
			$ret .= '</div>';
		}
		return $ret;
	}
	/**
	 * Get HTML for contact information for a given event
	 * @param object $event event
	 * @return string
	 */
	protected function get_contact_info_markup($event)
	{
		$ret = '';
		$contact_info = $this->bundle->contact_info($event);
		if(!empty($contact_info) )
		{
			$ret .= '<p class="contact"><strong>Contact:</strong> ';
			if(!empty($contact_info['email']))
				$ret .= '<a href="mailto:'.htmlspecialchars($contact_info['email']).'">';
			if(!empty($contact_info['fullname']))
				$ret .= htmlspecialchars($contact_info['fullname']);
			else
				$ret .= htmlspecialchars($contact_info['username']);
			if(!empty($contact_info['email']))
				$ret .= '</a>';
			if(!empty($contact_info['organization']))
				$ret .= ', '.$contact_info['organization'];
			if(!empty($contact_info['phone']))
				$ret .= ', '.htmlspecialchars($contact_info['phone']);
			$ret .= '</p>'."\n";
		}
		return $ret;
	}
	/**
	 * Get HTML link to export an ical representation of this event
	 * @param object $event event
	 * @return string
	 */
	protected function get_item_export_link_markup($event) {
		$ret = '';
		if($ical_link = $this->bundle->ical_link($event))
		{
			$ret .= '<div class="export">'."\n";
			if($event->get_value('recurrence') == 'none' || !$this->bundle->request_date() )
			{
				$ret .= '<a href="'.$ical_link.'">Import into your calendar program</a>';
			}
			else
			{
				$ret .= 'Add to your calendar: ';
				if($item_ical_link = $this->bundle->ical_link($event, false))
				{
					$ret .= '<a href="'.$item_ical_link.'">This occurrence</a> | ';
				}
				$ret .= '<a href="'.$ical_link.'">All occurrences</a>';
			}
			$ret .= '</div>'."\n";
		}
		return $ret;
	}
	/**
	 * Get HTML that shows the dates a given event occurs on
	 * @param object $event event
	 * @return string
	 */
	protected function get_dates_markup($event)
	{
		$ret = '';
		$dates = explode(', ', $event->get_value('dates'));
		if(count($dates) > 1 || !$this->bundle->request_date() || !strstr($event->get_value('dates'), $this->bundle->request_date()))
		{
			$ret .= '<div class="dates"><h4>This event occurs on:</h4>'."\n";
			$ret .= '<ul>'."\n";
			foreach($dates as $date)
			{
				$ret .= '<li>'.prettify_mysql_datetime( $date, "l, F jS, Y" ).'</li>'."\n";
			}
			$ret .= '</ul>'."\n";
			$ret .= '</div>'."\n";
		}
		return $ret;
	}
	
	/**
	 * Get HTML that lists the categories for a given event
	 * @param object $event event
	 * @return string
	 */
	protected function get_categories_markup($event)
	{
		$ret = '';
		if ($categories = $this->bundle->categories($event))
        {
            $ret .= '<div class="categories">';
            $ret .= '<h4>Categories:</h4>'."\n";
			$ret .= '<p>'."\n";
			$links = array();
            foreach( $categories AS $cat )
            {
				$links[] = '<a href="'.$cat->get_value('_link').'">'.$cat->get_value('name').'</a>';
            }
			$ret .= implode(', ',$links);
			$ret .= '</p>'."\n";
            $ret .= "</div>";
        }
        return $ret;
	}
	
	/**
	 * Get HTML that lists the audiences for a given event
	 * @param object $e event
	 * @return string
	 */
	protected function get_audiences_markup($event)
	{
		$ret = '';
		if ($audiences = $this->bundle->audiences($event))
        {
            $ret .=  '<div class="audiences">';
            $ret .=  '<h4>Audiences:</h4>'."\n";
			$ret .=  '<p>'."\n";
			$links = array();
            foreach( $audiences AS $aud )
            {
                $links[] = '<a href="'.$aud->get_value('_link').'">'.$aud->get_value('name').'</a>';
            }
			$ret .=  implode(', ',$links);
			$ret .=  '</p>'."\n";
            $ret .=  "</div>";
        }
        return $ret;
	}
	
	/**
	 * Get HTML that lists the keywords for a given event
	 * @param object $e event
	 * @return string
	 */
	function get_keywords_markup($event)
	{
		$ret = '';
		if($keywords = $this->bundle->keyword_links($event))
		{
			$ret .= '<div class="keywords">';
			$ret .= '<h4>Keywords:</h4>'."\n";
			$ret .= '<p>';
			$parts = array();
			foreach($keywords as $keyword => $link)
			{
				$parts[] = '<a href="'.$link.'">'.$keyword.'</a>';
			}
			$ret .= implode(', ',$parts);
			$ret .= '</p>';
			$ret .= '</div>'."\n";
		}
		return $ret;
	}
	
	/**
	 * Get the HTML markup for Reason admins
	 * @param object $e event
	 * @return string
	 */
	function get_admin_markup($event)
	{
		$ret = '';
		if($markup = $this->bundle->admin_markup($event))
		{
			$ret .= '<div class="reasonMaintainerPanel">';
			$ret .= '<h4>For Reason Site Maintainers:</h4>';
			$ret .= $markup;
			$ret .= '</div>'."\n";
		}
		return $ret;
	}
}
