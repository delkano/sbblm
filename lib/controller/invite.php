<?php
namespace Controller;

class Invite {
    public function create($f3) {
        $email = $f3->get("POST[inviteemail]");

        $invite = new \Model\Invite();
        $invite->load(array('email=?', $email));
        if($invite->dry()) { // Create
            $hash = hash("sha256", time());
            $invite->email = $email;
            $invite->hash = $hash;
        } else { // Resend
            $invite->status = 1;
        }
        $invite->save();

        $this->sendMail($invite);
    }

    public function send($f3,$params) {
        $hash = $params['hash'];
        $invite = new \Model\Invite();
        $invite->load(array('hash=?', $hash));
        if($invite->dry) {
            $f3->status(404);
        }
        $invite->status = 1;
        $invite->save();
        $this->sendMail($invite);
    }

    public function spend($f3, $params) {
        $invite = new \Model\Invite();
        $invite->load(array('hash=?', $params['hash']));

        if($invite->dry()) $f3->status(404);

        $f3->set('hash', $invite->hash);
        $coach = new \Model\Coach();
        $coach->email = $invite->email;

        $f3->set('coach', $coach);
        $f3->set('page.title', $f3->get("L.invite.register"));
        $f3->set('page.template', "coachEdit");

        echo \Template::instance()->render('layout.html');
    }

    private function sendMail($invite) {
        $f3 = \Base::instance();
        $subject = $f3->get("L.invite.subject", $f3->get("cfg.name"));
        $f3->set('hash', $invite->hash);
        $text = \Template::instance()->render('invite.txt','text/html');


        // To send HTML mail, the Content-type header must be set
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=UTF-8';

        $headers[] = 'From: '.$f3->get("coach")->email;


        mail($invite->email, $subject, $text, implode("\r\n", $headers));
        //echo "<pre>";
        //print_r($headers);
        //echo "</pre>";

        $f3->set("note", $f3->get("L.invite.sentmessage", $invite->email));
        (new \Controller\Coach)->getList($f3);
    }
}
