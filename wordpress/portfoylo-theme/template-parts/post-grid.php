<?php
$language = gd_portfolio_current_language();
?>
<section class="section section-soft">
    <div class="container blog-grid">
        <?php
        $portfolio_posts = new WP_Query(array(
            'post_type' => 'post',
            'posts_per_page' => 3,
            'lang' => $language,
            'orderby' => 'date',
            'order' => 'DESC',
        ));
        if ($portfolio_posts->have_posts()) :
            while ($portfolio_posts->have_posts()) :
                $portfolio_posts->the_post();
                $categories = get_the_category();
                $tags = get_the_tags();
                ?>
                <article class="blog-card">
                    <div class="blog-art blog-art-code" aria-hidden="true"><span>&lt;/&gt;</span></div>
                    <div class="blog-card-body">
                        <p class="card-kicker"><?php echo esc_html(!empty($categories) ? $categories[0]->name : ''); ?></p>
                        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <p><?php echo esc_html(get_the_excerpt()); ?></p>
                        <?php if ($tags) : ?>
                            <div class="tag-list" aria-label="<?php echo esc_attr($language === 'en' ? 'Tags' : 'Etiketler'); ?>">
                                <?php foreach ($tags as $tag) : ?>
                                    <span><?php echo esc_html($tag->name); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <a class="text-link" href="<?php the_permalink(); ?>"><?php echo esc_html($language === 'en' ? 'Read article' : 'Yazıyı oku'); ?> <span aria-hidden="true">→</span></a>
                    </div>
                </article>
                <?php
            endwhile;
            wp_reset_postdata();
        endif;
        ?>
    </div>
</section>
