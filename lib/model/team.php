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

    public function wins() {
        $res = 0;
        foreach($this->hosted?:[] as $game) {
            $res+= ($game->localResult > $game->visitorResult)?1:0;
        }
        foreach($this->visited?:[] as $game) {
            $res+= ($game->localResult < $game->visitorResult)?1:0;
        }
        return $res;
    }
    public function losses() {
        $res = 0;
        foreach($this->hosted?:[] as $game) {
            $res+= ($game->localResult < $game->visitorResult)?1:0;
        }
        foreach($this->visited?:[] as $game) {
            $res+= ($game->localResult > $game->visitorResult)?1:0;
        }
        return $res;
    }
    public function ties() {
        $res = 0;
        foreach($this->hosted?:[] as $game) {
            if($game->localResult!==null)
                $res+= ($game->localResult == $game->visitorResult)?1:0;
        }
        foreach($this->visited?:[] as $game) {
            if($game->visitorResult!==null)
                $res+= ($game->localResult == $game->visitorResult)?1:0;
        }
        return $res;
    }
    public function winperc() {
        $total = $this->wins()+$this->losses()+$this->ties();
        return $total?$this->wins()*100/$total:'-';
    }
    public function cp() {
        $res = 0;
        foreach($this->players?:[] as $p) {
            $res+= $p->CP;
        }
        return $res;
    }
    public function td() {
        $res = 0;
        foreach($this->players?:[] as $p) {
            $res+= $p->TD;
        }
        return $res;
    }
    public function int() {
        $res = 0;
        foreach($this->players?:[] as $p) {
            $res+= $p->Int;
        }
        return $res;
    }
    public function cas() {
        $res = 0;
        foreach($this->players?:[] as $p) {
            $res+= $p->Cas;
        }
        return $res;
    }
}
