<?php
namespace Controller;

class Coach {

    /***
     * Basic REST
     ***/

    public function getOne($f3, $params) {
        $id = intval($params['id']);
        $coach = new \Model\Coach();
        $coach->load(array('id=?', $id));

        if($coach->dry()) {
            $f3->error(404);
        } else {
            $f3->set('coach', $coach);
            $f3->set('page.title', $coach->name);
            $f3->set('page.template', "coachView");

            echo \Template::instance()->render('layout.html');
        }
    }
    public function getList($f3) {
        $coaches = new \Model\Coach();
        $coaches = $coaches->find();

        $f3->set('coaches', $coaches);
        $f3->set("page.title", "Coach List");
        $f3->set("page.template", "coachList");

        echo \Template::instance()->render('layout.html');
    }
    public function profile($f3){
        if(!empty($f3->get("SESSION")) && !empty($f3->get("SESSION.coach"))) {
            $coach = \Controller\Coach::get($f3, $f3->get("SESSION.coach"));
            $f3->set('coach', $coach);
            $f3->set('page.title', $coach->name." - Edit");
            $f3->set('page.template', "coachView");
            $f3->set("myprofile", true);

            echo \Template::instance()->render('layout.html');
        }
    }

    public function profileEdit($f3){
        if(!empty($f3->get("SESSION")) && !empty($f3->get("SESSION.coach"))) {
            $coach = \Controller\Coach::get($f3, $f3->get("SESSION.coach"));
            $f3->set('coach', $coach);
            $f3->set('page.title', $coach->name." - Edit");
            $f3->set('page.template', "coachEdit");
            $f3->set("myprofile", true);

            echo \Template::instance()->render('layout.html');
        }
    }

    public function edit($f3, $params) {
        $coach = new \Model\Coach();
        if(!empty($params['id'])) {
            $id = intval($params['id']);
            $coach->load(array('id=?', $id));
            $new = false;
        } else {
            $new = true;
        }

        if($new || !$coach->dry()) {
            $f3->set('coach', $coach);
            if($new)
                $f3->set('page.title', "New coach");
            else
                $f3->set('page.title', $coach->name." - Edit");
            $f3->set('page.template', "coachEdit");

            echo \Template::instance()->render('layout.html');
        } else {
            $f3->error(404);
        }
    }

    public function update($f3, $params) {
        $coach = new \Model\Coach();
        if(!empty($params['id'])) {
            $id = intval($params['id']);
            $coach->load(array('id=?', $id));
        }
        //Getting the data
        $coach->username = trim($f3->get("POST.username"));
        if(!empty($f3->get("POST.password")))
            $coach->password = password_hash($f3->get("POST.password"), PASSWORD_DEFAULT);
        $coach->name = trim($f3->get("POST.name"));
        $coach->email = trim($f3->get("POST.email"));
        if($f3->get("coach")->manager ) {
            $coach->manager = $f3->exists("POST.manager");
        }


        if(empty($params['id'])) { // This is causing trouble on 7.1, but not on the OVH PHP version
            $file = $f3->get('FILES'); 
            print_r($f3->get('FILES'));
            $img = new \Image($file['avatar']['tmp_name'], false, '');
            $img->resize(180, 180, false, false);
            $name = "img/avatars/".$coach->username.".png";
            $f3->write( $name, $img->dump('png') );

            /* Old code, works but does not resize
            $f3->set('UPLOADS', 'img/avatars/');
            $web = new \Web();
            $file = $web->receive(function($file) {
                print_r($file); exit;
                if($file['size'] > (2 * 1024 * 1024)) // if bigger than 2 MB
                    return false; // this file is not valid, return false will skip moving it
                return true;
            },true, function() { return \Base::instance()->get("coach")->username.".png"; });

            $name = array_keys($file)[0];
             */
            $coach->avatar = $name;
        }

        $coach->save();

        $f3->reroute("@coach_view(@id=$coach->id)");
    }

    public function uploadAvatar($f3, $params) {
        $id = intval($params['id']);
        $coach = new \Model\Coach();
        $coach->load(array('id=?', $id));

        if($coach->dry()) {
            $f3->error(404);
        } else {
            $file = $f3->get('FILES');
            $img = new \Image($file['avatar']['tmp_name'], false, '');
            $img->resize(180, 180, false, false);
            $name = "img/avatars/".$coach->username.".png";
            $f3->write( $name, $img->dump('png') );
            $coach->avatar = $name;
            $coach->save();

            echo json_encode(array('code' => 0, 'url' => $f3->get("BASE")."/".$name));
        }
    }

    public function delete($f3, $params) {
        $id = intval($params['id']);
        $coach = new \Model\Coach();
        $coach->load(array('id=?', $id));

        if($coach->dry()) {
            $f3->error(404);
        } else {
            $coach->erase();
            $f3->reroute("@coaches_list");
        }
    }

    public static function get($f3, $name) {
        $coach = new \Model\Coach();
        $coach->load(array('username = ?', $name));
        if($coach->dry()) return false;
        else return $coach;
    }
}
