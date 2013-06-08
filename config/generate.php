<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Mathieu
 * Date: 08/06/13
 * Time: 15:55
 * To change this template use File | Settings | File Templates.
 */

require_once 'config.php';
require_once '../lib/pdos.php';
require_once '../lib/epo.php';

define('THIS_MODEL_DIR', '../' . MODEL_DIR);
define('THIS_T_MODEL_DIR', '../' . TMODEL_DIR);
define('DISCLAIMER', "<?php
/**
* WARNING !
* This is an auto-generated file. DO NOT EDIT IT !
* It will be overwritten at the next database import.
*/
\n");
define('TAB', '  ');

function writeLine($str)
{
  echo $str . "\n";
}

function writeField($field)
{
  $fieldCapitalize    = $field;
  $fieldCapitalize[0] = strtoupper($fieldCapitalize[0]);
  $str                = TAB . 'private $_' . $field . ";\n\n";

  //Writing getter
  $str .= TAB . "public function get" . $fieldCapitalize . "()\n" . TAB . "{\n";
  $str .= TAB . TAB . 'return $this->_' . $field . ";\n" . TAB . "}\n\n";

  //Writting setter
  $str .= TAB . "public function set" . $fieldCapitalize . '($' . $field . ")\n" . TAB . "{\n";
  $str .= TAB . TAB . '$this->_' . $field . ' = $' . $field . ";\n" . TAB . "}\n\n";

  return $str;
}

function createT_Model($name, $fields)
{
  $name         = strtolower($name);
  $className    = "T_" . $name;
  $className[2] = strtoupper($className[2]);

  $file = fopen(THIS_T_MODEL_DIR . 't_' . $name . '.php', 'w');

  if ($file)
  {
    fwrite($file, DISCLAIMER);
    fwrite($file, 'class ' . $className . ' extends \Lib\Models\Deletable' . "\n{\n");

    foreach ($fields as $field)
    {
      fwrite($file, writeField($field));
    }

    fwrite($file, "}\n");
    fclose($file);
  }
}

writeLine("Connecting to " . DBNAME . " on " . DBHOST . "...");
$pdo = null;
try
{
  $pdo = \Lib\PDOS::getInstance();
}
catch (Exception $e)
{
  writeLine("Error ! Connection to database failed.");
  exit(1);
}

writeLine("Success !");
writeLine("Retrieving database public schema...");

//Getting all fields of all tables from public schema
$query = $pdo->prepare("SELECT table_name, column_name FROM information_schema.columns
  WHERE table_schema = 'public' ORDER BY table_name");
$query->execute();

$tables_fields = array();
$tables        = array();
$fields        = $query->fetchAll();

foreach ($fields as $field)
{
  if (!in_array($field['table_name'], $tables))
    $tables[] = $field['table_name'];
  if ($field['column_name'] != 'id')
    $tables_fields[$field['table_name']][] = $field['column_name'];
}

unset($fields);
unset($field);

//Foreach table, generates both T_Model and Model
foreach ($tables as $table)
{
  $fields = $tables_fields[$table];

  //Retrieving the 's' add the end of the table name
  $table = substr($table, 0, -1);
  createT_Model($table, $fields);
}