<?php

$schema['nodes'] = array(
  'id' => array('type' => 'int(11)', 'Null'=>'NO', 'Key'=>'PRI'),
  'userid' => array('type' => 'int(11)'),
  'hostname' => array('type' => 'text'),
  'comments' => array('type' => 'text'),
  'x' => array('type' => 'int(11)'),
  'y' => array('type' => 'int(11)'),
  'typeid' => array('type' => 'int(11)')
);

?>
