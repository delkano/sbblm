<?php

namespace Controller;

class Auth {
    /***
     * Login and authorization related functions
     ***/

    public function login($f3) {
        $f3->set('page.title', 'login');
        $f3->set("page.template", "login");
        echo \Template::instance()->render('layout.html');
    }

    public function check($f3) {
        $coach = new \Model\Coach();
        $coach->load(array('username = ?', $f3->get("POST.username")));
        if($coach->dry()) {
            $f3->error = $f3['L.login.wrongcredentials'];
            //$f3->reroute("@login");
            return $this->login($f3);
        }
        if(password_verify($f3->get("POST.password"), $coach->password)) {
            $f3->set("SESSION.coach", $coach->username);
            $f3->set("coach", $coach);
            if($f3->exists('POST.referer')) {
                $f3->reroute($f3['POST.referer']);
            } else 
                $f3->reroute("@home");
        } else {
            $f3->error = $f3['L.login.wrongcredentials'];
            //$f3->reroute("@login");
            return $this->login($f3);
        }
    }

    public static function role($f3) {
        $coach = false;
        if(!empty($f3->get("coach"))) $coach = $f3->get("coach");
        else if(!empty($f3->get("SESSION")) && !empty($f3->get("SESSION.coach"))) {
            $coach = \Controller\Coach::get($f3, $f3->get("SESSION.coach"));
            $f3->set("coach", $coach);
        }
        if(!$coach) return "guest";
        if($coach->manager) return "manager";
        else return "coach";
    }

    public function logout($f3) {
        $f3->set("SESSION.coach", false);
        $f3->set("coach", false);
        $f3->reroute("@login");
    }
}
