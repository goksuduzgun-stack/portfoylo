<?php
get_header();
if (have_posts()) {
    get_template_part('template-parts/post-grid');
} else {
    $language = gd_portfolio_current_language();
    ?>
    <section class="page-hero container">
        <h1><?php echo esc_html($language === 'en' ? 'Nothing here yet.' : 'Henüz içerik yok.'); ?></h1>
    </section>
    <?php
}
get_footer();
