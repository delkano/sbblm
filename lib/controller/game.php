<?php
namespace Controller;

class Game {
    public function getOne($f3, $params) {
       $id = intval($params['id']);
        $game = new \Model\Game();
        $game->load(array('id=?', $id));

        if($game->dry()) {
            $f3->error(404);
        } else {
            $f3->set('game', $game);
            $f3->set('page.title', $game->name);
            $f3->set('page.template', "gameView");

            echo \Template::instance()->render('layout.html');
        }
    }
    public function getList($f3) {
        $games = new \Model\Game();
        $games = $games->find();

        $f3->set('games', $games);
        $f3->set("page.title", "Game List");
        $f3->set("page.template", "gameList");

        echo \Template::instance()->render('layout.html');
    }
    public function getNext($f3) {
        $games = new \Model\Game();
        $games = $games->find(array('date>?', time()), array('order'=>'date DESC'));

        return $games;
    }
    public function program($f3, $params) {
        $game = new \Model\Game();
        if(!empty($params['id'])) {
            $id = intval($params['id']);
            $game->load(array('id=?', $id));
            $new = false;
        } else {
            $new = true;
        }

        $teams = (new \Model\Team())->find();

        if($new || !$game->dry()) {
            $f3->set('game', $game);
            if($new)
                $f3->set('page.title', "Program game");
            else
                $f3->set('page.title', $f3->format($f3->get("L.game.title"))." - Reprogram");

            $f3->set('page.template', "gameProgram");

            $f3->set("teams", $teams);

            echo \Template::instance()->render('layout.html');
        } else {
            $f3->error(404);
        }
    }
    public function results($f3, $params) {
        $game = new \Model\Game();
        if(!empty($params['id'])) {
            $id = intval($params['id']);
            $game->load(array('id=?', $id));
        }

        $teams = (new \Model\Team())->find();

        if($new || !$game->dry()) {
            $f3->set('game', $game);
            $f3->set('page.title', $f3->format($f3->get("L.game.title"))." - Results");

            $f3->set('page.template', "gameResults");

            $f3->set("teams", $teams);

            echo \Template::instance()->render('layout.html');
        } else {
            $f3->error(404);
        }
    }

    public function update($f3, $params) {
        $game = new \Model\Game();
        if(!empty($params['id'])) { //Updating
            $id = intval($params['id']);
            $game->load(array('id=?', $id));
            //
            //Getting the data (this part will exist only on results)
            $game->localResult = intval($f3->get("POST.localResult"));
            $game->visitorResult = intval($f3->get("POST.visitorResult"));
            $game->localCasualties = intval($f3->get("POST.localCasualties"));
            $game->visitorCasualties = intval($f3->get("POST.visitorCasualties"));
            $game->localGate = intval($f3->get("POST.localGate"));
            $game->visitorGate = intval($f3->get("POST.visitorGate"));
            $game->localFans = intval($f3->get("POST.localFans"));
            $game->visitorFans = intval($f3->get("POST.visitorFans"));
            $game->localMoney = intval($f3->get("POST.localMoney"));
            $game->visitorMoney = intval($f3->get("POST.visitorMoney"));
        }  // The rest, any time.
        $game->season = \Model\Season::getCurrent($f3);
        if($f3->exists('POST.local'))
            $game->local = intval($f3->get("POST.local"));
        if($f3->exists('POST.visitor'))
            $game->visitor = intval($f3->get("POST.visitor"));
        if($f3->exists('POST.date'))
            $game->date = strtotime($f3->get("POST.date"));

        $game->comment = trim($f3->get("POST.comment"));

        $game->save();

        $f3->reroute("@game_view(@id=$game->id)");
    }

}
