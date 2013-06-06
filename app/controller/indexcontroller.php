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
        $user = new User();
        $user->setLogin("mathieu");
        $user->save();


        $this->set("mavar", "cocorico");
        $this->render = "false";
    }
}
