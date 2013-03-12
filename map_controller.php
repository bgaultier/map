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
  
  class ProcessArg {
    const VALUE = 0;
    const INPUTID = 1;
    const FEEDID = 2;
  }

  class DataType {
    const UNDEFINED = 0;
    const REALTIME = 1;
    const DAILY = 2;
    const HISTOGRAM = 3;
  }
  
  function map_controller() {

    global $mysqli, $session, $route;

    include "Modules/map/map_model.php";
    $map = new Map($mysqli);

    require "Modules/feed/feed_model.php";
    $feed = new Feed($mysqli);

    require "Modules/input/input_model.php";
    $input = new Input($mysqli,$feed);

    require "Modules/input/process_model.php";
    $process = new Process($mysqli,$input,$feed);
   
    if ($route->action == "list")
    { 
      $userid = intval(get('userid'));
      if ($session['read']) { 
        $nodes = $map->get_user_nodes($session['userid']);
        $inputs = $input->getlist($session['userid']);
        
        $tmp = array();
        foreach ($inputs as $item) {
        	$tmp[] = (int) $item->nodeid;
        }
	      $inputNodes = array_unique($tmp);
      }

      if (isset($nodes)) {
        if ($route->format == 'json') $result = $nodes;
        if ($route->format == 'html' && $session['read']) $result = view("Modules/map/Views/map_view.php", array('nodes' => $nodes, 'inputNodes' => $inputNodes));
      }
    }
    elseif($route->action == "nodes")
    { 
      $userid = intval(get('userid'));
      if ($session['read']) { 
        $nodes = $map->get_user_nodes($session['userid']);
      }

      if (isset($nodes)) {
      	 $tmp = array();
      	 foreach ($nodes as $node) {
      	 	$node = ((array)$node);
          // note: we need to check if $node['id'] belongs to user here as $userid attribute has been removed:
          $id = $input->get_id($session['userid'],$node['id'],1);
          $feeds = $input->get_processlist_desc($process,$id); 
      	 	$feedlist = array();
      	 	foreach ($feeds as $feeditem) {
      	 		$field['id'] = $feed->get_field($feed->get_id($session['userid'],$feeditem[1]),'id'); // is get_field needed?
      	 		$field['name'] = $feeditem[1];
      	 		$feedlist[] = $field;
      	 	}
      	 	$node['feedlist'] = $feedlist;
      	 	$tmp[] = $node;
        }
	//var_dump($tmp);
        if ($route->format == 'json') $result = array("nodes"=>$tmp, "links"=>array(array("source"=>0,"target"=>1,"value"=>1)));
      }
    }
    elseif($route->action == "types")
    {
      if ($session['read']) {
		  if ($route->format == 'json') $result = $map->types;
      }
    }
    elseif ($route->action == 'add' && $session['write'])
    {
      $nodeid = intval(get("nodeid"));  
      $hostname = preg_replace('/[^\w\s-]/','',get('hostname'));
      $comments = preg_replace('/[^\w\s-]/','',get('comments'));      
      $x = preg_replace('/[^\w\s-]/','',get('x'));
      $y = preg_replace('/[^\w\s-]/','',get('y'));
      $type = preg_replace('/[^\w\s-]/','',get('type'));

      $nodecreated = $map->create_node($nodeid, $session['userid'], $hostname, $comments, $x, $y, $type);
      
      if ($route->format == 'html') {
		  if($nodecreated > 0)
			$result = _("Node created");
		  else
			$result = _("Node already exist");
			
  		header("Location: list");
      }
      else
		$result = "ok";
    }
    
    /*
		Node property actions
    */
    elseif ($route->action == 'set' && $session['write'])
    {
      $nodeid = intval(get('id'));
      if ($map->node_belongs_to_user($nodeid,$session['userid']))
      {
        $result = $map->set_node_fields(get('id'),get('fields'));
      }
    }
    
    /*

    Delete

    */

    //---------------------------------------------------------------------------------------------------------
    // Delete a feed ( move to recycle bin, so not permanent )
    // http://yoursite/emoncms/feed/delete?id=1
    //--------------------------------------------------------------------------------------------------------- 
    elseif ($route->action == "delete" && $session['write'])
    { 
      $nodeid = intval(get("id"));
      if ($map->node_belongs_to_user($nodeid,$session['userid']))
      {
        $map->delete_node($nodeid, $session['userid']);
        $result = _("Node ") . $map->get_node_field($nodeid,'hostname'). _(" deleted");
      } else $result = _("Node does not exist");
    }
    
    return array('content'=>$result);
  }

?>
