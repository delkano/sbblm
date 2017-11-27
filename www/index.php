<?php 
// composer autoloader for required packages and dependencies
require_once('../vendor/autoload.php');
/** @var \Base $f3 */
$f3 = \Base::instance();
// F3 autoloader for application business code
$f3->config('../config.ini');

if(file_exists($f3->get("DB_NAME")))  {
    $f3->set('DB', new \DB\SQL('sqlite:'.$f3->get('DB_NAME')));

    // Load League config (if present)
    $f3->set("cfg.logo", \Model\Config::read("logo"));
    $f3->set("cfg.name", \Model\Config::read("name"));
    $f3->set("cfg.welcome", \Model\Config::read("welcome"));
    $f3->set("cfg.initialgold", \Model\Config::read("initialgold"));
    $f3->set("cfg.ff", \Model\Config::read("ff"));
    $f3->set("cfg.ffPrice", \Model\Config::read("ffPrice"));

} else if($f3->get("URI") != '/install') {
    $f3->set('DB', new \DB\SQL('sqlite:'.$f3->get('DB_NAME')));
    (new \Controller\Base)->install($f3);
}

//new \DB\SQL\Session($f3->get("DB"));

// Error handling
$f3->set('ONERROR',
    function($f3) {
        switch ($f3->get('ERROR.code')) {
        case 404: echo \Template::instance()->render('404.html'); break;
        case 500: echo "<h2>Error ".$f3->get('ERROR.code')."</h2><p>".$f3->get("ERROR.status")."</p><p>".$f3->get("ERROR.text")."</p><div><pre>".$f3->get("ERROR.trace")."</pre></div>"; break;
        case 403: $f3->reroute("/"); break;
        default: echo "<h2>Big mistake nº".$f3->get('ERROR.code')."</h2><p>".$f3->get("ERROR.status")."</p>"; break;
        }
    }
);

$f3->set("LOGGEDIN", (\Controller\Auth::role($f3) != 'guest'));
$f3->set("MANAGER", (\Controller\Auth::role($f3) == 'manager'));

// Home
$f3->route("GET @home: /", '\Controller\Base->home');
$f3->route("GET @manifest: /manifest.json", function($f3) {
    echo \Template::instance()->render('manifest.json');
});

// Asset management
$f3->route('GET /assets/@type', '\Controller\Base->assets',	3600*24 );

// Login and auth
$f3->route('GET @login: /login', '\Controller\Auth->login');
$f3->route('POST @login_check: /login', '\Controller\Auth->check');
$f3->route('GET @logout: /logout', '\Controller\Auth->logout');

// Route access
$access = Access::instance();
$access->policy('allow');
$access->deny('/config');
$access->allow('/config', 'manager');
$access->deny('/coach/*');
$access->allow('/coach/*', 'manager');
$access->allow('/coach/@/view');
$access->allow('/coach/profile', 'coach');
$access->allow('/coach/profile/edit', 'coach');
$access->allow('/coach/@/avatar', 'coach');
$access->allow('/coach/@/update', 'coach');
$access->deny('/team/*');
$access->allow('/team/*', 'coach,manager');
$access->allow('/team/@/view', 'guest');
$access->deny('/news/*');
$access->allow('/news/*', 'coach,manager');
$access->deny('/season/@/edit');
$access->deny('/season/new');
$access->allow('/season/@/edit', 'manager');
$access->allow('/season/new', 'manager');
$access->deny('/game/@/reprogram', 'guest');
$access->deny('/game/program', 'guest');

$access->authorize(\Controller\Auth::role($f3));

