<?php

  /*

  All Emoncms code is released under the GNU Affero General Public License.
  See COPYRIGHT.txt and LICENSE.txt.

  ---------------------------------------------------------------------
  Emoncms - open source energy visualisation
  Part of the OpenEnergyMonitor project:
  http://openenergymonitor.org

  Nodes table
  

  id | hostname | comments | y | x | typeid
  
  typeid 0: emonTx
  typeid 1: emonBase
  typeid 2: emonGLCD
  typeid 3: emonPlug
  typeid 4: emonMeter
  typeid 5: Arduino
  typeid 6: No Type
  

  */
  
  $types = array(
	"1" => "emonTx",
	"2" => "emonBase",
	"3" => "emonGLCD",
	"4" => "emonPlug",
	"5" => "emonMeter",
	"6" => "Arduino",
	"7" => _("No type")
  );

  function create_node($id, $userid, $hostname, $comments, $x,  $y, $typeid) {
    // If node of given hostname by the user already exists
    $nodeid = get_node_id($userid,$hostname);
    $typeid = array_search($typeid, $types);
    
    if ($nodeid!=0)
		return $nodeid;

    $result = db_query("INSERT INTO nodes (id, userid, hostname, comments, x, y, typeid) VALUES ('$id', '$userid', '$hostname', '$comments', '$x', '$y', '$typeid')");
    $nodeid = db_insert_id();

    if ($nodeid > 0) {
		return $nodeid;											
    }
    else
		return 0;
  }
  
  function get_node_id($userid, $hostname)
  {
    $result = db_query("SELECT id FROM nodes WHERE userid = '$userid' AND hostname = '$hostname'");
    $row = db_fetch_array($result);
    return $row['id'];
  }
  
  function node_belongs_to_user($nodeid,$userid)
  {
    $result = db_query("SELECT id FROM nodes WHERE `userid` = '$userid' AND `id` = '$nodeid'");
    $row = db_fetch_array($result);
    if ($row)
		return true;
    else
		return false;
  }

  /*

  User Nodes lists

  Returns a specified user's nodelist in different forms:
  get_user_nodes: 	all the nodes table data

  */

  function get_user_nodes($userid) {
	  $result = db_query("SELECT * FROM nodes WHERE userid = '$userid'");
	  if (!$result) return 0;
	  $nodes = array();
	  while ($row = db_fetch_object($result)) {
		  $nodes[] = $row;
	  }
    return $nodes;
  }

  /*

  Nodes table GET functions

  */

  function get_node($id) {
    $result = db_query("SELECT * FROM nodes WHERE id = $id");
    return db_fetch_object($result);
  }

  function get_node_field($id,$field)
  {
    $result = db_query("SELECT `$field` FROM nodes WHERE `id` = '$id'");
    $row = db_fetch_array($result);
    return $row[0];
  }
  
  /*

  Nodes SET functions

  */

  function set_node_field($id,$field,$value)
  {
    if ($field!='id') {
		if ($field =='typeid')
			$typeid = array_search($typeid, $types);
		$result = db_query("UPDATE nodes SET `$field` = '$value' WHERE id = $id");
		return $result;
	}
  }
  
  /*

  Nodes wastebin, restore and permanent deletion

  */

  function delete_node($nodeid, $userid)
  {
    db_query("DELETE FROM nodes WHERE id = '$nodeid' AND userid = '$userid'");
  }

?>
