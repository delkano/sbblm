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
                'nullable' => true
            ),
            'visitorResult' => array(
                'type' => 'INT1',
                'nullable' => true
            ),
            'localCasualties' => array(
                'type' => 'INT1',
                'nullable' => true
            ),
            'visitorCasualties' => array(
                'type' => 'INT1',
                'nullable' => true
            ),
            'localGate' => array(
                'type' => 'INT1',
                'nullable' => true
            ),
            'visitorGate' => array(
                'type' => 'INT1',
                'nullable' => true
            ),
            'localFans' => array(
                'type' => 'INT1',
                'nullable' => true
            ),
            'visitorFans' => array(
                'type' => 'INT1',
                'nullable' => true
            ),
            'localMoney' => array(
                'type' => 'INT2',
                'nullable' => true
            ),
            'visitorMoney' => array(
                'type' => 'INT2',
                'nullable' => true
            ),
            'comment' => array(
                'type' => 'TEXT',
                'nullable' => false
            )
        ),
        $db = 'DB',
        $fluid = true,
        $table = 'game';
}

