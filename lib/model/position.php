<?php
namespace Model;

class Position extends \DB\Cortex {
    protected
        $fieldConf = array(
            'name' => array(
                'type' => 'VARCHAR256',
                'nullable' => false
            ),
            'max' => array(
                'type' => 'INT1',
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
            'value' => array(
                'type' => 'INT2',
                'nullable' => false
            ),
            'list' => array(
                'belongs-to-one' => '\Model\PositionList'
            ),
            'skills' => array(
                'belongs-to-many' => '\Model\Skill'
            ),
            'basic' => array(
                'belongs-to-many' => '\Model\SkillList'
            ),
            'doubles' => array(
                'belongs-to-many' => '\Model\SkillList'
            )
        ),
        $db = 'DB',
        $fluid = true,
        $table = 'position';

    /*****
     * Install list
     *****/
    public static function import($filename) {
        $file = fopen($filename, 'r');
        while($line = fgetcsv($file, 0, ";")) {
            $max = intval(trim($line[0]));
            $team = trim($line[1]);
            $name = trim($line[2]);
            $value = intval(trim($line[3]));
            $ma = intval(trim($line[4]));
            $st = intval(trim($line[5]));
            $ag = intval(trim($line[6]));
            $av = intval(trim($line[7]));
            $skills = explode(',', trim($line[8]));
            $basic = str_split(trim($line[9]));
            $doubles = str_split(trim($line[10]));

            $pos = new \Model\Position();
            $pos->max = $max;
            $pos->name = $name;
            $pos->value = $value;
            $pos->MA = $ma;
            $pos->ST = $st;
            $pos->AG = $ag;
            $pos->AV = $av;

            $list = new \Model\PositionList();
            $list->load(array("name=?", $team));
            if(!$list->dry()) {
                $pos->list = $list;
            } else {
                echo "<p>Error: No se ha encontrado la lista '$team'</p>";
            }

            $skill_ids = array();
            foreach( $skills as $skill) {
                if($skill=="") continue;
                $obj = new \Model\Skill();
                $obj->load(array("name=?", trim($skill)));
                if(!$obj->dry()) {
                    $skill_ids[] = $obj;
                } else {
                    echo "<p>Error: No se ha encontrado la habilidad '$skill'</p>";
                }
            }
            $pos->skills = $skill_ids;

            $basic_ids = array();
            foreach($basic as $letter) {
                switch($letter) {
                case 'G': $listname = "General"; break;
                case 'F': $listname = "Fuerza"; break;
                case 'A': $listname = "Agilidad"; break;
                case 'P': $listname = "Pase"; break;
                case 'M': $listname = "Mutación"; break;
                }

                $list = new \Model\SkillList();
                $list->load(array("name = ?", $listname));
                if(!$list->dry()) {
                    $basic_ids[] = $list;
                } else {
                    echo "<p>Error: No se ha encontrado el tipo de habilidades '$listname'.</p>";
                }
            }
            $pos->basic = $basic_ids;

            $doubles_ids = array();
            foreach($doubles as $letter) {
                switch($letter) {
                case 'G': $listname = "General"; break;
                case 'F': $listname = "Fuerza"; break;
                case 'A': $listname = "Agilidad"; break;
                case 'P': $listname = "Pase"; break;
                case 'M': $listname = "Mutación"; break;
                }

                $list = new \Model\SkillList();
                $list->load(array("name = ?", $listname));
                if(!$list->dry()) {
                    $doubles_ids[] = $list;
                } else {
                    echo "<p>Error: No se ha encontrado el tipo de habilidades '$listname'.</p>";
                }
            }
            $pos->doubles = $doubles_ids;

            $pos->save();
        }
    }

}
