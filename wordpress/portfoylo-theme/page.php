<?php
get_header();
while (have_posts()) :
    the_post();
    the_content();
    if (is_page(array('blog', 'articles'))) {
        get_template_part('template-parts/post-grid');
    }
endwhile;
get_footer();
