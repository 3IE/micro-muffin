<?php
/**
 * User: mathieu.savy
 * Date: 25/05/13
 * Time: 16:41
 */

namespace Lib;


abstract class Model
{
    /** @var string|null */
    protected static $procstock_find = null;
    /** @var string|null */
    protected static $procstock_all = null;
    /** @var string|null */
    protected static $table_name = null;

    /** @var  int */
    private $_id = 0;

    /**
     * @param int $id
     */
    private function setId($id)
    {
        $this->_id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param Model $object
     * @param $data
     */
    private static function hydrate(Model &$object, $data)
    {
        foreach (get_object_vars($data) as $k => $v)
        {
            $k[0] = strtoupper($k[0]);
            $method = "set" . $k;
            $object->$method($v);
        }
    }

    /**
     * call stored procedure getCLASSfromid(numeric)
     * @param int $id
     * @return object
     */
    public static function find($id)
    {
        $class = get_called_class();
        $classLowered = strtolower($class);

        $stored_procedure = self::$procstock_find != null ? self::$procstock_find : 'get' . $classLowered . 'fromid';

        $pdo = PDOS::getInstance();
        $req = $pdo->prepare('SELECT ' . $stored_procedure . '(:id)');
        $req->bindValue(':id', $id, \PDO::PARAM_INT);
        $req->execute();

        $json_object = json_decode($req->fetch(\PDO::FETCH_COLUMN));
        $output_object = new $class();

        self::hydrate($output_object, $json_object);

        return $output_object;
    }

    /**
     * @param \ReflectionClass $r
     * @return array
     */
    private function getAttributes(\ReflectionClass $r)
    {
        $attributes = array();
        $class = $r->getShortName();
        $attributes['id'] = $this->_id;
        foreach ($r->getProperties() as $att)
        {
            if ($att->class == $class)
            {
                $name = $att->name;
                if ($name[0] == "_")
                {
                    $property = $r->getProperty($name);
                    $property->setAccessible(true);
                    $attributes[substr($name, 1)] = $property->getValue($this);
                    $property->setAccessible(false);
                }
            }
        }
        return $attributes;
    }

    /**
     * @return string JSON object
     */
    public function toJson()
    {
        $reflection = new \ReflectionClass($this);
        $attributes = $this->getAttributes($reflection);
        return json_encode($attributes);
    }

    public function save()
    {
        $reflection = new \ReflectionClass($this);
        $class = $reflection->getShortName();
        $table = self::$table_name != null ? self::$table_name : strtolower($class) . 's';

        $attributes = $this->getAttributes($reflection);

        $fields = '(';
        $values = '(';
        foreach ($attributes as $k => $v)
        {
            if ($k != 'id')
            {
                $fields .= $k . ', ';
                $values .= ':' . $k . ', ';
            }
        }
        $fields = substr($fields, 0, -2) . ')';
        $values = substr($values, 0, -2) . ')';

        $pdo = PDOS::getInstance();

        $pdo->beginTransaction();
        if ($this->_id == 0)
            $this->add($pdo, $table, $fields, $values, $attributes);
        else
            $this->update($pdo, $table, $attributes);
        $pdo->commit();
    }

    /**
     * @param \Lib\EPO $pdo
     * @param $table
     * @param string $fields
     * @param string $values
     * @param array $attributes
     */
    private function add(EPO &$pdo, $table, $fields, $values, Array $attributes)
    {
        $query = 'INSERT INTO ' . $table . ' ' . $fields . ' VALUES ' . $values;

        $query = $pdo->prepare($query);
        foreach ($attributes as $k => $v)
        {
            if ($k != 'id')
                $query->bindValue(':' . $k, $v);
        }
        $query->execute();
        $this->setId($pdo->lastInsertId());
    }

    /**
     * @param EPO $pdo
     * @param $table
     * @param array $attributes
     */
    private function update(EPO &$pdo, $table, Array $attributes)
    {
        $sql = 'UPDATE ' . $table . ' SET ';

        $set = '';
        foreach ($attributes as $k => $v)
        {
            if ($k != 'id')
                $set .= $k . ' = :' . $k . ', ';
        }
        $set = substr($set, 0, -2);
        $sql .= $set . ' WHERE id = :id';

        $query = $pdo->prepare($sql);
        foreach ($attributes as $k => $v)
        {
            $query->bindValue(':' . $k, $v);
        }
        $query->execute();
    }

    /**
     * @return Model[]
     */
    public static function all()
    {
        $class = strtolower(get_called_class());
        $proc = self::$procstock_all != null ? self::$procstock_all : $class . 's';
        $pdo = PDOS::getInstance();

        $query = $pdo->prepare('SELECT getall' . $proc . '()');
        $query->execute();

        $datas = $query->fetchAll(\PDO::FETCH_COLUMN);

        $outputs = array();
        foreach ($datas as $d)
        {
            $json_object = json_decode($d);
            $object = new $class();
            self::hydrate($object, $json_object);
            $outputs[] = $object;
        }
        return $outputs;
    }

    public static function delete($id)
    {
        $class = strtolower(get_called_class());
        $table = self::$table_name != null ? self::$table_name : $class . 's';

        $pdo = PDOS::getInstance();

        $query = $pdo->prepare('DELETE FROM ' . $table . ' WHERE id = :id');
        $query->bindParam(':id', $id, \PDO::PARAM_INT);
        $query->execute();
    }
}