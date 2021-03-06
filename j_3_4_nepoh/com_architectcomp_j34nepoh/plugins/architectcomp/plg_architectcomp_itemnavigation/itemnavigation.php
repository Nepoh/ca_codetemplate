<?php
/**
 * @tempversion
 * @name			[%%ArchitectComp_name%%] (Release [%%COMPONENTSTARTVERSION%%])
 * @author			[%%COMPONENTAUTHOR%%] ([%%COMPONENTWEBSITE%%])
 * @package			[%%com_architectcomp%%]
 * @subpackage		[%%com_architectcomp%%].itemnavigation
 * @copyright		[%%COMPONENTCOPYRIGHT%%]
 * @license			GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html 
 * 
 * The following Component Architect header section must remain in any distribution of this file
 * 
 * @version			$Id: itemnavigation.php 482 2015-04-06 17:48:54Z BrianWade $
 * @CAauthor		Component Architect (www.componentarchitect.com)
 * @CApackage		architectcomp
 * @CAsubpackage	architectcomp.itemnavigation
 * @CAtemplate		joomla_3_4_nepoh (Release 0.0.1) by Nepoh<nepoh@outlook.de> based on joomla_3_4_standard (Release 1.0.0)
 * @CAcopyright		Copyright (c)2013 - 2015  Simply Open Source Ltd. (trading as Component Architect). All Rights Reserved
 * @Joomlacopyright Copyright (c)2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @CAlicense		GNU General Public License version 3 or later; See http://www.gnu.org/copyleft/gpl.html
 * 
 * This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 */

defined('_JEXEC') or die;

/**
 * [%%ArchitectComp%%] navigation plugin class.
 *
 */
class Plg[%%ArchitectComp%%]Itemnavigation extends JPlugin
{
	/**
	 * @var    $autoloadLanguage boolean	Load the language file on instantiation
	 */
	protected $autoloadLanguage = true;
	
