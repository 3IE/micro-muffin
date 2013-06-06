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
        /** @var User $user */
        //$user = User::find(6);
        //var_dump($user);
        //$user->setLogin("'élu'");
        $user = new User();
        $user->setLogin("mathieu");
        $user->save();

        //var_dump(User::)

        /*
                $pdo = \Lib\PDOS::getInstance();
                $req = $pdo->prepare('SELECT * FROM users');
                $req->execute();

                var_dump($req->fetchAll());

                //$req = $pdo->prepare("SELECT getusers()");
                //$req->execute();

                //var_dump($req->fetchAll());

                pg_connect('host=localhost port=5432 dbname=micro-muffin user=micro-muffin password=root');
                $req = pg_query("SELECT * FROM getusers()");
                var_dump(pg_fetch_all($req));
        */

        $this->set("mavar", "cocorico");
        $this->render = "false";
    }
}
