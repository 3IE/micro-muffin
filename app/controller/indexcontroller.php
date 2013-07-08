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
    public function index($params = array())
    {
        $t = array();
        $t['name'] = 'b';
        $t['toto'] = 'bubububu';
        $t['int'] = 'l';
        $t['intreal'] = -856856;
        $t['mail'] = 'toto@toto.com';
        $t['mails'] = 'toto@totocom';
        $t['url'] = 'toto.com';
        $t['url2'] = 'www.toto.com';
        $t['url3'] = 'https://www.toto.com';
        $t['match'] = '2';

        $v = new \Lib\Form\FormValidator($t);
        $v->addRule('name', array('min:1', 'max:10'));
        $v->addRule('toto', array('min:2', 'max:4'));
        $v->addRule('int', array('numeric'));
        $v->addRule('intreal', array('numeric', ''));
        $v->addRule('mail', array('mail'));
        $v->addRule('mails', array('mail', 'max:10'));
        $v->addRule('url', array('url'));
        $v->addRule('url2', array('url'));
        $v->addRule('url3', array('url'));
        $v->addRule('match', array('match:name'));
        $v->addRule('gerger', array('required'));

        var_dump($v->check());
        var_dump($v->getMessages());
    }
}
