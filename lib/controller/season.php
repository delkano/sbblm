<?php
namespace Controller;

class Season {
    public function getOne($f3, $params) {
       $id = intval($params['id']);
        $season = new \Model\Season();
        $season->load(array('id=?', $id));

        $season->teams->orderBy('points DESC');

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
        $season->teams = $f3->get("POST.teams");

        $season->save();

        $f3->reroute("@season_view(@id=$season->id)");
    }

    public function organize($f3) {
        $season = \Model\Season::getCurrent($f3);
        $teams = $season->teams;
        $teams_nb = count($teams);
        
        $days_per_round = \Model\Config::read("officials");
        $odd = false;
        if($teams_nb % 2 == 1) {
            $odd = true;
            $teams[$teams_nb] = null;
            $teams_nb ++;
        }

        $games_per_round = $teams_nb/2;
        $rounds_nb = ($teams_nb - 1) * $days_per_round;

        $t1 = 0;
        $t2 = $teams_nb - 2;
        for($i = 0; $i < $rounds_nb; $i++) { 
            $rn = 1 + floor($i/$days_per_round);
            if(!isset($round) || $round->number!=$rn) { 
                $round = new \Model\Round();
                $round->number = $rn;
                $round->season = $season;
                $round->save();
            }
            if(!$odd) {
                $game = new \Model\Game();
                $game->round = $round;
                $game->official = true;
                if( ($i % 2) == 0) {
                    $game->local = $teams[$t1];
                    $game->visitor = $teams[$teams_nb-1];
                } else {
                    $game->local = $teams[$teams_nb-1];
                    $game->visitor = $teams[$t1];
                }
                $game->save();
            }
            $t1 = ($t1 + 1) % ($teams_nb - 1);

            for($j = 1; $j < $games_per_round; $j++) {
                $game = new \Model\Game();
                $game->round = $round;
                $game->official = true;

                $game->local = $teams[$t1];
                $game->visitor = $teams[$t2];

                $game->save();

                $t1 = ($t1 + 1) % ($teams_nb - 1);
                $t2 = ($t2 == 0)? $teams_nb-2:$t2-1;
            }
        }
        $f3->reroute("@season_view(@id=$season->id)");
    }
}
