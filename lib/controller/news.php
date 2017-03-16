<?php
namespace Controller;

class News {
    public function getLast($f3) {
        $news = new \Model\News();

        return $news->find('', array('order' => 'date DESC', 'limit' => 3));
    }
    public function getPage($f3) {
        $news = new \Model\News();
        $page_nb = intval($f3->get("GET[p]"));

        $f3->set("news", $news->paginate($page_nb, 10, null, array('order' => 'date DESC')));
        $f3->set("page.title", $f3->get("L.news.archive"));

        $f3->set("page.template", "newsPage");
        echo \Template::instance()->render('layout.html');
    }

    public function edit($f3, $params) {
        $news = new \Model\News();
        if(!empty($params['id'])) {
            $id = intval($params['id']);
            $news->load(array('id=?', $id));
            $new = false;
        } else {
            $new = true;
        }

        if($new || !$news->dry()) {
            $f3->set('news', $news);
            if($new)
                $f3->set('page.title', $f3->get("L.news.creating"));
            else
                $f3->set('page.title', $f3->get("L.news.editing")." - ".$news->title);
            $f3->set('page.template', "newsEdit");

            echo \Template::instance()->render('layout.html');
        } else {
            $f3->error(404);
        }
    }

    public function update($f3, $params) {
        $news = new \Model\News();
        if(!empty($params['id'])) {
            $id = intval($params['id']);
            $news->load(array('id=?', $id));
        }
        //Getting the data
        $news->title = trim($f3->get("POST.title"));
        $news->content = trim($f3->get("POST.content"));
        if(empty($news->author))
            $news->author = $f3->get("coach");
        $news->date = $f3->get("POST.date")?:time();

        $news->save();

        $f3->reroute('@home');
    }
    
}
