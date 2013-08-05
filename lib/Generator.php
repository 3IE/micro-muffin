<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Mathieu
 * Date: 14/07/13
 * Time: 15:52
 * To change this template use File | Settings | File Templates.
 */

/**
 *
 * DO NOT EDIT THIS GENERATION SCRIPT
 * IT MAY BE OVERWRITEN BY UPDATING THE FRAMEWORK
 * ONLY EXECUTE IT WITH A TERMINAL
 *
 *
 * HOW TO WRITE CUSTOM STORED PROCEDURE WITH MODEL
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
 *
 * OR
 *
 * Create your stored procedure with OUT or INOUT parameters
 *
 * Naming convention : sp_NAME()
 */
namespace Lib;

class Generator
{
  /** @var array */
  private $tablesFields;

  /** @var array */
  private $MTOconstraints;

  /** @var array */
  private $OTMconstraints;

  /** @var array */
  private $tableId;

  /** @var array */
  private $sequences;

  /** @var array */
  private $primaryKeys;

  private function __construct()
  {
    $this->tablesFields   = array();
    $this->OTMconstraints = array();
    $this->MTOconstraints = array();
    $this->tableId        = array();
    $this->sequences      = array();
    $this->primaryKeys    = array();
  }

  private function init()
  {
    define('NOAUTOLOAD', true);

    require_once 'config.php';
    require_once '../lib/pdos.php';
    require_once '../lib/epo.php';
    require_once '../lib/config.php';

    define('THIS_MODEL_DIR', '../' . MODEL_DIR);
    define('THIS_T_MODEL_DIR', '../' . TMODEL_DIR);
    define('THIS_SP_MODEL_DIR', '../' . SPMODEL_DIR);
    define('DISCLAIMER', "<?php
/**
* WARNING !
* This is an auto-generated file. DO NOT EDIT IT !
* It will be overwritten during the next database import.
*/
\n");
    define('TAB', '  ');
    define('SPMODELMATCH', '#^sp_[a-zA-Z0-9_]+$#');
    define('RO_CHMOD', 0444);
    define('W_CHMOD', 0660);
  }


  /**
   * @param $var
   * @return bool
   */
  private function isTypeString($var)
  {
    $stringType = array(
      'character varying',
      'text'
    );

    return in_array($var, $stringType);
  }

  private function writeLine($str)
  {
    echo $str . "\n";
  }

  /**
   * @param array $constraints
   * @param string $column
   * @param string $table
   * @return array|null
   */
  private function getForeignKey(Array $constraints, $column, $table)
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
  private function removeSFromTableName($table)
  {
    if ($table[strlen($table) - 1] == 's')
      $table = substr($table, 0, -1);
    return $table;
  }

  /**
   * @param string $field
   * @param bool $visible
   * @param array $column_defaults
   * @return string
   */
  private function writeField($field, $visible = true, Array $column_defaults = array())
  {
    $fieldCapitalize    = $field;
    $fieldCapitalize[0] = strtoupper($fieldCapitalize[0]);
    $str                = TAB . 'protected' . ' $_' . $field . " = " . (is_null($column_defaults[$field]) ? "null" : $column_defaults[$field]) . ";\n\n";

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
  private function writeManyToOneJoin($field, $foreignTable, $foreignField)
  {
    $str          = '';
    $className    = $this->removeSFromTableName($foreignTable);
    $className[0] = strtoupper($className);
    $var          = $className;
    $var[0]       = strtolower($var[0]);

    //Object field
    $this_field_tab    = explode("_" . $foreignField, $field);
    $object_field      = $this_field_tab[0];
    $object_fieldUp    = $object_field;
    $object_fieldUp[0] = strtoupper($object_fieldUp[0]);
    $var               = $object_field;
    $str .= TAB . "/** @var " . $className . " */\n";
    $str .= TAB . 'protected $' . $var . " = null;\n\n";

    //Getter
    $str .= TAB . "/** @return " . $className . " */\n";
    $str .= TAB . "public function get" . $object_fieldUp . "()\n" . TAB . "{\n";
    $str .= TAB . TAB . 'if (is_null($this->' . $var . '))' . "\n";
    $str .= TAB . TAB . TAB . '$this->' . $var . ' = ' . $className . '::find($this->_' . $field . ');' . "\n";
    $str .= TAB . TAB . 'return $this->' . $var . ';' . "\n";
    $str .= TAB . "}\n\n";

    //Setter
    $foreignFieldUp    = $foreignField;
    $foreignFieldUp[0] = strtoupper($foreignFieldUp[0]);
    $str .= TAB . "/** @param " . $className . " \$" . $var . "*/\n";
    $str .= TAB . "public function set" . $object_fieldUp . "(\$" . $var . ")\n" . TAB . "{\n";
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
   * @param string $foreignColumnClean
   * @param string $tableName
   * @param string $columnType
   * @return string
   */
  private function writeOneToManyProcedure($foreignTable, $foreignColumn, $foreignColumnClean, $tableName, $columnType)
  {
    $procedureName = strtolower('otm_' . $foreignTable . 'from' . $this->removeSFromTableName($tableName) . '_' . $foreignColumnClean);
    $pdo           = PDOS::getInstance();

    $pdo->beginTransaction();
    $pdo->exec("
  CREATE OR REPLACE function " . $procedureName . "(foreign_column " . $columnType . ")
  RETURNS SETOF " . $foreignTable . " AS
  'SELECT * FROM " . $foreignTable . " WHERE " . $foreignColumn . " = \$1'
  LANGUAGE sql VOLATILE
  COST 100
  ROWS 1000;
  ALTER function " . $procedureName . "(" . $columnType . ")
  OWNER TO " . DBUSER . ";");
    $pdo->commit();

    $this->writeLine(' ' . $procedureName . '() written in database');

    return $procedureName;
  }

  /**
   * WARNING ! Foreign denomination is inverted compared with constraints query result
   *
   * @param string $foreignTableName
   * @param string $foreignColumnName
   * @param string $tableName
   * @param string $fieldName
   * @param string $columnType
   * @return string
   */
  private function writeOneToManyJoin($foreignTableName, $foreignColumnName, $tableName, $fieldName, $columnType)
  {
    $str = '';

    $joinColumn = $foreignColumnName;
    if (substr($joinColumn, strlen($joinColumn) - 3) == '_id')
      $joinColumn = substr($joinColumn, 0, -3);
    else if (substr($joinColumn, strlen($joinColumn) - 2 == 'Id'))
      $joinColumn = substr($joinColumn, 0, -2);
    $joinColumn    = strtolower($joinColumn);
    $joinColumn[0] = strtoupper($joinColumn[0]);

    $var             = $foreignTableName . 'From' . $joinColumn;
    $varUppered      = $var;
    $varUppered[0]   = strtoupper($varUppered[0]);
    $field           = $foreignTableName;
    $fieldUppered    = $field;
    $fieldUppered[0] = strtoupper($fieldUppered[0]);

    $procedure = $this->writeOneToManyProcedure($foreignTableName, $foreignColumnName, $joinColumn, $tableName, $columnType);

    $str .= TAB . '/** @var ' . $this->removeSFromTableName($fieldUppered) . '[] */' . "\n";
    $str .= TAB . 'protected $' . $var . " = null;\n\n";
    $str .= TAB . "/**\n";
    $str .= TAB . " * @return " . $this->removeSFromTableName($fieldUppered) . "[]\n";
    $str .= TAB . " */\n";
    $str .= TAB . 'public function get' . $varUppered . "()\n";
    $str .= TAB . "{\n";
    $str .= TAB . TAB . "if (is_null(\$this->" . $var . "))\n";
    $str .= TAB . TAB . "{\n";
    $str .= TAB . TAB . TAB . "\$pdo = \\Lib\\PDOS::getInstance();\n";
    $str .= TAB . TAB . TAB . "\$query = \$pdo->prepare('SELECT * FROM " . $procedure . "('.\$this->_" . $fieldName . ".')');\n";
    $str .= TAB . TAB . TAB . "\$query->execute();\n";
    $str .= TAB . TAB . TAB . "\$results = \$query->fetchAll();\n";
    $str .= TAB . TAB . TAB . "\$objs = array();\n";
    $str .= TAB . TAB . TAB . "foreach (\$results as \$r)\n";
    $str .= TAB . TAB . TAB . "{\n";
    $str .= TAB . TAB . TAB . TAB . "\$obj = new " . $this->removeSFromTableName($fieldUppered) . "();\n";
    $str .= TAB . TAB . TAB . TAB . "self::hydrate(\$obj, \$r);\n";
    $str .= TAB . TAB . TAB . TAB . "\$objs[] = \$obj;\n";
    $str .= TAB . TAB . TAB . "}\n";
    $str .= TAB . TAB . TAB . "\$this->" . $var . " = \$objs;\n";
    $str .= TAB . TAB . "}\n";
    $str .= TAB . TAB . "return \$this->" . $var . ";\n";
    $str .= TAB . "}\n\n";

    return $str;
  }

  /**
   * @param string $tableName
   * @return string
   */
  private function writeUpdateFunction($tableName)
  {
    $str = '';

    $str .= TAB . "protected function update()\n";
    $str .= TAB . "{\n";
    $str .= TAB . TAB . "\$sql        = 'UPDATE $tableName SET ';\n";
    $str .= TAB . TAB . "\$set        = '';\n";
    $str .= TAB . TAB . "\$where      = '';\n";
    $str .= TAB . TAB . "\$attributes = \$this->getAttributes(new \\ReflectionClass(\$this));\n";
    $str .= TAB . TAB . "foreach (\$attributes as \$k => \$v)\n";
    $str .= TAB . TAB . TAB . "if (!in_array(\$k, self::\$primary_keys))\n";
    $str .= TAB . TAB . TAB . TAB . "\$set .= \$k . ' = :' . \$k . ', ';\n";
    $str .= TAB . TAB . "foreach (self::\$primary_keys as \$pk)\n";
    $str .= TAB . TAB . TAB . "\$where .= \$pk . ' = :' . \$pk . ' AND ';\n";
    $str .= TAB . TAB . "\$where = substr(\$where, 0, -5);\n";
    $str .= TAB . TAB . "\$set = substr(\$set, 0, -2);\n";
    $str .= TAB . TAB . "\$sql .= \$set . ' WHERE ' . \$where;\n";
    $str .= TAB . TAB . "\$pdo = \\Lib\\PDOS::getInstance();\n";
    $str .= TAB . TAB . "\$pdo->beginTransaction();\n";
    $str .= TAB . TAB . "\$query = \$pdo->prepare(\$sql);\n";
    $str .= TAB . TAB . "foreach(\$attributes as \$k => \$v)\n";
    $str .= TAB . TAB . TAB . "\$query->bindValue(':' . \$k, \$v);\n";
    $str .= TAB . TAB . "\$query->execute();\n";
    $str .= TAB . TAB . "\$pdo->commit();\n";
    $str .= TAB . "}\n\n";

    return $str;
  }

  /**
   * @param $tableName
   * @param $primaryKeys
   * @return string
   */
  private function writeFindFunction($tableName, $primaryKeys)
  {
    $str          = '';
    $className    = $this->removeSFromTableName($tableName);
    $className[0] = strtoupper($className[0]);
    $params       = '';
    $proto        = '';
    $placeholder  = '';
    $checkNull    = '';

    foreach ($primaryKeys as $pk)
    {
      $params .= TAB . " * @param \$" . $pk['name'] . "\n";
      $proto .= "\$" . $pk['name'] . ", ";
      $placeholder .= ":" . $pk['name'] . ", ";
      $checkNull .= '!is_null($result[\'' . $pk['name'] . '\']) && ';
    }
    $proto       = substr($proto, 0, -2);
    $placeholder = substr($placeholder, 0, -2);
    $checkNull   = substr($checkNull, 0, -4);

    $procedureName = $this->writeFindProcedure($tableName, $primaryKeys);

    //find
    $str .= TAB . "/**\n";
    $str .= $params;
    $str .= TAB . " * @return " . $className . "\n";
    $str .= TAB . " */\n";
    $str .= TAB . 'public static function find(' . $proto . ')' . "\n";
    $str .= TAB . "{\n";
    $str .= TAB . TAB . '$pdo = \Lib\PDOS::getInstance();' . "\n";
    $str .= TAB . TAB . "\$query = \$pdo->prepare('SELECT * FROM $procedureName($placeholder)');\n";
    foreach ($primaryKeys as $pk)
    {
      $str .= TAB . TAB . "if (is_string(\$" . $pk['name'] . "))\n";
      $str .= TAB . TAB . TAB . "\$query->bindValue(':" . $pk['name'] . "', \$" . $pk['name'] . ", PDO::PARAM_STR);\n";
      $str .= TAB . TAB . "else\n";
      $str .= TAB . TAB . TAB . "\$query->bindValue(':" . $pk['name'] . "', \$" . $pk['name'] . ");\n";
    }
    $str .= TAB . TAB . "\$query->execute();\n";
    $str .= TAB . TAB . "\$result = \$query->fetch();\n";
    $str .= TAB . TAB . "if (!is_null(\$result) && $checkNull)\n";
    $str .= TAB . TAB . "{\n";
    $str .= TAB . TAB . TAB . "\$output_object = new $className();\n";
    $str .= TAB . TAB . TAB . "self::hydrate(\$output_object, \$result);\n";
    $str .= TAB . TAB . TAB . "return \$output_object;\n";
    $str .= TAB . TAB . "}\n";
    $str .= TAB . TAB . "else\n";
    $str .= TAB . TAB . TAB . " return null;\n";

    $str .= TAB . "}\n\n";

    return $str;
  }

  /**
   * @param string $className
   * @return string
   */
  private function writeOverrideAllFunctions($className)
  {
    $str = '';

    //all
    $str .= TAB . "/**\n";
    $str .= TAB . " * @param string \$order\n";
    $str .= TAB . " * @return " . $className . "[]\n";
    $str .= TAB . " */\n";
    $str .= TAB . 'public static function all($order = NULL)' . "\n";
    $str .= TAB . "{\n";
    $str .= TAB . TAB . 'return parent::all($order);' . "\n";
    $str .= TAB . "}\n";

    return $str;
  }

  /**
   * @param string $tableName
   * @param array $fields
   * @param array $manyToOneConstraints
   * @param array $oneToManyConstraints
   * @param array $sequences
   * @param array $column_defaults
   */
  private function createT_Model($tableName, $fields, Array $manyToOneConstraints, Array $oneToManyConstraints, Array $sequences, Array $column_defaults)
  {
    $originalTableName = $tableName;
    $tableName         = strtolower($tableName);
    $class             = $this->removeSFromTableName($tableName);
    $className         = "T_" . $class;
    $className[2]      = strtoupper($className[2]);
    $filename          = THIS_T_MODEL_DIR . 't_' . $class . '.php';
    $extendFrom        = '\Lib\Models\Deletable';

    $file = fopen($filename, 'w');

    if ($file)
    {
      fwrite($file, DISCLAIMER);
      fwrite($file, 'class ' . $className . ' extends ' . $extendFrom . "\n{\n");

      fwrite($file, TAB . "protected static \$table_name = '" . $tableName . "';\n");
      if ($this->haveSequence($originalTableName))
        fwrite($file, TAB . "protected static \$sequence_name = '" . $sequences[$tableName] . "';\n");

      $primary_keys = "protected static \$primary_keys = array(";
      foreach ($this->primaryKeys[$originalTableName] as $pk)
        $primary_keys .= "'" . $pk['name'] . "', ";
      $primary_keys = substr($primary_keys, 0, -2) . ');';
      fwrite($file, TAB . $primary_keys . "\n");

      fwrite($file, "\n");

      foreach ($fields as $field)
      {
        if ($constrait = $this->getForeignKey($manyToOneConstraints, $field, $tableName))
        {
          fwrite($file, $this->writeField($field, false, $column_defaults[$originalTableName]));
          fwrite($file, $this->writeManyToOneJoin($field, $constrait['foreign_table_name'], $constrait['foreign_column_name']));
        }
        else
          fwrite($file, $this->writeField($field, true, $column_defaults[$originalTableName]));
      }

      if (array_key_exists($tableName, $oneToManyConstraints))
      {
        foreach ($oneToManyConstraints[$tableName] as $c)
        {
          fwrite($file, $this->writeOneToManyJoin($c['table_name'], $c['column_name'], $c['foreign_table_name'], $c['foreign_column_name'], $c['foreign_column_type']));
        }
      }

      fwrite($file, $this->writeFindFunction($originalTableName, $this->primaryKeys[$originalTableName]));
      fwrite($file, $this->writeUpdateFunction($originalTableName));
      fwrite($file, $this->writeOverrideAllFunctions(substr($className, 2)));

      fwrite($file, "}\n");
      fclose($file);

      chmod($filename, RO_CHMOD);
    }
  }

  /**
   * @param string $name
   */
  private function createModel($name)
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
  private function writeAllProcedure(EPO &$pdo, $tableName)
  {
    $procedureName = 'getall' . $tableName;

    $pdo->beginTransaction();

    $pdo->exec("CREATE OR REPLACE function " . $procedureName . "()
  RETURNS SETOF " . $tableName . " AS
  'SELECT * FROM " . $tableName . "'
  LANGUAGE SQL VOLATILE
  COST 100;
  ALTER function " . $procedureName . "()
  OWNER TO \"" . DBUSER . "\";");

    $pdo->commit();
  }

  /**
   * @param string $tableName
   * @param $primaryKeys
   * @return string
   */
  private function writeFindProcedure($tableName, $primaryKeys)
  {
    $procedureName = 'find' . $tableName;
    //$parameter     = substr($tableName, 0, -1) . "_id";
    $alias = $tableName[0];
    $pdo   = PDOS::getInstance();

    $proto     = '';
    $where     = '';
    $signature = '';
    $count     = 1;
    foreach ($primaryKeys as $pk)
    {
      $proto .= $pk['name'] . ' ' . $pk['type'] . ', ';
      $where .= $alias . "." . $pk['name'] . ' = $' . $count++ . ' AND ';
      $signature .= $pk['type'] . ', ';
    }
    $proto     = substr($proto, 0, -2);
    $where     = substr($where, 0, -5);
    $signature = substr($signature, 0, -2);

    $pdo->beginTransaction();

    $pdo->exec("CREATE OR REPLACE function " . $procedureName . "(" . $proto . ")
    RETURNS " . $tableName . " AS
    'SELECT * FROM " . $tableName . " " . $alias . " WHERE " . $where . "'
    LANGUAGE sql VOLATILE
    COST 100;
    ALTER function " . $procedureName . "(" . $signature . ")
    OWNER TO \"" . DBUSER . "\";");

    $pdo->commit();

    return $procedureName;
  }

  /**
   * @param \Lib\EPO $pdo
   * @param string $tableName
   */
  private function writeCountProcedure(EPO &$pdo, $tableName)
  {
    $procedureName = 'count' . $tableName;

    $pdo->beginTransaction();
    $pdo->exec("
  CREATE OR REPLACE function " . $procedureName . "()
  RETURNS bigint AS
  'SELECT COUNT(*) FROM " . $tableName . "'
  LANGUAGE sql VOLATILE
  COST 100;
  ALTER function count" . $tableName . "()
  OWNER TO \"" . DBUSER . "\";
  ");
    $pdo->commit();
  }

  private function writeSP_record(Array $sp)
  {
    $name         = substr($sp['name'], 3);
    $fileName     = 'sp_' . $name . '.php';
    $className    = 'SP_' . $name;
    $className[3] = strtoupper($className[3]);
    $filepath     = THIS_SP_MODEL_DIR . $fileName;

    $file = fopen($filepath, 'w');

    if ($file)
    {

      $buffer = '';

      $buffer .= DISCLAIMER;
      $buffer .= 'class ' . $className . " extends \\Lib\\Models\\Model\n{\n";

      //Writing class fields (OUT parameters)
      if (count($sp['parameters']) > 0)
      {
        foreach ($sp['parameters'] as $p)
        {
          if ($p['mode'] == 'OUT' || $p['mode'] == 'INOUT')
          {
            $nameUppered    = $p['name'];
            $nameUppered[0] = strtoupper($nameUppered[0]);

            //Field
            $buffer .= TAB . "protected \$" . $p['name'] . ";\n\n";

            //Getter
            $buffer .= TAB . "public function get" . $nameUppered . "()\n";
            $buffer .= TAB . "{\n";
            $buffer .= TAB . TAB . "return \$this->" . $p['name'] . ";\n";
            $buffer .= TAB . "}\n\n";

            //Setter
            $buffer .= TAB . "protected function set" . $nameUppered . "(\$" . $p['name'] . ")\n";
            $buffer .= TAB . "{\n";
            $buffer .= TAB . TAB . "\$this->" . $p['name'] . " = \$" . $p['name'] . ";\n";
            $buffer .= TAB . "}\n\n";
          }
        }
      }

      $prototype = 'execute(';
      $query     = 'SELECT * FROM ' . $sp['name'] . '(';

      if (count($sp['parameters']) > 0)
      {
        $in_params = false;
        foreach ($sp['parameters'] as $p)
        {
          if ($p['mode'] == 'IN' || $p['mode'] == 'INOUT')
          {
            $in_params = true;
            $prototype .= '$' . $p['name'] . ', ';
            if ($this->isTypeString($p['type']))
              $query .= '\\\'\'.$' . $p['name'] . '.\'\\\', ';
            else
              $query .= '\'.$' . $p['name'] . '.\', ';
          }
        }
        if ($in_params)
        {
          $prototype = substr($prototype, 0, -2) . ')';
          $query     = substr($query, 0, -2) . ')';
        }
        else
        {
          $prototype .= ')';
          $query .= ')';
        }
      }
      else
      {
        $prototype .= ')';
        $query .= ')';
      }

      //Execute function
      $buffer .= TAB . "/**\n";
      if ($sp['return_type'] != 'record')
        $buffer .= TAB . " * @return array\n";
      else
        $buffer .= TAB . " * @return " . $className . "[]\n";
      $buffer .= TAB . " */\n";
      $buffer .= TAB . 'public static function ' . $prototype . "\n";
      $buffer .= TAB . "{\n";
      $buffer .= TAB . TAB . "\$pdo = \\Lib\\PDOS::getInstance();\n";
      $buffer .= TAB . TAB . "\$query = \$pdo->prepare('" . $query . "');\n";
      $buffer .= TAB . TAB . "\$query->execute();\n";
      $buffer .= TAB . TAB . "\$res = array();\n";

      if ($sp['return_type'] == 'record')
      {
        $buffer .= TAB . TAB . "foreach(\$query->fetchAll() as \$v)\n";
        $buffer .= TAB . TAB . "{\n";
        $buffer .= TAB . TAB . TAB . "\$obj = new " . $className . "();\n";
        $buffer .= TAB . TAB . TAB . "self::hydrate(\$obj, \$v);\n";
        $buffer .= TAB . TAB . TAB . "\$res[] = \$obj;\n";
        $buffer .= TAB . TAB . "}\n";
      }
      else
      {
        $buffer .= TAB . TAB . "foreach(\$query->fetchAll(PDO::FETCH_COLUMN) as \$v)\n";
        $buffer .= TAB . TAB . TAB . "\$res[] = \$v;\n";
      }

      $buffer .= TAB . TAB . "return \$res;\n";
      $buffer .= TAB . "}\n\n";

      $buffer .= "}\n";

      fwrite($file, $buffer);
      fclose($file);

      chmod($filepath, RO_CHMOD);
      $this->writeLine(" " . $className . " model written");
    }
  }

  /**
   * @param array $storedProcedures
   */
  private function createSP_Models(Array $storedProcedures)
  {
    if (count($storedProcedures) > 0)
    {
      foreach ($storedProcedures as $sp)
      {
        if (preg_match(SPMODELMATCH, $sp['name']))
        {
          $this->writeSP_record($sp);
        }
      }
    }
  }

  private function emptyDirectory($dirName)
  {
    /** @var $file \DirectoryIterator */
    foreach (new \DirectoryIterator($dirName) as $file)
    {
      if (!$file->isDot() && $file->getFilename() != 'empty')
      {
        chmod($file->getPathname(), W_CHMOD);
        unlink($file->getPathname());
      }
    }
  }

  private function haveId($table)
  {
    return in_array($table, $this->tableId);
  }

  private function haveSequence($table)
  {
    return array_key_exists($table, $this->sequences);
  }

  private function execute()
  {
    //Empting t_model and sp_model directories
    $this->emptyDirectory(substr(THIS_T_MODEL_DIR, 0, -1));
    $this->writeLine("Emptying t_model directory");

    $this->emptyDirectory(substr(THIS_SP_MODEL_DIR, 0, -1));
    $this->writeLine("Emptying sp_model directory");

    $this->writeLine("Connecting to " . DBNAME . " on " . DBHOST . "...");
    $pdo = null;
    try
    {
      $pdo = PDOS::getInstance();
    } catch (\Exception $e)
    {
      $this->writeLine("Error ! Connection to database failed.");
      exit(1);
    }

    $this->writeLine("Success !");
    $this->writeLine("Retrieving database " . DBSCHEMA . " schema...");

//Getting foreign keys
    $query = $pdo->prepare("
    SELECT
        tc.table_name,
        kcu.column_name,
        ccu.table_name AS foreign_table_name,
        ccu.column_name AS foreign_column_name,
        c.data_type AS foreign_column_type
    FROM
        information_schema.table_constraints AS tc
        JOIN information_schema.key_column_usage AS kcu ON tc.constraint_name = kcu.constraint_name
        JOIN information_schema.constraint_column_usage AS ccu ON ccu.constraint_name = tc.constraint_name
        JOIN information_schema.columns AS c ON c.table_name = ccu.table_name AND c.column_name = ccu.column_name
    WHERE constraint_type = 'FOREIGN KEY';");

    $query->execute();

    $manyToOneConstraints = array();
    $oneToManyConstraints = array();
    foreach ($query->fetchAll() as $constraint)
    {
      $manyToOneConstraints[$constraint['table_name']][]         = $constraint;
      $oneToManyConstraints[$constraint['foreign_table_name']][] = $constraint;
    }

    $this->OTMconstraints = $oneToManyConstraints;
    $this->MTOconstraints = $manyToOneConstraints;

    //Getting primary keys
    $query = $pdo->prepare("
    SELECT
        tc.table_name,
        ccu.column_name,
        c.data_type
    FROM
        information_schema.table_constraints AS tc
        INNER JOIN information_schema.constraint_column_usage AS ccu ON tc.constraint_name = ccu.constraint_name
        INNER JOIN information_schema.columns AS c ON c.column_name = ccu.column_name AND c.table_name = tc.table_name
    WHERE tc.constraint_type = 'PRIMARY KEY' AND tc.constraint_schema = '" . DBSCHEMA . "'");
    $query->execute();

    $primaryKeys = array();
    foreach ($query->fetchAll() as $pk)
    {
      if (!array_key_exists($pk['table_name'], $primaryKeys))
        $primaryKeys[$pk['table_name']] = array();
      $primaryKeys[$pk['table_name']][] = array('name' => $pk['column_name'], "type" => $pk['data_type']);
    }
    $this->primaryKeys = $primaryKeys;

    //Getting all fields of all tables from public schema
    $query = $pdo->prepare("
    SELECT
      table_name,
      column_name,
      column_default,
      pg_get_serial_sequence(table_name, column_name) AS sequence_name
    FROM
      information_schema.columns
    WHERE
      table_schema = '" . DBSCHEMA . "'
    ORDER BY
      table_name");
    $query->execute();

    $column_defaults = array();
    $sequences       = array();
    $tables_fields   = array();
    $tables          = array();
    $fields          = $query->fetchAll();

    foreach ($fields as $field)
    {
      if (!in_array($field['table_name'], $tables))
      {
        $tables[]                              = $field['table_name'];
        $column_defaults[$field['table_name']] = array();
      }
      if ($field['column_name'] != 'id')
      {
        $tables_fields[$field['table_name']][]                        = $field['column_name'];
        $column_defaults[$field['table_name']][$field['column_name']] = is_null($field['sequence_name']) ? $field['column_default'] : null;
      }
      else
        $this->tableId[] = $field['table_name'];
      if (!is_null($field['sequence_name']))
        $sequences[$field['table_name']] = explode(DBSCHEMA . '.', $field['sequence_name'])[1];
    }

    $this->sequences    = $sequences;
    $this->tablesFields = $tables_fields;
    unset($fields);
    unset($field);

//Getting all custom stored procedures (name like sp_*) and theirs parameters
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
      r.specific_schema = '" . DBSCHEMA . "' AND
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
      if (!is_null($param['parameter_position']))
      {
        $storedProcedures[$param['routine_name']]['parameters'][] = array(
          'name'     => $param['parameter_name'],
          'type'     => $param['parameter_type'],
          'mode'     => $param['parameter_mode'],
          'position' => $param['parameter_position']
        );
      }
    }

    $this->writeLine(count($tables) . ' table' . (count($tables) > 1 ? 's' : '') . ' found');
    $this->writeLine("Generating models...");

    //Foreach table, generates both T_Model and Model
    foreach ($tables as $table)
    {
      $fields            = $tables_fields[$table];
      $originalTableName = $table;

      //Retrieving the 's' add the end of the table name, if exists
      $table = $this->removeSFromTableName($table);

      $className    = $table;
      $className[0] = strtoupper($className[0]);

      $this->createT_Model($originalTableName, $fields, $manyToOneConstraints, $oneToManyConstraints, $sequences, $column_defaults);
      $this->writeLine(' T_' . $className . ' model written');

      $this->createModel($table);
      $this->writeLine(' ' . $className . ' model written');

      $this->writeAllProcedure($pdo, $originalTableName);
      $this->writeLine(' getall' . $originalTableName . '() function written in database');

      /*
      if ($this->haveId($originalTableName))
      {
        $this->writeFindProcedure($pdo, $originalTableName);
        $this->writeLine(' get' . substr($originalTableName, 0, -1) . 'fromid() function written in database');
      }
      */
      $this->writeCountProcedure($pdo, $originalTableName);
      $this->writeLine(' count' . $originalTableName . '() function written in database');

      $this->writeLine("");
    }

    $this->createSP_Models($storedProcedures);

    $this->writeLine("Done !");
    $this->writeLine("Generation finished ! Enjoy ;)");
  }

  public static function run()
  {
    $generator = new Generator();
    $generator->init();
    $generator->execute();
  }
}