<div id="welcome-message">
    <?php echo $cfg['welcome']; ?>

</div>

<div id="news-block">
    <h3><?php echo $LANG['basic']['news']; ?></h3>
    <div class="news-meta">
        <?php if($MANAGER): ?><a href="<?php echo \Base::instance()->alias('news_new'); ?>"><?php echo $LANG['news']['new']; ?></a><?php endif ?>
    </div>
    <ul id="news-list">
        <?php foreach($news?:[] as $piece): ?>
        <li>
            <article>
                <header>
                    <h4><?php echo $piece['title']; ?></h4>
                    <div class='news-meta'>
                        <?php echo \Base::instance()->format($LANG['news']['meta'],$piece['author']['name'],$piece['date']); ?>

                        <?php if($MANAGER): ?>- <a href="<?php echo \Base::instance()->alias('news_edit', 'id='.$piece['id']); ?>"><?php echo $LANG['news']['edit']; ?></a><?php endif ?>
                    </div>
                </header>
                <div class='news-body'>
                    <?php echo $piece['content']; ?>

                </div>
            </article>
        </li>
        <?php endforeach ?>
    </ul>
</div>