	[%%FOREACH COMPONENT_OBJECT%%]
		[%%IF GENERATE_PLUGINS%%]
			[%%IF GENERATE_PLUGINS_ITEMNAVIGATION%%]
	/**
	 * On Before Display event procedure for item navigation
	 * @param	string			$context	Context of the paging
	 * @param	array			&$row		Passed by reference and row updated with html for prev and/or next buttons
	 * @param	json/registry	&$params	Item navigation parameters	
	 * @param	integer			$page		Current Item page		
	 * 
	 * @return  void
	 */			
	public function on[%%CompObject%%]BeforeDisplay ($context, &$row, &$params, $page=0)
	{
		$app = JFactory::getApplication();

		$view = $app->input->getString('view');
		$layout = $app->input->getString('layout');
		$print = $app->input->getBool('print');

		if ($print) 
		{
			return false;
		}

		if ($params->get('show_[%%compobject%%]_navigation') AND 
			($context == '[%%com_architectcomp%%].[%%compobject%%]') AND 
			($view == '[%%compobject%%]')) 
		{
			
			$html = '';
			$db		= JFactory::getDbo();
			$user	= JFactory::getUser();
			$lang	= JFactory::getLanguage();
			$null_date = $db->getNullDate();

			$date	= JFactory::getDate();
			$now	= $date->toSQL();

			$uid	= $row->id;
			$option	= '[%%com_architectcomp%%]';
			[%%IF INCLUDE_ASSETACL%%]
				[%%IF INCLUDE_ASSETACL_RECORD%%]
			$can_publish = $user->authorise('core.edit.state', $option.'.[%%compobject%%].'.$row->id);
				[%%ELSE INCLUDE_ASSETACL_RECORD%%]
			$can_publish = $user->authorise('core.edit.state', $option);
				[%%ENDIF INCLUDE_ASSETACL_RECORD%%]
			[%%ENDIF INCLUDE_ASSETACL%%]
			$query	= $db->getQuery(true);
			

			
			[%%IF INCLUDE_NAME%%]
				[%%IF INCLUDE_ALIAS%%]
	        $slug_select = ' CASE WHEN ';
	        $slug_select .= $query->charLength('a.alias', '!=', '0');
	        $slug_select .= ' THEN ';
	        $a_id = $query->castAsChar('a.id');
	        $slug_select .= $query->concatenate(array($a_id, 'a.alias'), ':');
	        $slug_select .= ' ELSE ';
	        $slug_select .= $a_id.' END as slug, ';			
					[%%IF GENERATE_CATEGORIES%%]
	        $slug_select .= ' CASE WHEN ';
	        $slug_select .= $query->charLength('cc.alias', '!=', '0');
	        $slug_select .= ' THEN ';
	        $c_id = $query->castAsChar('cc.id');
	        $slug_select .= $query->concatenate(array($c_id, 'cc.alias'), ':');
	        $slug_select .= ' ELSE ';
			$slug_select .= $c_id.' END as catslug, ';			
					[%%ENDIF GENERATE_CATEGORIES%%]
				[%%ELSE INCLUDE_ALIAS%%]
			$a_id = $query->castAsChar('a.id');			
			$slug_select = $a_id.' as slug, ';
					[%%IF GENERATE_CATEGORIES%%]
	        $c_id = $query->castAsChar('cc.id');				
			$slug_select .=	$c_id.' as catslug, ';
					[%%ENDIF GENERATE_CATEGORIES%%]			
				[%%ENDIF INCLUDE_ALIAS%%]
			[%%ELSE INCLUDE_NAME%%]
			$a_id = $query->castAsChar('a.id');			
			$slug_select = $a_id.' as slug, ';
					[%%IF GENERATE_CATEGORIES%%]
	        $c_id = $query->castAsChar('cc.id');				
			$slug_select .=	$c_id.' as catslug, ';
					[%%ENDIF GENERATE_CATEGORIES%%]			
			[%%ENDIF INCLUDE_NAME%%]
			
			$query->select($slug_select.
				[%%IF INCLUDE_PARAMS_RECORD%%]				
				$db->quoteName('a.params').','.
				[%%ENDIF INCLUDE_PARAMS_RECORD%%]
				[%%IF INCLUDE_LANGUAGE%%]
				$db->quoteName('a.language').', '.
				[%%ENDIF INCLUDE_LANGUAGE%%]
				[%%IF GENERATE_CATEGORIES%%]
				$db->quoteName('a.catid').', '.
				[%%ENDIF GENERATE_CATEGORIES%%]
				$db->quoteName('a.id'));
			$query->from($db->quoteName('#__[%%architectcomp%%]_[%%compobjectplural%%]').' AS a');
			[%%IF GENERATE_CATEGORIES%%]
			$query->join('LEFT', $db->quoteName('#__categories').' AS cc ON '.$db->quoteName('cc.id').' = '.$db->quoteName('a.catid'));
			[%%ENDIF GENERATE_CATEGORIES%%]
			[%%IF INCLUDE_CREATED%%]
			// Join over users for created by
			$query->select($db->quoteName('ua.name').' AS created_by_name');
			$query->join('LEFT', $db->quoteName('#__users').' AS ua on '.$db->quoteName('ua.id').' = '.$db->quoteName('a.created_by'));
			[%%ENDIF INCLUDE_CREATED%%]
			
			[%%IF INCLUDE_LANGUAGE%%]
			if ($app->isSite() AND JLanguageMultilang::isEnabled())
			{
				$query->where($db->quoteName('a.language').' IN ('.$db->quote($lang->getTag()).','.$db->quote('*').')');
			}
			[%%ENDIF INCLUDE_LANGUAGE%%]
						
			[%%IF GENERATE_CATEGORIES%%]
			// Filter by a same category as the selected row
			if ($params->get('limit_category_fieldtype_navigation',false) == true) 
			{
				$query->where($db->quoteName('a.catid').' = '. (int)$row->catid);
			} 
			[%%ENDIF GENERATE_CATEGORIES%%]
			

			[%%IF INCLUDE_STATUS%%]
				[%%IF INCLUDE_ASSETACL%%]
			if (!$can_publish) 
			{
				[%%ENDIF INCLUDE_ASSETACL%%]
				$query->where( '('.$db->quoteName('a.state').' = 1 OR '.$db->quoteName('a.state').' = -1)'
					[%%IF INCLUDE_PUBLISHED_DATES%%]
					.' AND ('.$db->quoteName('a.publish_up').' = '.$db->quote($null_date)
					.' OR '.$db->quoteName('a.publish_up').' <= '.$db->quote($now).')'
					.' AND ('.$db->quoteName('a.publish_down').' = '.$db->quote($null_date)
					.' OR '.$db->quoteName('a.publish_down').' >= '.$db->quote($now).')'
					[%%ENDIF INCLUDE_PUBLISHED_DATES%%]
					);
				[%%IF INCLUDE_ASSETACL%%]
			}
			else
			{
				$query->where($db->quoteName('a.state').' = '. (int)$row->state);			
			}
				[%%ENDIF INCLUDE_ASSETACL%%]
			[%%ELSE INCLUDE_STATUS%%]
				[%%IF INCLUDE_PUBLISHED_DATES%%]
					[%%IF INCLUDE_ASSETACL%%]
			if (!$can_publish) 
			{
					[%%ENDIF INCLUDE_ASSETACL%%]
				$query->where('('.$db->quoteName('a.publish_up').' = '.$db->quote($null_date)
					.' OR '.$db->quoteName('a.publish_up').' <= '.$db->quote($now).')'
					.' AND ('.$db->quoteName('a.publish_down').' = '.$db->quote($null_date)
					.' OR '.$db->quoteName('a.publish_down').' >= '.$db->quote($now).')'
					);
					[%%IF INCLUDE_ASSETACL%%]
			}
					[%%ENDIF INCLUDE_ASSETACL%%]
				[%%ENDIF INCLUDE_PUBLISHED_DATES%%]
			[%%ENDIF INCLUDE_STATUS%%]
						
			[%%IF INCLUDE_ACCESS%%]
			if ($params->get('show_[%%compobject%%]_noauth') <> 1 AND $params->get('show_[%%compobject%%]_noauth') <> 'use_[%%compobject%%]')
			{
				$query->where($db->quoteName('a.access').' = ' .(int)$row->access);
			}
			[%%ENDIF INCLUDE_ACCESS%%]			
			[%%IF INCLUDE_PARAMS_RECORD%%]
			// Add the list ordering clause.
			$initial_sort = $params->get('[%%compobject%%]_initial_sort');
			// Falll back to old style if the parameter hasn't been set yet.
			if (empty($initial_sort))
			{
				[%%IF INCLUDE_ORDERING%%]
				$query->order($db->quoteName($db->escape($params->get('list.ordering', 'a.ordering'))).' '.$db->escape($params->get('list.direction', 'ASC')));
				[%%ELSE INCLUDE_ORDERING%%]
					[%%IF INCLUDE_NAME%%]
				$query->order($db->quoteName($db->escape($params->get('list.ordering', 'a.name'))).' '.$db->escape($params->get('list.direction', 'ASC')));
					[%%ELSE INCLUDE_NAME%%]				
				$query->order($db->quoteName($db->escape($params->get('list.ordering', 'a.id'))).' '.$db->escape($params->get('list.direction', 'ASC')));
					[%%ENDIF INCLUDE_NAME%%]				
				[%%ENDIF INCLUDE_ORDERING%%]			
			}
			else 
			{
				$query->order($db->quoteName('a.'.$initial_sort).' '.$db->escape($params->get('list.direction', 'ASC')));
			}	
			[%%ENDIF INCLUDE_PARAMS_RECORD%%]			
			$db->setQuery($query);
			
			$list = $db->loadObjectList('id');

			// This check needed if incorrect Itemid is given resulting in an incorrect result.
			if (!is_array($list)) 
			{
				$list = array();
			}

			reset($list);

			// Location of current [%%compobject_name%%] item in array list.
			$location = array_search($uid, array_keys($list));

			$rows = array_values($list);

			$row->prev = null;
			$row->next = null;
			
			// Get the global params
			$global_params = JComponentHelper::getParams('[%%com_architectcomp%%]', true);

			if ($location -1 >= 0)	
			{
				$row->prev = $location -1 ; 
				// The previous [%%compobject_name%%] item cannot be in the array position -1.
				for ($i = $location-1; $i >= 0; $i--)
				{

					$row->prev = $rows[$i];
					break;

				}

			}

			if (($location +1) < count($rows)) 
			{
				$row->next = $location +1;
				// The next [%%compobject_name%%] item cannot be in an array position greater than the number of array postions.
				for ($i = $location+1; $i <= count($rows)-1; $i++)
				{
					$row->next = $rows[$i];
					break;
				}	

			}

			$pn_space = "";
			if (JText::_('JGLOBAL_LT') OR JText::_('JGLOBAL_GT')) 
			{
				$pn_space = " ";
			}


			$keep_item_id = (int) $params->get('keep_[%%compobject%%]_itemid', 0);		
					
			if ($row->prev) 
			{
				[%%IF INCLUDE_NAME%%]
				$row->prev_label = ($this->params->get('display', 0) == 0) ? JText::_('JPREV') : $row->prev->name;
				[%%ELSE INCLUDE_NAME%%]				
				$row->prev_label =JText::_('JPREV');
				[%%ENDIF INCLUDE_NAME%%]				
				[%%IF GENERATE_CATEGORIES%%]		 
					[%%IF INCLUDE_LANGUAGE%%]
				$row->prev = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($row->prev->slug,$row->prev->catid, $row->prev->language, $layout, $keep_item_id));
					[%%ELSE INCLUDE_LANGUAGE%%]
				$row->prev = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($row->prev->slug, $row->prev->catid, $layout, $keep_item_id));
					[%%ENDIF INCLUDE_LANGUAGE%%]
				[%%ELSE GENERATE_CATEGORIES%%]
					[%%IF INCLUDE_LANGUAGE%%]
				$row->prev = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($row->prev->slug, $row->prev->language, $layout, $keep_item_id));
					[%%ELSE INCLUDE_LANGUAGE%%]
				$row->prev =  JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($row->prev->slug, $layout, $keep_item_id));
					[%%ENDIF INCLUDE_LANGUAGE%%]	
				[%%ENDIF GENERATE_CATEGORIES%%]				
			} 
			else 
			{
				$row->prev_label = '';
				$row->prev = '';
			}

			if ($row->next) 
			{
				[%%IF INCLUDE_NAME%%]
				$row->next_label = ($this->params->get('display', 0) == 0) ? JText::_('JNEXT') : $row->next->name;
				[%%ELSE INCLUDE_NAME%%]				
				$row->next_label = JText::_('JNEXT');
				[%%ENDIF INCLUDE_NAME%%]				
				[%%IF GENERATE_CATEGORIES%%]		 
					[%%IF INCLUDE_LANGUAGE%%]
				$row->next = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($row->next->slug,$row->next->catid, $row->next->language, $layout, $keep_item_id));
					[%%ELSE INCLUDE_LANGUAGE%%]
				$row->next = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($row->next->slug, $row->next->catid, $layout, $keep_item_id));
					[%%ENDIF INCLUDE_LANGUAGE%%]
				[%%ELSE GENERATE_CATEGORIES%%]
					[%%IF INCLUDE_LANGUAGE%%]
				$row->next = JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($row->next->slug, $row->next->language, $layout, $keep_item_id));
					[%%ELSE INCLUDE_LANGUAGE%%]
				$row->next =  JRoute::_([%%ArchitectComp%%]HelperRoute::get[%%CompObject%%]Route($row->next->slug, $layout, $keep_item_id));
					[%%ENDIF INCLUDE_LANGUAGE%%]	
				[%%ENDIF GENERATE_CATEGORIES%%]				
							
			} 
			else 
			{
				$row->next_label = '';
				$row->next = '';
			}

			// Output.
			if ($row->prev OR $row->next) 
			{
				$lang = JFactory::getLanguage();
				
				$html = '<ul class="pager pagenav">';
				if ($row->prev) 
				{
					$direction = $lang->isRTL() ? 'right' : 'left';
					$html .= '
					<li class="previous">
						<a href="'. $row->prev .'" rel="prev">'
						. '<i class="icon-chevron-' . $direction . '"></i> ' . $row->prev_label
						. '</a>'
					.'</li>';
				}



				if ($row->next) 
				{
					$direction = $lang->isRTL() ? 'left' : 'right';
					$html .= '
					<li class="next">
						<a href="'. $row->next .'" rel="next">'
							. $row->next_label . ' <i class="icon-chevron-' . $direction . '"></i>'
					. '</a>'
					. '</li>';
				}
				$html .= '</ul>';
				
				$row->pagination = $html;
				$row->paginationposition = $this->params->get('[%%compobject%%]_position', 1);
				// This will default to the 1.5 and 1.6-1.7 behavior.
				$row->paginationrelative = $this->params->get('[%%compobject%%]_relative',0);				
			}
		}

		return ;
	}
			[%%ENDIF GENERATE_PLUGINS_ITEMNAVIGATION%%]
		[%%ENDIF GENERATE_PLUGINS%%]
	[%%ENDFOR COMPONENT_OBJECT%%]		
}
