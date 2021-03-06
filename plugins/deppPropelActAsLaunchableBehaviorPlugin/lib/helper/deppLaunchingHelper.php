<?php
/*
 * This file is part of the deppPropelActAsLaunchableBehaviorPlugin
 * 
 * @author Guglielmo Celata <guglielmo.celata@gmail.com>
 */
sfLoader::loadHelpers(array('Tag', 'I18N'));

$response = sfContext::getInstance()->getResponse();

//$css = '/deppPropelActAsLaunchableBehaviorPlugin/css/depp_launching';
//if ( !array_key_exists( $css, $response->getStylesheets() ) )
//	$response->addStylesheet($css);

$js = '/js/jquery-ajaxsort.js';
if ( !array_key_exists( $js, $response->getJavascripts() ) )
	$response->addJavascript($js);

/**
 * Return the HTML code of the div containing the launcher tool
 * If the user has already launched, then a message appears
 * 
 * @param  BaseObject  $object     Propel object instance to launch
 * @param  string      $namespace  The namespace where the object is *launched*
 * @param  array       $options    Array of HTML options to apply on the HTML list
 * @return string
 **/
function depp_launching_block($object, $namespace, $options = array())
{
  return content_tag('div', depp_launcher($object, $namespace, $options));
}

/**
 * Return the HTML code for the launch/remove link 
 * plus the list of launched object for the namespace
 * 
 * @param  BaseObject  $object     Propel object instance to launch
 * @param  string      $namespace  The namespace where the object is *launched*
 * @param  array       $options    Array of HTML options to apply on the HTML list
 * @return string
 **/
function depp_launcher($object, $namespace, $options = array())
{

  if (is_null($object))
  {
    sfLogger::getInstance()->debug('A NULL object cannot be launched');
    return '';
  }

  if (is_null($namespace))
  {
    sfLogger::getInstance()->debug('A namespace must be given');
    return '';
  }
  
  try
  {
    $options = _parse_attributes($options);
    if (!isset($options['id']))
    {
      $options = array_merge($options, array('id' => 'launching-items'));
    }
	if (!isset($options['class']))
    {
      $options = array_merge($options, array('class' => 'vote-administration'));
    }
    
    $object_model = get_class($object);
    $object_id = $object->getPrimaryKey();

    // build launch/remove link
    if (in_array($namespace, $object->hasBeenLaunched()))
    {
      $action_link = link_to(__('Take the launch back'), 
                             sprintf('deppLaunching/remove?item_model=%s&item_pk=%s&namespace=%s',
                                     $object_model, $object_id, $namespace));
    } else {
      $action_link = link_to(__('Launch the object'), 
                             sprintf('deppLaunching/launch?item_model=%s&item_pk=%s&namespace=%s',
                                     $object_model, $object_id, $namespace));
    }
    $action = content_tag('div', $action_link);
    
    $list_content = '';
    $launches = sfLaunchingPeer::getAllByNamespace($namespace);
    foreach ($launches as $i => $l)
    {
      $l_obj_model = $l->getObjectModel();
      $l_obj_id = $l->getObjectId();
      $l_obj = deppPropelActAsLaunchableToolkit::retrieveLaunchableObject($l_obj_model, $l_obj_id);
      $l_obj_short_string = $l_obj->getShortTitle();
      $l_obj_remove_action = sprintf('deppLaunching/remove?item_model=%s&item_pk=%s&namespace=%s',
                                     $l_obj_model, $l_obj_id, $namespace);
      $l_obj_priority_up_action = sprintf('deppLaunching/priorityUp?item_model=%s&item_pk=%s&namespace=%s',
                                          $l_obj_model, $l_obj_id, $namespace);
      $l_obj_priority_dn_action = sprintf('deppLaunching/priorityDn?item_model=%s&item_pk=%s&namespace=%s',
                                          $l_obj_model, $l_obj_id, $namespace);
                                     
      $l_obj_remove_link = link_to('<img src="/images/ico-remove_alert.png" alt="X" title="Rimuovi" />', $l_obj_remove_action, array('title' => __('Take the launch back'), 'class' => 'remove-vote'));
      $l_obj_priority_up_link = link_to('<img src="/images/ico-thumb-up.png" alt="+" title="Aumenta priorità" />', $l_obj_priority_up_action, array('title' => __('Increase the priority'), 'class' => 'moveup-vote'));
      $l_obj_priority_dn_link = link_to('<img src="/images/ico-thumb-down.png" alt="-" title="Diminuisci priorità" />', $l_obj_priority_dn_action, array('title' => __('Decrease the priority'), 'class' => 'movedown-vote'));
      
      $l_obj_actions = "";
      /*if ($i > 0)*/ $l_obj_actions .= " $l_obj_priority_up_link ";
      /*if ($i < count($launches) - 1 )*/ $l_obj_actions .= " $l_obj_priority_dn_link ";
      $l_obj_actions .= " $l_obj_remove_link ";
      

      /*$list_content .= content_tag('tr', 
                                   content_tag('td', '<input type="text" value="'. $l->getPriority().'" name="priority['. $l_obj_id.']" size="3">'. $l_obj_short_string) . 
                                   content_tag('td', $l_obj_actions, array('style' => 'text-align:right; width:36px;display:inline-block;')));*/
		$list_content .= content_tag('li', 
				content_tag('span', $l_obj_actions, array('style' => 'text-align:right; width:36px;display:inline-block;float:right;')) .
				 $l_obj_short_string,
				 array('style' => 'cursor:move; border-bottom: 1px dotted #CCC;') 
                         );
    }
    $list = content_tag('ul', $list_content, $options);
    
	// adding javascript for drag and drop
	//use_javascript('/js/jquery-ui-1.8.16.sortable.min.js');

    return $action . $list;
  }
  catch (Exception $e)
  {
    sfLogger::getInstance()->err('Exception catched from deppLaunching helper: '.$e->getMessage());
  }
}
