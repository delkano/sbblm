<?php
namespace Model;

class League extends \DB\Cortex {
    protected
        $fieldConf = array(
            'manager' => array(
                'belongs-to-one' => '\Model\Coach',
                'nullable' => false
            ),
            'teams' => array(
                'has-many' => array('\Model\Team', 'league')
            ),
            'welcome' => array(
                'type' => 'TEXT',
                'nullable' => false
            ),
            'seasons' => array(
                'has-many' => array('\Model\Season', 'league')
            ) 
        ),
        $db = 'DB',
        $fluid = true,
        $table = 'league';
}
