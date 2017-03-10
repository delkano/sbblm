<?php
namespace Controller;

class Base {
    public function home($f3) {
        $f3->set("news", (new \Controller\News)->getLast($f3));
        $f3->set("games", (new \Controller\Game)->getNext($f3));

        $season = new \Model\Season();
        $season = $season::getCurrent($f3);

        $season->teams->orderBy('points DESC');
        $f3->set("teams", $season->teams);
        foreach($season->teams?:[] as $team) { // Get all playing players into one collection
            if(empty($players))
                $players = $team->players;
            else
                foreach($team->players?:[] as $player)
                    $players->append($player);
        }

        $players->orderBy('TD DESC');
        $f3->set("players_by_td", array_slice($players->castAll(), 0, 3));

        $players->orderBy('Cas DESC');
        $f3->set("players_by_cas", array_slice($players->castAll(), 0, 3));

        $players->orderBy('Int DESC');
        $f3->set("players_by_int", array_slice($players->castAll(), 0, 3));

        $players->orderBy('CP DESC');
        $f3->set("players_by_cp", array_slice($players->castAll(), 0, 3));

        $f3->set("page.template", "home");
        echo \Template::instance()->render('layout.html');
    }

    public function install($f3) {
        echo "<h3>Creando bases de datos...</h3>";

        $models = array('Coach', 'Game', 'Config', 'Player', 'PositionList', 'Position', 'Season', 'SkillList', 'Skill', 'Team', 'News', 'Invite');
        foreach($models as $model) {
            $class = "\Model\\$model";
            if( $class::setup() )
                echo "<p>Tabla <code>$model</code> creada</p>";
            else
                echo "<p>No se ha podido crear la tabla <code>$model</code>.</p>";
        }
        echo "<h3>Cargando lista de habilidades....</h3>";
        \Model\Skill::import("../rules/skills.csv");
        echo "<h3>Cargando listas de equipo....</h3>";
        \Model\PositionList::import("../rules/teams.csv");
        echo "<h3>Cargando lista de posiciones....</h3>";
        \Model\Position::import("../rules/positions.csv");

        // Let's create the Manager
        $coach = new \Model\Coach();
        $coach->manager=true;
        $f3->set("coach", $coach);

        $f3->set("SESSION.INSTALLING", true);
         
        $f3->route('POST @coach_create: /postinstall', '\Controller\Base->post_install');
        echo \Template::instance()->render('templates/coachEdit.html');
        exit;
    }

    public function post_install($f3) {
        if(!$f3->exists("SESSION.INSTALLING")) {
            $f3->status(404);
            exit;
        }
        $f3->set("SESSION.INSTALLING", false);

        $coach = new \Model\Coach();
        //Getting the data
        $coach->username = trim($f3->get("POST.username"));
        if(!empty($f3->get("POST.password")))
            $coach->password = password_hash($f3->get("POST.password"), PASSWORD_DEFAULT);
        $coach->name = trim($f3->get("POST.name"));
        $coach->email = trim($f3->get("POST.email"));
        $coach->manager = true;

        if(empty($params['id'])) { // This is causing trouble on 7.1, but not on the OVH PHP version
            $file = $f3->get('FILES'); 
            if(!empty($file['avatar']['name'])) {
                $img = new \Image($file['avatar']['tmp_name'], false, '');
                $img->resize(180, 180, false, false);
                $name = "img/avatars/".$coach->username.".png";
                $f3->write( $name, $img->dump('png') );
                $coach->avatar = $name;
            }
        }

        $coach->save();

        $f3->set("coach", $coach);
        $f3->set("SESSION.coach", $coach->username);

        $f3->reroute("config");
    }

	public function assets($f3, $args) {
        $path = $f3->get('UI').$args['type'].'/';
        if($args['type'] == 'less') {
            $parser = new \Less_Parser(array('compress'=>true));
            $files = $_GET['files'];
            foreach(explode(",", $files) as $file) 
                $parser->parseFile($path.$file);

            header('Content-type: text/css');
            echo $parser->getCss();
        } else {
            $files = preg_replace('/(\.+\/)/','',$_GET['files']); // close potential hacking attempts  
            echo \Preview::instance()->resolve(\Web::instance()->minify($files, null, true, $path));
        }
	}
}
