<?php
namespace Model;

class Game extends \DB\Cortex {
    protected
        $fieldConf = array(
            'date' => array(
                'type' => 'DATE',
                'nullable' => false
            ),
            'season' => array(
                'belongs-to-one' => '\Model\Season'
            ),
            'local' => array(
                'belongs-to-one' => '\Model\Team'
            ),
            'visitor' => array(
                'belongs-to-one' => '\Model\Team'
            ),
            'localResult' => array(
                'type' => 'INT1',
                'nullable' => false
            ),
            'visitorResult' => array(
                'type' => 'INT1',
                'nullable' => false
            ),
            'localCasualties' => array(
                'type' => 'INT1',
                'nullable' => false
            ),
            'visitorCasualties' => array(
                'type' => 'INT1',
                'nullable' => false
            ),
            'localGate' => array(
                'type' => 'INT1',
                'nullable' => false
            ),
            'visitorGate' => array(
                'type' => 'INT1',
                'nullable' => false
            ),
            'localFans' => array(
                'type' => 'INT1',
                'nullable' => false
            ),
            'visitorFans' => array(
                'type' => 'INT1',
                'nullable' => false
            ),
            'localMoney' => array(
                'type' => 'INT2',
                'nullable' => false
            ),
            'visitorMoney' => array(
                'type' => 'INT2',
                'nullable' => false
            ),
            'comment' => array(
                'type' => 'TEXT',
                'nullable' => true
            )
        ),
        $db = 'DB',
        $fluid = true,
        $table = 'game';
}

