<?php

namespace Controller;

class Team {

    public function getOne($f3, $params) {
        $id = intval($params['id']);
        $team = new \Model\Team();
        $team->load(array('id=?', $id));

        if($team->dry()) {
            $f3->error(404);
        } else {
            $f3->set('team', $team);
            $f3->set('page.title', $team->name);
            $f3->set('page.template', "teamView");

            echo \Template::instance()->render('layout.html');
        }
    }

    public function edit($f3, $params) {
        $team = new \Model\Team();
        if(!empty($params['id'])) {
            $id = intval($params['id']);
            $team->load(array('id=?', $id));
            $new = false;
        } else {
            $new = true;
        }

        $list = new \Model\PositionList();
        $lists = $list->find();
        $f3->set("lists", $lists);

        if($new || !$team->dry()) {
            $f3->set('team', $team);
            if($new) {
                $f3->set('page.title', "New team");
            } else
                $f3->set('page.title', $team->name." - Edit");
            $f3->set('page.template', "teamEdit");

            echo \Template::instance()->render('layout.html');
        } else {
            $f3->error(404);
        }
    }

    public function update($f3, $params) {
        $team = new \Model\Team();
        if(!empty($params['id'])) {
            $id = intval($params['id']);
            $team->load(array('id=?', $id));

            $existing = $team->players;
        }
        $p = $f3->get("POST");
        //Getting the data
        $team->name = $p['teamname'];
        $team->coach = $f3->get("coach");
        $team->money = $p['money'];
        $team->list = $p['race'];
        $team->value = $p['value'];
        $team->FF = $p['ff'];
        $team->rerolls = $p['rerolls'];
        $team->assistants = $p['assistants'];
        $team->cheerleaders = $p['cheerleaders'];
        $team->apothecary = $p['apothecary'];
        // Add the currently logged in coach as team coach
        $team->save();

        $post = $f3->get("POST");
        //Now, get each player.
        $saved = array();
        foreach($post['name'] as $key=>$name) {
            $player = new \Model\Player();
            if(!empty($post['id'][$key])) {
                $pl_id = intval($post['id'][$key]);
                $player->load(array('id=?', $pl_id));
            }

            $player->name = trim($name);
            $player->position = $post['position'][$key];
            $player->number = $post['number'][$key];
            $player->MA = $post['ma'][$key];
            $player->AG = $post['ag'][$key];
            $player->ST = $post['st'][$key];
            $player->AV = $post['av'][$key];
            $player->basicSkills = $post['basicskills'][$key];
            $player->learnedSkills = $post['learnedskills'][$key];
            $player->CP = $post['cp'][$key];
            $player->TD = $post['td'][$key];
            $player->Int = $post['int'][$key];
            $player->Cas = $post['cas'][$key];
            $player->MVP = $post['mvp'][$key];
            $player->SPP = $post['spp'][$key];
            $player->level = $post['level'][$key];
            $player->value = $post['playervalue'][$key];
            $player->team = $team;
            $player->save();
            $saved[$player->name] = true;
        }
        //Now, let's delete the ones that were not on the POSTed array
        foreach($existing?:[] as $old) {
            if(empty($saved[$old->name])) {
                $old->erase();
            }
        }

        if(empty($params['id'])) {
            $file = $f3->get('FILES'); 
            $img = new \Image($file['logo']['tmp_name'], false, '');
            $img->resize(180, 180, false, false);
            $slug = trim($team->name);
            $slug = transliterator_transliterate("Any-Latin; NFD; [:Nonspacing Mark:] Remove; NFC; [:Punctuation:] Remove; Lower();", $slug);
            $slug = preg_replace('/[-\s]+/', '-', $slug);
            $name = "img/teamlogos/$slug.png";
            $f3->write( $name, $img->dump('png') );


            /*
            $f3->set("team", $team);
            $f3->set('UPLOADS', 'img/teamlogos/');
            $web = new \Web();
            $file = $web->receive(function($file) {
                if($file['size'] > (2 * 1024 * 1024)) // if bigger than 2 MB
                    return false; // this file is not valid, return false will skip moving it
                return true;
            },true, function() { return \Base::instance()->get("team")->name.".png"; });

            $name = array_keys($file)[0];
             */
            $team->logo = $name;
        }

        $team->save();

        $f3->reroute("@team_view(@id=".$team->id.")");
    }

    public function uploadLogo($f3, $params) {
        $id = intval($params['id']);
        $team = new \Model\Team();
        $team->load(array('id=?', $id));

        if($team->dry()) {
            $f3->error(404);
        } else {
            $file = $f3->get('FILES'); 
            $img = new \Image($file['logo']['tmp_name'], false, '');
            $img->resize(180, 180, false, false);
            $slug = trim($team->name);
            $slug = transliterator_transliterate("Any-Latin; NFD; [:Nonspacing Mark:] Remove; NFC; [:Punctuation:] Remove; Lower();", $slug);
            $slug = preg_replace('/[-\s]+/', '-', $slug);
            $name = "img/teamlogos/$slug.png";
            $f3->write( $name, $img->dump('png') );
            $team->logo = $name;

            /*

            $f3->set("team", $team);

            $f3->set('UPLOADS', 'img/teamlogos/');
            $web = new \Web();
            $file = $web->receive(function($file) {
                if($file['size'] > (2 * 1024 * 1024)) // if bigger than 2 MB
                    return false; // this file is not valid, return false will skip moving it
                return true;
            },true, function() { return \Base::instance()->get("team")->logo.".png"; });

            $name = array_keys($file)[0];

            $team->logo = $name;
             */
            $team->save();

            echo json_encode(array('code' => 0, 'url' => $f3->get("BASE")."/".$name));
        }
    }

}
