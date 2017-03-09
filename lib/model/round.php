<?php
namespace Model;

class Round extends \DB\Cortex {
    protected
        $fieldConf = array(
            'number' => array(
                'type' => 'INT1',
                'nullable' => false
            ),
            'season' => array(
                'belongs-to-one' => '\Model\Season'
            ),
            'games' => array(
                'has-many' => array('\Model\Game', 'round')
            ),
            'over' => array(
                'type' => 'BOOLEAN',
                'default' => 0
            )
        ),
        $db = 'DB',
        $fluid = true,
        $table = 'round';
}
