<?php
/**
 * Created by JetBrains PhpStorm.
 * User: savy_m
 * Date: 24/05/13
 * Time: 15:59
 * To change this template use File | Settings | File Templates.
 */

class IndexController extends \Lib\Controller
{
    public function toArray($object)
    {
        $array = array();
        var_dump(get_object_vars($object));
        foreach(get_class_vars(get_class($object)) as $k => $v)
        {
            $array[$k] = $v;
        }
        return $array;
    }

    public function index($params = array())
    {

        /*
        $sql = \Lib\PDOS::getInstance();


        $toto = $sql->prepare("SELECT all_users3()");
        $toto->execute();

        //var_dump($toto->fetchAll(PDO::FETCH_COLUMN));


        $obj = array();
        foreach ($toto->fetchAll(PDO::FETCH_COLUMN) as $t)
        {
            $obj[] = json_decode($t);
        }

        var_dump($obj);

        var_dump($obj[0]->login);
*/
        $user = User::find(2);
        $user->save();


        $this->set("mavar", "cocorico");
    }
}
