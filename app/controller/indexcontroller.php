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
        foreach (get_class_vars(get_class($object)) as $k => $v)
        {
            $array[$k] = $v;
        }
        return $array;
    }

    public function index($params = array())
    {
        $user = User::find(2);
        $user->save();

        /*
        $pdo = \Lib\PDOS::getInstance();
        $req = $pdo->prepare('SELECT * FROM users');
        $req->execute();

        var_dump($req->fetchAll());

        $req = $pdo->prepare("SELECT getusers()");
        $req->execute();

        var_dump($req->fetchAll());
        */

        $this->set("mavar", "cocorico");
    }
}
