<div class="post__main">
    <div class="post-link__wrapper">
        <a class="post-link__external" href="<?=htmlspecialchars($post['content'])?>" title="Перейти по ссылке">
            <div class="post-link__info-wrapper">
                <div class="post-link__info">
                    <h3><?=$post['title'];?></h3>
                    <span><?=htmlspecialchars($post['content'])?></span>
                </div>
            </div>
        </a>
    </div>
</div>
