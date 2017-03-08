<?php
namespace Model;

class Invite extends \DB\Cortex {
    protected
        $fieldConf = array(
            'email' => array(
                'type' => 'VARCHAR256',
                'nullable' => false,
                'unique' => true
            ),
            'hash' => array(
                'type' => 'VARCHAR256',
                'nullable' => false,
                'unique' => true
            ),
            'state' => array(
                'type' => 'INT1',
                'default' => 0
            )
        ),
        $db = 'DB',
        $fluid = true,
        $table = 'invite';
}
