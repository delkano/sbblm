<?php
namespace Controller;

class Skill {
    public function getList($f3) {
        $skillList = new \Model\SkillList();
        $lists = $skillList->find();

        $res = array();

        foreach($lists as $list) {
            $skills = $list->skills;
            $s = array();
            foreach($skills as $skill) {
                $s[] = array(
                    'id' =>$skill->id,
                    'name' =>$skill->name,
                    'desc' =>$skill->description
                );
            }
            $res[$list->name] = $s;
        }
        echo json_encode($res);
    }
}
