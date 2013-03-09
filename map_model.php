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
  

class Map
{

  public $types = array(
	"1" => "emonTx",
	"2" => "emonBase",
	"3" => "emonGLCD",
	"4" => "emonPlug",
	"5" => "emonMeter",
	"6" => "Arduino",
	"7" => "No type"
  );

  private $mysqli;

  public function __construct($mysqli)
  {
    $this->mysqli = $mysqli;
  }

  public function create_node($id, $userid, $hostname, $comments, $x,  $y, $typeid) {
    // If node of given hostname by the user already exists
    $nodeid = $this->get_node_id($userid,$hostname);
    $typeid = array_search($typeid, $this->types);
    
    if ($nodeid!=0)
		return $nodeid;

    $result = $this->mysqli->query("INSERT INTO nodes (id, userid, hostname, comments, x, y, typeid) VALUES ('$id', '$userid', '$hostname', '$comments', '$x', '$y', '$typeid')");
    $nodeid = $this->mysqli->insert_id;

    if ($nodeid > 0) {
		return $nodeid;											
    }
    else
		return 0;
  }
  
  public function get_node_id($userid, $hostname)
  {
    $result = $this->mysqli->query("SELECT id FROM nodes WHERE userid = '$userid' AND hostname = '$hostname'");
    $row = $result->fetch_array();
    return $row['id'];
  }
  
  public function node_belongs_to_user($nodeid,$userid)
  {
    $result = $this->mysqli->query("SELECT id FROM nodes WHERE `userid` = '$userid' AND `id` = '$nodeid'");
    $row = $result->fetch_array();
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

  public function get_user_nodes($userid) {
	  $result = $this->mysqli->query("SELECT * FROM nodes WHERE userid = '$userid'");
	  if (!$result) return 0;
	  $nodes = array();
	  while ($row = $result->fetch_object()) {
		  $nodes[] = $row;
	  }
    return $nodes;
  }

  /*

  Nodes table GET functions

  */

  public function get_node($id) {
    $result = $this->mysqli->query("SELECT * FROM nodes WHERE id = $id");
    return $result->fetch_object();
  }

  public function get_node_field($id,$field)
  {
    $result = $this->mysqli->query("SELECT `$field` FROM nodes WHERE `id` = '$id'");
    $row = $result->fetch_array();
    return $row[0];
  }
  
  /*

  Nodes SET functions

  */

  public function set_node_fields($id,$fields)
  {
    $id = intval($id);
    $fields = json_decode($fields);

    $array = array();

    // Repeat this line changing the field name to add fields that can be updated:
    if (isset($fields->hostname)) $array[] = "`hostname` = '".preg_replace('/[^\w\s-]/','',$fields->hostname)."'";
    if (isset($fields->comments)) $array[] = "`comments` = '".preg_replace('/[^\w\s-]/','',$fields->comments)."'";
    if (isset($fields->x)) $array[] = "`x` = '".((int)$fields->x)."'";
    if (isset($fields->y)) $array[] = "`y` = '".((int)$fields->y)."'";
    if (isset($fields->typeid)) $array[] = "`typeid` = '".((int)$fields->typeid)."'";
    // Convert to a comma seperated string for the mysql query
    $fieldstr = implode(",",$array);
    $this->mysqli->query("UPDATE nodes SET ".$fieldstr." WHERE `id` = '$id'");

    if ($this->mysqli->affected_rows>0){
      return array('success'=>true, 'message'=>'Field updated');
    } else {
      return array('success'=>false, 'message'=>'Field could not be updated');
    }
  }
  
  /*

  Nodes wastebin, restore and permanent deletion

  */

  public function delete_node($nodeid, $userid)
  {
    $this->mysqli->query("DELETE FROM nodes WHERE id = '$nodeid' AND userid = '$userid'");
  }
}

?>
