<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mathieu
 * Date: 7/8/13
 * Time: 10:49 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Lib\Form;


class Rule
{
    /** @var array */
    private $source;

    /** @var string */
    private $name;

    /** @var array */
    private $constraints;

    /** @var mixed */
    private $var;

    /**
     * @param array $source
     * @param string $name
     * @param array $constraints
     */
    public function Rule(Array $source, $name, Array $constraints)
    {
        $this->source = $source;
        $this->name = $name;
        $this->constraints = $constraints;
        $this->var = array_key_exists($this->name, $this->source) ? $this->source[$this->name] : null;
    }

    /**
     * @param array $messages
     * @return bool
     */
    public function check(Array &$messages)
    {
        $pass = true;
        $messages[$this->name] = array();
        foreach ($this->constraints as $constraint)
        {
            $chunks = explode(':', $constraint);
            $function = $chunks[0];
            $parameter = count($chunks) > 1 ? $chunks[1] : null;

            if (method_exists($this, $function))
            {
                //Check if there is a parameter
                if (!is_null($parameter))
                    $ret = $this->$function($parameter);
                else
                    $ret = $this->$function();

                if (!is_bool($ret) || !$ret)
                {
                    $pass = false;
                    $messages[$this->name][] = $ret;
                }
            }
        }
        return $pass;
    }

    /**
     * Checking methods
     */

    /**
     * @return bool|string
     */
    private function required()
    {
        if (array_key_exists($this->name, $this->source))
            return true;
        else
            return "Champ obligatoire";
    }

    /**
     * @param int $n
     * @return bool|string
     */
    private function min($n)
    {
        if (strlen($this->var) >= $n)
            return true;
        else
            return "Champ trop court, " . $n . " charactères minimun";
    }

    /**
     * @param int $n
     * @return bool|string
     */
    private function max($n)
    {
        if (strlen($this->var) <= $n)
            return true;
        else
            return "Champ trop long, " . $n . " charactères maximum";
    }

    /**
     * @param $filter
     * @return bool
     */
    private function checkFilter($filter)
    {
        $ret = filter_var($this->var, $filter);

        if (is_bool($ret) && $ret === false)
            return false;
        else
            return true;
    }

    /**
     * @return bool|string
     */
    private function mail()
    {
        if ($this->checkFilter(FILTER_VALIDATE_EMAIL))
            return true;
        else
            return "Adresse mail invalide";
    }

    /**
     * @return bool|string
     */
    private function url()
    {
        if ($this->checkFilter(FILTER_VALIDATE_URL))
            return true;
        else
            return "URL invalide";
    }

    /**
     * @return bool|string
     */
    private function numeric()
    {
        if (is_numeric($this->var))
            return true;
        else
            return "Nombre invalide";
    }

    /**
     * @param $field
     * @return bool|string
     */
    private function match($field)
    {
        if (array_key_exists($field, $this->source) && $this->source[$field] == $this->var)
            return true;
        else
            return "Ne correspond pas à " . $field;
    }
}