<?php
namespace Model;

class Team extends \DB\Cortex {
    protected
        $fieldConf = array(
            'name' => array(
                'type' => 'VARCHAR256',
                'nullable' => false
            ),
            'logo' => array(
                'type' => 'VARCHAR256',
                'nullable' => false
            ),
            'money' => array(
                'type' => 'INT2',
                'nullable' => false
            ),
            'seasons' => array(
                'has-many' => array('\Model\Season', 'teams')
            ),
            'list' => array(
                'belongs-to-one' => '\Model\PositionList'
            ),
            'coach' => array(
                'belongs-to-one' => '\Model\Coach'
            ),
            'players' => array(
                'has-many' => array('\Model\Player', 'team')
            ),
            'value' => array(
                'type' => 'INT2'
            ),
            'FF' => array(
                'type' => 'INT1'
            ),
            'rerolls' => array(
                'type' => 'INT1'
            ),
            'assistants' => array(
                'type' => 'INT1'
            ),
            'cheerleaders' => array(
                'type' => 'INT1'
            ),
            'apothecary' => array(
                'type' => 'INT1'
            ),
            'hosted' => array(
                'has-many' => array('\Model\Game', 'local')
            ),
            'visited' => array(
                'has-many' => array('\Model\Game', 'visitor')
            ),
            'points' => array(
                'type' => 'INT2'
            )
        ),
        $db = 'DB',
        $fluid = true,
        $table = 'team';
}
