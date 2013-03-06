<?php

  /*

  All Emoncms code is released under the GNU Affero General Public License.
  See COPYRIGHT.txt and LICENSE.txt.

  ---------------------------------------------------------------------
  Emoncms - open source energy visualisation
  Part of the OpenEnergyMonitor project:
  http://openenergymonitor.org

  */

  // no direct access
  defined('EMONCMS_EXEC') or die('Restricted access');
  
  
  function map_controller() {
    include "Modules/map/map_model.php";
    require "Modules/input/input_model.php";
    require "Modules/feed/feed_model.php";
    require "Modules/input/process_model.php";
    require "Modules/input/input_controller.php";
    
    
    
    global $session, $route;

    $format = $route['format'];
    $action = $route['action'];

    $output['content'] = "";
    $output['message'] = "";
    
    if ($action == "list")
    { 
      $userid = intval(get('userid'));
      if ($session['read']) { 
        $nodes = get_user_nodes($session['userid']);
        $inputs = get_user_inputsbynode($session['userid']);
        
        $tmp = array();
        foreach ($inputs as $input) {
        	$tmp[] = $input['nodeid'];
        }
	$inputNodes = array_unique($tmp);
      }

      if (isset($nodes)) {
        if ($format == 'json') $output['content'] = json_encode($nodes);
        if ($format == 'html' && $session['read']) $output['content'] = view("map/Views/map_view.php", array('nodes' => $nodes, 'inputNodes' => $inputNodes));
      }
    }
    elseif($action == "nodes")
    { 
      $userid = intval(get('userid'));
      if ($session['read']) { 
        $nodes = get_user_nodes($session['userid']);
      }

      if (isset($nodes)) {
      	 $tmp = array();
      	 foreach ($nodes as $node) {
      	 	$node = ((array)$node);
      	 	$feeds = get_input_processlist_desc($session['userid'], $node['id']);
      	 	$feedlist = array();
      	 	foreach ($feeds as $feed) {
      	 		$field['id'] = get_feed_field(get_feed_id($session['userid'],$feed[1]),'id');
      	 		$field['name'] = $feed[1];
      	 		$feedlist[] = $field;
      	 	}
      	 	$node['feedlist'] = $feedlist;
      	 	$tmp[] = $node;
        }
	//var_dump($tmp);
        if ($format == 'json') $output['content'] = "{\"nodes\":" . json_encode($tmp) . ",\"links\":[{\"source\":0,\"target\":1,\"value\":1}]}";
      }
    }
    elseif($action == "types")
    {
      if ($session['read']) {
		  if ($format == 'json') $output['content'] = json_encode($types);
      }
    }
    elseif ($action == 'add' && $session['write'])
    {
      $nodeid = intval(get("nodeid"));  
      $hostname = preg_replace('/[^\w\s-]/','',get('hostname'));
      $comments = preg_replace('/[^\w\s-]/','',get('comments'));      
      $x = preg_replace('/[^\w\s-]/','',get('x'));
      $y = preg_replace('/[^\w\s-]/','',get('y'));
      $typeid = preg_replace('/[^\w\s-]/','',get('typeid'));
      
      
      
      $nodecreated = create_node($nodeid, $session['userid'], $hostname, $comments, $x, $y, $typeid);
      
      if ($format == 'html') {
		  if($nodecreated > 0)
			$output['message'] = _("Node created");
		  else
			$output['message'] = _("Node already exist");
			
		header("Location: list");
      }
      else
		$output['message'] = "ok";
    }
    
    /*
		Node property actions
    */
    elseif ($action == 'set' && $session['write'])
    {
      $nodeid = intval(get('id'));
      if (node_belongs_to_user($nodeid,$session['userid']))
      {
        $field = preg_replace('/[^\w\s-]/','',get('field'));
        $value = preg_replace('/[^\w\s-]/','',get('value'));
        var_dump(set_node_field($nodeid,$field,$value));
      }
    }
    
    /*

    Delete

    */

    //---------------------------------------------------------------------------------------------------------
    // Delete a feed ( move to recycle bin, so not permanent )
    // http://yoursite/emoncms/feed/delete?id=1
    //--------------------------------------------------------------------------------------------------------- 
    elseif ($action == "delete" && $session['write'])
    { 
      $nodeid = intval(get("id"));
      if (node_belongs_to_user($nodeid,$session['userid']))
      {
        delete_node($nodeid, $session['userid']);
        $output['message'] = _("Node ") . get_node_field($nodeid,'hostname'). _(" deleted");
      } else $output['message'] = _("Node does not exist");
    }
    
    return $output;
  }

?>
