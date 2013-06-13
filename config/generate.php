<?php
/**
 * User: Mathieu
 * Date: 08/06/13
 * Time: 15:55
 */

/**
 * HOW TO WRITE CUSTOM STORED PROCEDURE
 * Execute a SQL query like this one :
 *
 * CREATE OR REPLACE FUNCTION sp_truc()
 * RETURNS TABLE(article_id integer, user_id integer) AS
 * $func$
 * BEGIN
 * RETURN QUERY EXECUTE '
 * SELCET a.id AS article_id, u.id AS user_id
 * FROM articles a
 * INNER JOIN users u ON a.user_id = u.id'
 * END
 * $func$ LANGUAGE plpgsql;
 */

require_once 'config.php';
require_once '../lib/pdos.php';
require_once '../lib/epo.php';

define('THIS_MODEL_DIR', '../' . MODEL_DIR);
define('THIS_T_MODEL_DIR', '../' . TMODEL_DIR);
define('THIS_SP_MODEL_DIR', '../' . SPMODEL_DIR);
define('DISCLAIMER', "<?php
/**
* WARNING !
* This is an auto-generated file. DO NOT EDIT IT !
* It will be overwritten at the next database import.
*/
\n");
define('TAB', '  ');
define('SPMODELMATCH', '#^sp_[a-zA-Z0-9_]+$#');

function writeLine($str)
{
  echo $str . "\n";
}

/**
 * @param array $constraints
 * @param string $column
 * @param string $table
 * @return array|null
 */
function getForeignKey(Array $constraints, $column, $table)
{
  if (array_key_exists($table, $constraints))
  {
    $tableConstraints = $constraints[$table];
    foreach ($tableConstraints as $constraint)
    {
      if ($column == $constraint['column_name'])
        return $constraint;
    }
  }
  return null;
}

/**
 * @param string $table
 * @return string
 */
function removeSFromTableName($table)
{
  if ($table[strlen($table) - 1] == 's')
    $table = substr($table, 0, -1);
  return $table;
}

/**
 * @param string $field
 * @param bool $visible
 * @return string
 */
function writeField($field, $visible = true)
{
  $fieldCapitalize    = $field;
  $fieldCapitalize[0] = strtoupper($fieldCapitalize[0]);
  $str                = TAB . 'protected' . ' $_' . $field . ";\n\n";

  //Writing getter
  $str .= TAB . ($visible ? "public" : "private") . " function get" . $fieldCapitalize . "()\n" . TAB . "{\n";
  $str .= TAB . TAB . 'return $this->_' . $field . ";\n" . TAB . "}\n\n";

  //Writting setter
  $str .= TAB . ($visible ? "public" : "private") . " function set" . $fieldCapitalize . '($' . $field . ")\n" . TAB . "{\n";
  $str .= TAB . TAB . '$this->_objectEdited();' . "\n";
  $str .= TAB . TAB . '$this->_' . $field . ' = $' . $field . ";\n" . TAB . "}\n\n";

  return $str;
}

/**
 * @param string $field
 * @param string $foreignTable
 * @param string $foreignField
 * @return string
 */
function writeManyToOneJoin($field, $foreignTable, $foreignField)
{
  $str          = '';
  $className    = removeSFromTableName($foreignTable);
  $className[0] = strtoupper($className);
  $var          = $className;
  $var[0]       = strtolower($var[0]);

  //Object field
  $str .= TAB . "/** @var " . $className . " */\n";
  $str .= TAB . 'protected $' . $var . " = null;\n\n";

  //Getter
  $str .= TAB . "/** @return " . $className . " */\n";
  $str .= TAB . "public function get" . $className . "()\n" . TAB . "{\n";
  $str .= TAB . TAB . 'if (is_null($this->' . $var . '))' . "\n";
  $str .= TAB . TAB . TAB . '$this->' . $var . ' = ' . $className . '::find($this->_' . $field . ');' . "\n";
  $str .= TAB . TAB . 'return $this->' . $var . ';' . "\n";
  $str .= TAB . "}\n\n";

  //Setter
  $foreignFieldUp    = $foreignField;
  $foreignFieldUp[0] = strtoupper($foreignFieldUp[0]);
  $str .= TAB . "public function set" . $className . "(" . $className . " \$" . $var . ")\n" . TAB . "{\n";
  $str .= TAB . TAB . '$this->' . $var . ' = $' . $var . ";\n";
  $str .= TAB . TAB . '$this->_' . $field . ' = $' . $var . "->get" . $foreignFieldUp . "();\n";
  $str .= TAB . TAB . '$this->_objectEdited();' . "\n";
  $str .= TAB . "}\n\n";

  return $str;
}

/**
 * WARNING ! Foreign denomination is inverted compared with constraints query result
 *
 * @param string $foreignTable
 * @param string $foreignColumn
 * @param string $tableName
 * @return string
 */
function writeOneToManyProcedure($foreignTable, $foreignColumn, $tableName)
{
  $procedureName = 'otm_' . $foreignTable . 'from' . removeSFromTableName($tableName);
  $pdo           = \Lib\PDOS::getInstance();

  $pdo->beginTransaction();
  $pdo->exec("
  CREATE OR REPLACE FUNCTION " . $procedureName . "(foreign_id numeric)
  RETURNS SETOF " . $foreignTable . " AS
  'SELECT * FROM " . $foreignTable . " WHERE " . $foreignColumn . " = foreign_id'
  LANGUAGE sql VOLATILE
  COST 100
  ROWS 1000;
  ALTER FUNCTION " . $procedureName . "(numeric)
  OWNER TO " . DBUSER . ";");
  $pdo->commit();

  writeLine(' ' . $procedureName . '() written in database');

  return $procedureName;
}

/**
 * WARNING ! Foreign denomination is inverted compared with constraints query result
 *
 * @param string $foreignTableName
 * @param string $foreignColumnName
 * @param string $tableName
 * @param string $fieldName
 * @return string
 */
function writeOneToManyJoin($foreignTableName, $foreignColumnName, $tableName, $fieldName)
{
  $str             = '';
  $field           = $foreignTableName;
  $fieldUppered    = $field;
  $fieldUppered[0] = strtoupper($fieldUppered[0]);
  $procedure       = writeOneToManyProcedure($foreignTableName, $foreignColumnName, $tableName);

  $str .= TAB . '/** @var ' . removeSFromTableName($fieldUppered) . '[] */' . "\n";
  $str .= TAB . 'protected $' . $field . " = null;\n\n";
  $str .= TAB . "/**\n";
  $str .= TAB . " * @return " . substr($fieldUppered, 0, -1) . "[]\n";
  $str .= TAB . " */\n";
  $str .= TAB . 'public function get' . $fieldUppered . "()\n";
  $str .= TAB . "{\n";
  $str .= TAB . TAB . "if (is_null(\$this->" . $field . "))\n";
  $str .= TAB . TAB . "{\n";
  $str .= TAB . TAB . TAB . "\$pdo = \\Lib\\PDOS::getInstance();\n";
  $str .= TAB . TAB . TAB . "\$query = \$pdo->prepare('SELECT * FROM " . $procedure . "('.\$this->_id.')');\n";
  $str .= TAB . TAB . TAB . "\$query->execute();\n";
  $str .= TAB . TAB . TAB . "\$this->" . $field . " = \$query->fetchAll();\n";
  $str .= TAB . TAB . "}\n";
  $str .= TAB . TAB . "return \$this->" . $field . ";\n";
  $str .= TAB . "}\n\n";

  return $str;
}

/**
 * @param string $className
 * @return string
 */
function writeOverrideBaseFunctions($className)
{
  $str = '';

  //find
  $str .= TAB . "/**\n";
  $str .= TAB . " * @param int \$id\n";
  $str .= TAB . " * @return " . $className . "\n";
  $str .= TAB . " */\n";
  $str .= TAB . 'public static function find($id)' . "\n";
  $str .= TAB . "{\n";
  $str .= TAB . TAB . 'return parent::find($id);' . "\n";
  $str .= TAB . "}\n\n";

  //all
  $str .= TAB . "/**\n";
  $str .= TAB . " * @return " . $className . "[]\n";
  $str .= TAB . " */\n";
  $str .= TAB . 'public static function all()' . "\n";
  $str .= TAB . "{\n";
  $str .= TAB . TAB . 'return parent::all();' . "\n";
  $str .= TAB . "}\n";

  return $str;
}

/**
 * @param string $tableName
 * @param array $fields
 * @param array $manyToOneConstraints
 * @param array $oneToManyConstraints
 */
function createT_Model($tableName, $fields, Array $manyToOneConstraints, Array $oneToManyConstraints)
{
  $tableName    = strtolower($tableName);
  $class        = removeSFromTableName($tableName);
  $className    = "T_" . $class;
  $className[2] = strtoupper($className[2]);

  $file = fopen(THIS_T_MODEL_DIR . 't_' . $class . '.php', 'w');

  if ($file)
  {
    fwrite($file, DISCLAIMER);
    fwrite($file, 'class ' . $className . ' extends \Lib\Models\Deletable' . "\n{\n");

    fwrite($file, TAB . "protected static \$table_name = '" . $tableName . "';\n\n");

    foreach ($fields as $field)
    {
      if ($constrait = getForeignKey($manyToOneConstraints, $field, $tableName))
      {
        fwrite($file, writeField($field, false));
        fwrite($file, writeManyToOneJoin($field, $constrait['foreign_table_name'], $constrait['foreign_column_name']));
      }
      else
        fwrite($file, writeField($field));
    }

    if (array_key_exists($tableName, $oneToManyConstraints))
    {
      foreach ($oneToManyConstraints[$tableName] as $c)
      {
        fwrite($file, writeOneToManyJoin($c['table_name'], $c['column_name'], $c['foreign_table_name'], $c['foreign_column_name']));
      }
    }

    fwrite($file, writeOverrideBaseFunctions(substr($className, 2)));

    fwrite($file, "}\n");
    fclose($file);
  }
}

/**
 * @param string $name
 */
function createModel($name)
{
  $name         = strtolower($name);
  $className    = $name;
  $className[0] = strtoupper($className[0]);

  if (!file_exists(THIS_MODEL_DIR . $name . '.php'))
  {
    $file = fopen(THIS_MODEL_DIR . $name . '.php', 'w');

    if ($file)
    {
      fwrite($file, "<?php\n\n");
      fwrite($file, 'class ' . $className . ' extends T_' . $className . "\n{\n");
      fwrite($file, "\n}\n");

      fclose($file);
    }
  }
}

/**
 * @param \Lib\EPO $pdo
 * @param string $tableName
 */
function writeAllProcedure(\Lib\EPO &$pdo, $tableName)
{
  $procedureName = 'getall' . $tableName;

  $pdo->beginTransaction();

  $pdo->exec("CREATE OR REPLACE FUNCTION " . $procedureName . "()
  RETURNS SETOF " . $tableName . " AS
  'SELECT * FROM " . $tableName . "'
  LANGUAGE SQL VOLATILE
  COST 100;
  ALTER FUNCTION " . $procedureName . "()
  OWNER TO \"" . DBUSER . "\";");

  $pdo->commit();
}

/**
 * @param \Lib\EPO $pdo
 * @param string $tableName
 */
function writeFindProcedure(\Lib\EPO &$pdo, $tableName)
{
  $procedureName = 'get' . substr($tableName, 0, -1) . 'fromid';
  $parameter     = substr($tableName, 0, -1) . "_id";
  $alias         = $tableName[0];

  $pdo->beginTransaction();

  $pdo->exec("CREATE OR REPLACE FUNCTION " . $procedureName . "(" . $parameter . " numeric)
  RETURNS " . $tableName . " AS
  'SELECT * FROM " . $tableName . " " . $alias . " WHERE " . $alias . ".id = " . $parameter . "'
  LANGUAGE sql VOLATILE
  COST 100;
  ALTER FUNCTION " . $procedureName . "(numeric)
  OWNER TO \"" . DBUSER . "\";");

  $pdo->commit();
}

/**
 * @param \Lib\EPO $pdo
 * @param string $tableName
 */
function writeCountProcedure(\Lib\EPO &$pdo, $tableName)
{
  $procedureName = 'count' . $tableName;

  $pdo->beginTransaction();
  $pdo->exec("
  CREATE OR REPLACE FUNCTION count" . $tableName . "()
  RETURNS bigint AS
  'SELECT COUNT(id) FROM " . $tableName . "'
  LANGUAGE sql VOLATILE
  COST 100;
  ALTER FUNCTION count" . $tableName . "()
  OWNER TO \"" . DBUSER . "\";
  ");
  $pdo->commit();
}

/**
 * @param array $storedProcedures
 */
function createSP_Models(Array $storedProcedures)
{
  if (count($storedProcedures) > 0)
  {
    foreach ($storedProcedures as $sp)
    {
      if (preg_match(SPMODELMATCH, $sp['name']))
      {
        $trunks       = explode('_', $sp['name']);
        $fileName     = 'sp_' . $trunks[1] . '.php';
        $className    = 'SP_' . $trunks[1];
        $className[3] = strtoupper($className[3]);

        $file = fopen(THIS_SP_MODEL_DIR . $fileName, 'w');

        $buffer = '';

        $buffer .= DISCLAIMER;
        $buffer .= 'class ' . $className . " extends \\Lib\\Models\\Model\n{\n";

        $prototype = 'execute(';
        $query     = 'SELECT * FROM ' . $sp['name'] . '(';

        if (count($sp['parameters']) > 0)
        {
          foreach ($sp['parameters'] as $p)
          {
            $prototype .= '$' . $p['name'] . ', ';
            $query .= '\'.$' . $p['name'] . '.\', ';
          }
          $prototype = substr($prototype, 0, -2) . ')';
          $query     = substr($query, 0, -2) . ')';
        }
        else
        {
          $prototype .= ')';
          $query .= ')';
        }

        $buffer .= TAB . 'public static function ' . $prototype . "\n";
        $buffer .= TAB . "{\n";
        $buffer .= TAB . TAB . "\$pdo = \\Lib\\PDOS::getInstance();\n";
        $buffer .= TAB . TAB . "\$query = \$pdo->prepare('" . $query . "');\n";
        $buffer .= TAB . TAB . "\$query->execute();\n";
        $buffer .= TAB . TAB . "return \$query->fetchAll(PDO::FETCH_OBJ);\n";

        $buffer .= TAB . "}\n\n";

        $buffer .= "}\n";

        fwrite($file, $buffer);
        fclose($file);
      }
    }
  }
}

/**
 * SCRIPT BEGINS HERE
 */

writeLine("Connecting to " . DBNAME . " on " . DBHOST . "...");
$pdo = null;
try
{
  $pdo = \Lib\PDOS::getInstance();
} catch (Exception $e)
{
  writeLine("Error ! Connection to database failed.");
  exit(1);
}

writeLine("Success !");
writeLine("Retrieving database public schema...");

//Getting constraints
$query = $pdo->prepare("
SELECT
    tc.table_name, kcu.column_name,
    ccu.table_name AS foreign_table_name,
    ccu.column_name AS foreign_column_name
FROM
    information_schema.table_constraints AS tc
    JOIN information_schema.key_column_usage AS kcu ON tc.constraint_name = kcu.constraint_name
    JOIN information_schema.constraint_column_usage AS ccu ON ccu.constraint_name = tc.constraint_name
WHERE constraint_type = 'FOREIGN KEY';");

$query->execute();

$manyToOneConstraints = array();
$oneToManyConstraints = array();
foreach ($query->fetchAll() as $constraint)
{
  $manyToOneConstraints[$constraint['table_name']][]         = $constraint;
  $oneToManyConstraints[$constraint['foreign_table_name']][] = $constraint;
}

//Getting all fields of all tables from public schema
$query = $pdo->prepare("
SELECT table_name, column_name
FROM information_schema.columns
WHERE table_schema = 'public'
ORDER BY table_name");
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

//Getting all custom stored procedures (name like sp_*_*) and theirs parameters
$query            = $pdo->prepare("
SELECT
  r.routine_name,
  r.type_udt_name AS routine_return_type,
  p.ordinal_position AS parameter_position,
  p.parameter_name,
  p.data_type AS parameter_type,
  p.parameter_mode
FROM
  information_schema.routines r
  LEFT JOIN information_schema.parameters p ON p.specific_name = r.specific_name
WHERE
  r.specific_schema = 'public' AND
  r.routine_type = 'FUNCTION' AND
  r.routine_name LIKE 'sp_%'
ORDER BY
  r.routine_name, parameter_position
");
$storedProcedures = array();
$query->execute();

foreach ($query->fetchAll() as $param)
{
  if (!array_key_exists($param['routine_name'], $storedProcedures))
  {
    $storedProcedures[$param['routine_name']] = array(
      'name'        => $param['routine_name'],
      'return_type' => $param['routine_return_type'],
      'parameters'  => array()
    );
  }
  if (!is_null($param['parameter_position']) && $param['parameter_mode'] == 'IN')
  {
    $storedProcedures[$param['routine_name']]['parameters'][] = array(
      'name'     => $param['parameter_name'],
      'type'     => $param['parameter_type'],
      'position' => $param['parameter_position']
    );
  }
}

var_dump($storedProcedures);

writeLine(count($tables) . ' table' . (count($tables) > 1 ? 's' : '') . ' found');
writeLine("Generating models...");

//Foreach table, generates both T_Model and Model
foreach ($tables as $table)
{
  $fields            = $tables_fields[$table];
  $originalTableName = $table;

  //Retrieving the 's' add the end of the table name, if exists
  $table = removeSFromTableName($table);

  $className    = $table;
  $className[0] = strtoupper($className[0]);

  createT_Model($originalTableName, $fields, $manyToOneConstraints, $oneToManyConstraints);
  writeLine(' T_' . $className . ' model written');

  createModel($table);
  writeLine(' ' . $className . ' model written');

  writeAllProcedure($pdo, $originalTableName);
  writeLine(' getall' . $originalTableName . '() function written in database');

  writeFindProcedure($pdo, $originalTableName);
  writeLine(' get' . substr($originalTableName, 0, -1) . 'fromid() function written in database');

  writeCountProcedure($pdo, $originalTableName);
  writeLine(' count' . $originalTableName . '() function written in database');

  writeLine("");
}

createSP_Models($storedProcedures);

writeLine("Done !");
writeLine("Generation finished ! Enjoy ;)");

/**
 * SCRIPT ENDS HERE
 */