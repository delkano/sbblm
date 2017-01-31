<?php
namespace Model;

class SkillList extends \DB\Cortex {
    protected
        $fieldConf = array(
            'name' => array(
                'type' => 'VARCHAR256',
                'nullable' => false
            ),
            'skills' => array(
                'has-many' => array('\Model\Skill', 'list')
            )
        ),
        $db = 'DB',
        $fluid = true,
        $table = 'skill_list';
}

