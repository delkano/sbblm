<?php

namespace Controller;

class Config {
    public function create($f3) {
        $f3->set("cfg.logo", \Model\Config::read("logo"));
        $f3->set("cfg.name", \Model\Config::read("name"));
        $f3->set("cfg.welcome", \Model\Config::read("welcome"));
        $f3->set("cfg.initialgold", \Model\Config::read("initialgold"));
        $f3->set("cfg.ff", \Model\Config::read("ff"));
        $f3->set("cfg.ffPrice", \Model\Config::read("ffPrice"));

        $f3->set("cfg.roundLength", \Model\Config::read("roundLength"));
        $f3->set("cfg.officials", \Model\Config::read("officials"));
        $f3->set("cfg.friendlies", \Model\Config::read("friendlies"));
        $f3->set("cfg.lostPoints", \Model\Config::read("lostPoints"));
        $f3->set("cfg.drawPoints", \Model\Config::read("drawPoints"));
        $f3->set("cfg.wonPoints", \Model\Config::read("wonPoints"));

        $f3->set("page.title", "League Configuration");
        $f3->set('page.template', "configEdit");

        echo \Template::instance()->render("layout.html");
    }

    public function save($f3) {
        $name = trim($f3->get("POST.name"));
        $welcome = trim($f3->get("POST.welcome"));
        $initialgold = trim($f3->get("POST.initialgold"));
        $ff = trim($f3->get("POST.ff"));
        $ffPrice = trim($f3->get("POST.ffPrice"));

        $roundLength = trim($f3->get("POST.roundLength"));
        $officials = trim($f3->get("POST.officials"));
        $friendlies = trim($f3->get("POST.friendlies"));

        $draw = trim($f3->get("POST.drawPoints"));
        $lost = trim($f3->get("POST.lostPoints"));
        $won = trim($f3->get("POST.wonPoints"));

        \Model\Config::store("name", $name);
        \Model\Config::store("welcome", $welcome);
        \Model\Config::store("initialgold", $initialgold);
        \Model\Config::store("ff", $ff);
        \Model\Config::store("ffPrice", $ffPrice);

        \Model\Config::store("roundLength", $roundLength);
        \Model\Config::store("officials", $officials);
        \Model\Config::store("friendlies", $friendlies);

        \Model\Config::store("wonPoints", $won);
        \Model\Config::store("lostPoints", $lost);
        \Model\Config::store("drawPoints", $draw);

        if(!empty($f3->get("FILES.upload-logo.name"))) {
            $f3->set('UPLOADS', 'img/');
            $web = new \Web();
            $file = $web->receive(function($file) {
                if($file['size'] > (2 * 1024 * 1024)) // if bigger than 2 MB
                    return false; // this file is not valid, return false will skip moving it
                return true;
            },true, function() { return "logo.png"; });

            $logo = array_keys($file)[0];
            \Model\Config::store("logo", $logo);
        }

        $f3->reroute("@home");
    }

    /**
     * We'll add, later on, the possibility to change it
     */
}
