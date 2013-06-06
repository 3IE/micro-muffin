<?php
/**
 * Created by JetBrains PhpStorm.
 * User: savy_m
 * Date: 24/05/13
 * Time: 16:22
 * To change this template use File | Settings | File Templates.
 */

namespace Lib;

class EPO extends \PDO
{
    public $num_queries = 0;

    /**
     * @param $dsn
     * @param null $username
     * @param null $password
     * @param array $driver_options
     */
    public function __construct($dsn, $username = null, $password = null, array $driver_options = array())
    {
        parent::__construct($dsn, $username, $password, $driver_options);
    }

    /**
     * @param string $statement
     * @return int
     */
    public function exec($statement)
    {
        PDOS::incNbQuery();
        return parent::exec($statement);
    }

    /**
     * @param string $statement
     * @return \PDOStatement
     */
    public function query($statement)
    {
        PDOS::incNbQuery();
        return parent::query($statement);
    }
}

class EPOStatement extends \PDOStatement
{
    protected $epo;

    protected function __construct(EPO $epo)
    {
        $this->epo = $epo;
    }

    /**
     * @param array $input_parameters
     * @return bool
     */
    public function execute($input_parameters = array())
    {
        PDOS::incNbQuery();
        if ($input_parameters == array())
            return parent::execute();
        else
            return parent::execute($input_parameters);
    }
}