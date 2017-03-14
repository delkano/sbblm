<?php
namespace Controller;

class News {
    public function getLast($f3) {
        $news = new \Model\News();

        return $news->find('', array('order' => 'date DESC', 'limit' => 10));
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
                $f3->set('page.title', "Creating news");
            else
                $f3->set('page.title', $news->name." - Edit");
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