// Normal routes
$f3->route('GET @coach_list: /coaches', '\Controller\Coach->getList');
$f3->route('GET @coach_view: /coach/@id/view', '\Controller\Coach->getOne');
$f3->route('GET @coach_new: /coach/new', '\Controller\Coach->edit');
$f3->route('GET @coach_edit: /coach/@id/edit', '\Controller\Coach->edit');
$f3->route('POST @coach_update: /coach/@id/update', '\Controller\Coach->update');
$f3->route('POST @coach_create: /coach/create', '\Controller\Coach->update');
$f3->route('POST @coach_avatar: /coach/@id/avatar', '\Controller\Coach->uploadAvatar');
$f3->route('GET @coach_delete: /coach/@id/delete', '\Controller\Coach->delete');

$f3->route('GET @coach_profile: /coach/profile', '\Controller\Coach->profile');
$f3->route('GET @coach_profile_edit: /coach/profile/edit', '\Controller\Coach->profileEdit');

$f3->route('GET @team_view: /team/@id/view', '\Controller\Team->getOne');
$f3->route('GET @team_new: /team/new', '\Controller\Team->edit');
$f3->route('GET @team_edit: /team/@id/edit', '\Controller\Team->edit');
$f3->route('POST @team_update: /team/@id/update', '\Controller\Team->update');
$f3->route('POST @team_create: /team/create', '\Controller\Team->update');
$f3->route('POST @team_logo: /team/@id/logo', '\Controller\Team->uploadLogo');

$f3->route('GET @race_getpositions: /race/@id/getlist', '\Controller\PositionList->getPositions');
$f3->route('GET @skills_list: /skills/getlist', '\Controller\Skill->getList');

$f3->route('POST @player_delete: /player/@id/delete', '\Controller\Player->delete');
$f3->route('POST @player_kill: /player/@id/kill', '\Controller\Player->delete');

$f3->route('GET @config: /config', '\Controller\Config->create');
$f3->route('POST @config: /config', '\Controller\Config->save');

$f3->route('GET @news_edit: /news/@id/edit', '\Controller\News->edit');
$f3->route('GET @news_new: /news/new', '\Controller\News->edit');
$f3->route('POST @news_update: /news/@id/update', '\Controller\News->update');
$f3->route('POST @news_create: /news/create', '\Controller\News->update');
$f3->route('GET @news_page: /news', '\Controller\News->getPage');

$f3->route('GET @season_current: /season/current', '\Controller\Season->getCurrent');
$f3->route('GET @season_view: /season/@id/view', '\Controller\Season->getOne');
$f3->route('GET @season_list: /seasons', '\Controller\Season->getList');
$f3->route('GET @season_edit: /season/@id/edit', '\Controller\Season->edit');
$f3->route('GET @season_new: /season/new', '\Controller\Season->edit');
$f3->route('POST @season_update: /season/@id/update', '\Controller\Season->update');
$f3->route('POST @season_create: /season/create', '\Controller\Season->update');

$f3->route('GET @game_view: /game/@id/view', '\Controller\Game->getOne');
$f3->route('GET @game_program: /game/program', '\Controller\Game->program');
$f3->route('GET @game_reprogram: /game/@id/reprogram', '\Controller\Game->program');
$f3->route('GET @game_results: /game/@id/results', '\Controller\Game->results');
$f3->route('POST @game_create: /game/create', '\Controller\Game->update');
$f3->route('POST @game_update: /game/@id/update', '\Controller\Game->update');

$f3->route('GET @season_organize: /season/organize', '\Controller\Season->organize');
// Install: if base /install route doesn't exist, we can only access it by removing "db.sql": safer
//$f3->route('GET @install: /install', '\Controller\Base->install');
$f3->route('POST /postinstall', '\Controller\Base->post_install');

// Invites: how the Manager gets new coaches into his league
$f3->route('POST @invite_create: /invite/create', '\Controller\Invite->create');
$f3->route('GET @invite_send: /invite/send/@hash', '\Controller\Invite->send');
$f3->route('GET @invite_spend: /invite/@hash', '\Controller\Invite->spend');
$f3->route('POST @invite_save: /invite/@hash/save', '\Controller\Coach->update');

//\Assets::instance();
$f3->run();

