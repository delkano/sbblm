<?php
namespace Model;

class PositionList extends \DB\Cortex {
    protected
        $fieldConf = array(
           'name' => array(
               'type' => 'VARCHAR256',
               'nullable' => false
            ),
            'description' => array(
               'type' => 'TEXT',
               'nullable' => false
            ),
            'positions' => array(
                'has-many' => array('\Model\Position', 'list')
            ),
            'rerolls' => array(
                'type' => 'INT2',
                'nullable' => false
            )
        ),
        $db = 'DB',
        $fluid = true,
        $table = 'position_list';

    public static function import($filename) {
        $file = fopen($filename, 'r');
        while($line = fgetcsv($file, 0, ";")) {
            $name = trim($line[0]);
            $desc = trim($line[1]);
            $reroll = intval(trim($line[2]));

            $list = new \Model\PositionList();
            $list->name = $name;
            $list->description = $desc;
            $list->rerolls = $reroll;

            $list->save();
        }
    }
}
