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
    protected static $find_stored_procedure = null;

    /**
     * @param \Lib\Model $object $object
     * @param object $data
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

        $stored_procedure = self::$find_stored_procedure != null ? self::$find_stored_procedure : 'get' . $classLowered . 'fromid';

        $pdo = PDOS::getInstance();
        $req = $pdo->prepare('SELECT ' . $stored_procedure . '(:id)');
        $req->bindValue(':id', $id, \PDO::PARAM_INT);
        $req->execute();

        $json_object = json_decode($req->fetch(\PDO::FETCH_COLUMN));
        $output_object = new $class();

        self::hydrate($output_object, $json_object);

        return $output_object;
    }

    private function getAttributes(\ReflectionClass $r)
    {
        $attributes = array();
        $class = $r->getShortName();
        foreach ($r->getProperties() as $att)
        {
            if ($att->class == $class)
            {
                $name = $att->name;
                $property = $r->getProperty($name);
                $property->setAccessible(true);
                $attributes[$name] = $property->getValue($this);
                $property->setAccessible(false);
            }
        }
        return $attributes;
    }

    public function toJson()
    {
        $reflection = new \ReflectionClass($this);
        $attributes = $this->getAttributes($reflection);
        return json_encode($attributes);
    }

    public function save()
    {
        //$reflection = new \ReflectionClass($this);
        //$class = $reflection->getShortName();
        //$classLowered = strtolower($class);
        //$attributes = $this->getAttributes($reflection);
        //var_dump(json_encode($attributes));
        var_dump($this->toJson());

    }
}