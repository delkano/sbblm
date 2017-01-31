<?php
namespace Model;

class Coach extends \DB\Cortex {
    protected
        $fieldConf = array(
            'username' => array(
                'type' => 'VARCHAR256',
                'nullable' => false
            ),
            'password' => array(
                'type' => 'VARCHAR256',
                'nullable' => false
            ),
            'name' => array(
                'type' => 'VARCHAR256',
                'nullable' => false
            ),
            'avatar' => array(
                'type' => 'VARCHAR256',
                'nullable' => true
            ),
            'email' => array(
                'type' => 'VARCHAR256',
                'nullable' => false
            ),
            'teams' => array(
                'has-many' => array('\Model\Team', 'coach')
            ),
            'manager' => array(
                'type' => 'BOOLEAN',
                'default' => 0
            )
        ),
        $db = 'DB',
        $fluid = true,
        $table = 'coach';
}

