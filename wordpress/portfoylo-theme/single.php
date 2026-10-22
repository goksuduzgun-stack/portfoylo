<?php
get_header();
while (have_posts()) :
    the_post();
    $language = gd_portfolio_current_language();
    ?>
    <section class="page-hero container">
        <p class="eyebrow"><?php the_category(' · '); ?></p>
        <h1><?php the_title(); ?></h1>
        <p><?php echo esc_html(get_the_date()); ?></p>
    </section>
    <section class="section section-soft">
        <article class="container article-notes">
            <div><?php the_content(); ?></div>
        </article>
    </section>
    <?php
endwhile;
get_footer();
