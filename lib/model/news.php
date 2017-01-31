<?php
namespace Model;

class News extends \DB\Cortex {
    protected
        $fieldConf = array(
            'title' => array(
                'type' => 'VARCHAR256',
                'nullable' => false
            ),
            'date' => array(
                'type' => 'DATETIME',
                'nullable' => false
            ),
            'content' => array(
                'type' => 'TEXT',
                'nullable' => false
            ),
            'author' => array(
                'belongs-to-one' => '\Model\Coach'
            )
        ),
        $db = 'DB',
        $fluid = true,
        $table = 'news';
}

