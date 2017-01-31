<?php

namespace Model;

class Skill extends \DB\Cortex {

    protected
        $fieldConf = array(
            'name' => array(
                'type' => 'VARCHAR256',
                'nullable' => false
            ),
            'description' => array(
                'type' => 'TEXT'
            ),
            'list' => array(
                'belongs-to-one' => '\Model\SkillList'
            )
        ),
        $db = 'DB',
        $fluid = true,
        $table = 'skill';

    public static function import($filename) {
        $file = fopen($filename, 'r');
        while($line = fgetcsv($file, 0, ";")) {
            $name = trim($line[0]);
            $type = substr(trim($line[1]), 1, -1);
            $desc = trim($line[2]);

            $skill = new \Model\Skill();
            $skill->name = $name;
            $skill->description = $desc;

            $list = new \Model\SkillList();
            $list->load(array("name = ?", $type));
            if($list->dry()) {
                $list->name = $type;
                $list->save();
            }
            $skill->list = $list;
            $skill->save();
        }
    }
}
