<?php
if (!defined('ABSPATH')) {
    exit;
}
$language = gd_portfolio_current_language();
$menu_label = $language === 'en' ? 'Main navigation' : 'Ana gezinme';
$toggle_label = $language === 'en' ? 'Toggle menu' : 'Menüyü aç veya kapat';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main-content"><?php echo esc_html($language === 'en' ? 'Skip to content' : 'İçeriğe geç'); ?></a>
<header class="site-header">
    <div class="container nav-wrap">
        <a class="brand" href="<?php echo esc_url(gd_portfolio_page_url('home')); ?>">
            <span class="brand-mark">gd.</span><span>Göksu Düzgün</span>
        </a>
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="main-nav">
            <span class="sr-only"><?php echo esc_html($toggle_label); ?></span><span></span><span></span><span></span>
        </button>
        <nav class="main-nav" id="main-nav" aria-label="<?php echo esc_attr($menu_label); ?>">
            <?php foreach (gd_portfolio_navigation_items() as $key => $label) :
                $url = gd_portfolio_page_url($key);
                $is_current = is_front_page() ? $key === 'home' : is_page($key === 'home' ? 'ana-sayfa' : $key);
                if ($key !== 'home' && is_page()) {
                    $page = get_queried_object();
                    if ($page && function_exists('pll_get_post')) {
                        $translated_page = pll_get_post($page->ID, $language);
                        $target_page = get_page_by_path($language === 'en'
                            ? array('home' => 'home', 'about' => 'about', 'projects' => 'projects', 'blog' => 'articles', 'gallery' => 'gallery', 'contact' => 'contact')[$key]
                            : array('home' => 'ana-sayfa', 'about' => 'hakkimda', 'projects' => 'projeler', 'blog' => 'blog', 'gallery' => 'galeri', 'contact' => 'iletisim')[$key]);
                        $is_current = $translated_page && $target_page && $translated_page === $target_page->ID;
                    } else {
                        $is_current = is_page($language === 'en'
                            ? array('home' => 'home', 'about' => 'about', 'projects' => 'projects', 'blog' => 'articles', 'gallery' => 'gallery', 'contact' => 'contact')[$key]
                            : array('home' => 'ana-sayfa', 'about' => 'hakkimda', 'projects' => 'projeler', 'blog' => 'blog', 'gallery' => 'galeri', 'contact' => 'iletisim')[$key]);
                    }
                }
                ?>
                <a href="<?php echo esc_url($url); ?>"<?php echo $is_current ? ' class="active" aria-current="page"' : ''; ?>><?php echo esc_html($label); ?></a>
            <?php endforeach; ?>
            <?php gd_portfolio_language_switcher(); ?>
        </nav>
    </div>
</header>
<main id="main-content">
