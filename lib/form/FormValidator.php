<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mathieu
 * Date: 7/8/13
 * Time: 10:41 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Lib\Form;

class FormValidator
{
    /** @var array */
    private $source;

    /** @var Rule[] */
    private $rules;

    /** @var array */
    private $messages;

    /**
     * @param array $source
     */
    public function FormValidator(Array $source)
    {
        $this->source = $source;
    }

    /**
     * @param string $name
     * @param array $constraints
     */
    public function addRule($name, Array $constraints)
    {
        $this->rules[] = new Rule($name, $constraints);
    }

    /**
     * @return bool
     */
    public function check()
    {
        $pass = true;
        foreach ($this->rules as $rule)
        {
            $ret = $rule->check($this->messages);
            if (!is_bool($ret) || !$ret)
                $pass = false;
        }
        return $pass;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    public function example()
    {
        $validator = new FormValidator($_POST);
        $validator->addRule('login', array('min:3', 'max:255', 'required'));

        if ($validator->check())
        {

        }
        else
        {
            $errors = $validator->getMessages();
        }
    }
}