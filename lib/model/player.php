<?php
namespace Model;

class Player extends \DB\Cortex {
    protected
        $fieldConf = array(
            'number' => array(
                'type' => 'INT1',
                'nullable' => false
            ),
            'team' => array(
                'belongs-to-one' => '\Model\Team',
                'nullable' => false
            ),
            'name' => array(
                'type' => 'VARCHAR256',
                'nullable' => false
            ),
            'MA' => array(
                'type' => 'INT1',
                'nullable' => false
            ),
            'ST' => array(
                'type' => 'INT1',
                'nullable' => false
            ),
            'AG' => array(
                'type' => 'INT1',
                'nullable' => false
            ),
            'AV' => array(
                'type' => 'INT1',
                'nullable' => false
            ),
            'position' => array(
                'belongs-to-one' => '\Model\Position'
            ),
            'basicSkills' => array(
                'belongs-to-many' => '\Model\Skill'
            ),
            'learnedSkills' => array(
                'belongs-to-many' => '\Model\Skill'
            ),
            'CP' => array(
                'type' => 'INT1'
            ),
            'TD' => array(
                'type' => 'INT1'
            ),
            'Int' => array(
                'type' => 'INT1'
            ),
            'Cas' => array(
                'type' => 'INT1'
            ),
            'MVP' => array(
                'type' => 'INT1'
            ),
            'SPP' => array(
                'type' => 'INT1'
            ),
            'value' => array(
                'type' => 'INT2'
            ),
            'level' => array(
                'type' => 'INT1'
            ),
            'dead' => array(
                'type' => 'BOOLEAN',
                'default' => 0
            )
        ),
        $db = 'DB',
        $fluid = true,
        $table = 'player';


    public function hasSkill($skill, $include_basic=true) {
        foreach($this->learnedSkills?:[] as $s) {
            if($s->id == $skill->id) return true;
        }
        if($include_basic) {
            foreach($this->basicSkills as $s) {
                if($s->id == $skill->id) return true;
            }
        }
        return false;
    }
}
