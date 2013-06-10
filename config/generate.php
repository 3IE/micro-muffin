<?php
/**
 * User: Mathieu
 * Date: 08/06/13
 * Time: 15:55
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
  $str                = TAB . 'protected $_' . $field . ";\n\n";

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

  if ($file) {
    fwrite($file, DISCLAIMER);
    fwrite($file, 'class ' . $className . ' extends \Lib\Models\Deletable' . "\n{\n");

    foreach ($fields as $field) {
      fwrite($file, writeField($field));
    }

    fwrite($file, "}\n");
    fclose($file);
  }
}

function createModel($name)
{
  $name         = strtolower($name);
  $className    = $name;
  $className[0] = strtoupper($className[0]);

  if (!file_exists(THIS_MODEL_DIR . $name . '.php')) {
    $file = fopen(THIS_MODEL_DIR . $name . '.php', 'w');

    if ($file) {
      fwrite($file, "<?php\n\n");
      fwrite($file, 'class ' . $className . ' extends T_' . $className . "\n{\n");
      fwrite($file, "\n}\n");

      fclose($file);
    }
  }
}

function writeAllProcedure(\Lib\EPO &$pdo, $tableName)
{
  $procedureName = 'getall' . $tableName;

  $pdo->beginTransaction();

  $pdo->exec("CREATE OR REPLACE FUNCTION " . $procedureName . "()
  RETURNS SETOF JSON AS
  'SELECT row_to_json(" . $tableName . ") FROM " . $tableName . "'
  LANGUAGE SQL VOLATILE
  COST 100
  ROWS 1000;
  ALTER FUNCTION " . $procedureName . "()
  OWNER TO \"" . DBUSER . "\";");

  $pdo->commit();
}

function writeFindProcedure(\Lib\EPO &$pdo, $tableName)
{
  $procedureName = 'get' . substr($tableName, 0, -1) . 'fromid';
  $parameter = substr($tableName, 0, -1)."_id";
  $alias = $tableName[0];

  $pdo->beginTransaction();

  $pdo->exec("CREATE OR REPLACE FUNCTION ".$procedureName."(".$parameter." numeric)
  RETURNS json AS
  'SELECT row_to_json(".$alias.") FROM ".$tableName." ".$alias." WHERE ".$alias.".id = ".$parameter."'
  LANGUAGE sql VOLATILE
  COST 100;
  ALTER FUNCTION ".$procedureName."(numeric)
  OWNER TO \"".DBUSER."\";");

  $pdo->commit();
}

/**
 * SCRIPT BEGINS HERE
 */

writeLine("Connecting to " . DBNAME . " on " . DBHOST . "...");
$pdo = null;
try {
  $pdo = \Lib\PDOS::getInstance();
} catch (Exception $e) {
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

foreach ($fields as $field) {
  if (!in_array($field['table_name'], $tables))
    $tables[] = $field['table_name'];
  if ($field['column_name'] != 'id')
    $tables_fields[$field['table_name']][] = $field['column_name'];
}

unset($fields);
unset($field);

writeLine(count($tables) . ' table' . (count($tables) > 1 ? 's' : '') . ' found');
writeLine("Generating models...");

//Foreach table, generates both T_Model and Model
foreach ($tables as $table) {
  $fields            = $tables_fields[$table];
  $originalTableName = $table;

  //Retrieving the 's' add the end of the table name, if exists
  if ($table[strlen($table) - 1] == 's')
    $table = substr($table, 0, -1);

  $className    = $table;
  $className[0] = strtoupper($className[0]);

  createT_Model($table, $fields);
  writeLine(' T_' . $className . ' model written');

  createModel($table);
  writeLine(' ' . $className . ' model written');

  writeAllProcedure($pdo, $originalTableName);
  writeLine(' getall' . $originalTableName . '() function written in database');

  writeFindProcedure($pdo, $originalTableName);
  writeLine(' get' . substr($originalTableName, 0, -1) . 'fromid() function written in database');
  writeLine("");
}

writeLine("Done !");
writeLine("Generation finished ! Enjoy ;)");

/**
 * SCRIPT ENDS HERE
 */