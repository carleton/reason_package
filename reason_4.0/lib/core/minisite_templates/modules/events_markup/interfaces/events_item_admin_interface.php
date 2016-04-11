<?php
/**
 * Interface for event item admin panel markup
 * @package reason
 * @subpackage events_markup
 */
/**
 * Interface for event item markup
 */
interface eventsItemAdminMarkup
{
	/**
	 * Modify the page's head items, if desired
	 * @param object $head_items
	 * @return void
	 */
	public function modify_head_items($head_items);
	
	/**
	 * Set the function bundle for the markup to use
	 * @param object $bundle
	 * @return void
	 */
	public function set_bundle($bundle);
	
	/**
	 * Get the item markup
	 * @return string markup
	 */
	public function get_markup($event);
}