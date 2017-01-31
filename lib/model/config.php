<?php
namespace Model;

class Config extends \DB\Cortex {
    protected
        $fieldConf = array(
            'key' => array(
                'type' => 'VARCHAR256',
                'nullable' => false
            ),
            'value' => array(
                'type' => 'TEXT',
                'nullable' => false
            )
        ),
        $db = 'DB',
        $fluid = true,
        $table = 'config';

    /***
     * Config is a special model, since it allows
     * to retrieve any value in an easy and fast way
     * if you know the key.
     */
    static function read($key) {
        $conf = new \Model\Config();
        $conf->load(array("key = ?", $key));
        if($conf->dry()) {
            return false;
        } else {
            return $conf->value;
        }
    }

    /***
     * Similar, but for storing
     */
    static function store($key, $value) {
        $conf = new \Model\Config();
        $conf->load(array("key = ?", $key));
        if($conf->dry()) {
            $conf->key = $key;
            $conf->value = $value;
        } else {
            $conf->value = $value;
        }
        $conf->save();
    }
}
