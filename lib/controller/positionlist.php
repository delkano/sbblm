<?php
namespace Controller;

class PositionList {

    /**
     * Getting the Positions in the list, in json format
     */
    public function getPositions($f3, $params) {
        $list = new \Model\PositionList();
        $list->load(array("id= ?", intval($params['id'])));

        if($list->dry()) {
            $f3->error(404);
        }

        $arr = array();
        foreach($list->positions as $pos) {
            $p = array(
                'id' => $pos->id,
                'name' => $pos->name,
                'max' => $pos->max,
                'MA' => $pos->MA,
                'ST' => $pos->ST,
                'AG' => $pos->AG,
                'AV' => $pos->AV,
                'value' => $pos->value,
                'skills' => array(),
                'basic' => array(),
                'doubles' => array()
            );
            foreach($pos->skills as $s) {
                $skill = array(
                    'id' => $s->id,
                    'name' => $s->name,
                    'desc' => $s->description
                );
                $p['skills'][] = $skill;
            }
             foreach($pos->basic as $s) {
                $skill = array(
                    'id' => $s->id,
                    'name' => $s->name,
                );
                $p['basic'][] = $skill;
            }
             foreach($pos->doubles as $s) {
                $skill = array(
                    'id' => $s->id,
                    'name' => $s->name,
                );
                $p['doubles'][] = $skill;
            }
            $arr[] = $p;
        }

        print_r(json_encode(array(
            'rerolls' => $list->rerolls,
            'description' => $list->description,
            'positions' => $arr
        )));
    }
}
