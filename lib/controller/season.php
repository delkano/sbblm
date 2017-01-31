<?php
namespace Controller;

class Season {
    public function getOne($f3, $params) {
       $id = intval($params['id']);
        $season = new \Model\Season();
        $season->load(array('id=?', $id));

        if($season->dry()) {
            $f3->error(404);
        } else {
            $f3->set('season', $season);
            $f3->set('page.title', $season->name);
            $f3->set('page.template', "seasonView");

            echo \Template::instance()->render('layout.html');
        }
    }
    public function getList($f3) {
        $seasons = new \Model\Season();
        $seasons = $seasons->find();

        $f3->set('seasons', $seasons);
        $f3->set("page.title", "Season List");
        $f3->set("page.template", "seasonList");

        echo \Template::instance()->render('layout.html');
    }
    public function edit($f3, $params) {
        $season = new \Model\Season();
        if(!empty($params['id'])) {
            $id = intval($params['id']);
            $season->load(array('id=?', $id));
            $new = false;
        } else {
            $new = true;
        }

        $teams = (new \Model\Team())->find();

        if($new || !$season->dry()) {
            $f3->set('season', $season);
            if($new)
                $f3->set('page.title', "New season");
            else
                $f3->set('page.title', $f3->format($f3->get("L.season.title"))." - Edit");
            $f3->set('page.template', "seasonEdit");

            $f3->set("teams", $teams);

            echo \Template::instance()->render('layout.html');
        } else {
            $f3->error(404);
        }
    }

    public function update($f3, $params) {
        $season = new \Model\Season();
        if(!empty($params['id'])) {
            $id = intval($params['id']);
            $season->load(array('id=?', $id));
        }
        //Getting the data
        $season->name = trim($f3->get("POST.name"));
        $season->number = trim($f3->get("POST.number"));
        $season->comment = trim($f3->get("POST.comment"));
        $season->teams = trim($f3->get("POST.teams"));

        $season->save();

        $f3->reroute("@season_view(@id=$season->id)");
    }
}
