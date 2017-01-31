<?php
namespace Model;

class Season extends \DB\Cortex {
    protected
        $fieldConf = array(
            'name' => array(
                'type' => 'VARCHAR256',
                'nullable' => false
            ),
            'number' => array(
                'type' => 'INT1',
                'nullable' => false
            ),
            'comment' => array(
                'type' => 'TEXT'
            ),
            'teams' => array(
                'has-many' => array('\Model\Team', 'seasons')
            ),
            'games' => array(
                'has-many' => array('\Model\Game', 'season')
            )
        ),
        $db = 'DB',
        $fluid = true,
        $table = 'season';

    public function hasTeam($team) {
        foreach($this->teams?:[] as $s) {
            if($s->id == $team->id) return true;
        }
        return false;
    }

    static public function getCurrent($f3) {
        $season = (new Season())->find('', array('order' => 'number DESC', 'limit' => 1));
        return $season[0];
    }
}
 
