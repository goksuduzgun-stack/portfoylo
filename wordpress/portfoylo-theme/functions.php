<?php

if (!defined('ABSPATH')) {
    exit;
}

function gd_portfolio_enqueue_assets()
{
    wp_enqueue_style(
        'gd-portfolio-fonts',
        'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=DM+Sans:wght@400;500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap',
        array(),
        null
    );
    wp_enqueue_style(
        'gd-portfolio-style',
        get_theme_file_uri('assets/css/style.css'),
        array('gd-portfolio-fonts'),
        wp_get_theme()->get('Version')
    );
    wp_enqueue_script(
        'gd-portfolio-script',
        get_theme_file_uri('assets/js/script.js'),
        array(),
        wp_get_theme()->get('Version'),
        true
    );
    wp_localize_script('gd-portfolio-script', 'gdPortfolioContact', array(
        'url' => rest_url('portfoylo/v1/contact'),
    ));
}
add_action('wp_enqueue_scripts', 'gd_portfolio_enqueue_assets');

function gd_portfolio_theme_setup()
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'gallery', 'caption', 'style', 'script'));
}
add_action('after_setup_theme', 'gd_portfolio_theme_setup');

function gd_portfolio_current_language()
{
    if (function_exists('pll_current_language')) {
        $language = pll_current_language('slug');
        if ($language) {
            return $language;
        }
    }

    return 'tr';
}

function gd_portfolio_page_url($key)
{
    $pages = array(
        'tr' => array(
            'home' => 'ana-sayfa',
            'about' => 'hakkimda',
            'projects' => 'projeler',
            'blog' => 'blog',
            'gallery' => 'galeri',
            'contact' => 'iletisim',
        ),
        'en' => array(
            'home' => 'home',
            'about' => 'about',
            'projects' => 'projects',
            'blog' => 'articles',
            'gallery' => 'gallery',
            'contact' => 'contact',
        ),
    );
    $language = gd_portfolio_current_language();
    $slug = isset($pages[$language][$key]) ? $pages[$language][$key] : $pages['tr']['home'];
    $page = get_page_by_path($slug);

    if ($page && function_exists('pll_get_post')) {
        $translated_id = pll_get_post($page->ID, $language);
        if ($translated_id) {
            $page = get_post($translated_id);
        }
    }

    return $page ? get_permalink($page) : home_url('/');
}

function gd_portfolio_navigation_items()
{
    if (gd_portfolio_current_language() === 'en') {
        return array(
            'home' => 'Home',
            'about' => 'About',
            'projects' => 'Projects',
            'blog' => 'Blog',
            'gallery' => 'Gallery',
            'contact' => 'Contact',
        );
    }

    return array(
        'home' => 'Ana Sayfa',
        'about' => 'Hakkımda',
        'projects' => 'Projeler',
        'blog' => 'Blog',
        'gallery' => 'Galeri',
        'contact' => 'İletişim',
    );
}

function gd_portfolio_language_switcher()
{
    if (!function_exists('pll_the_languages')) {
        return;
    }

    $languages = pll_the_languages(array('raw' => 1, 'hide_if_no_translation' => 0));
    if (!is_array($languages)) {
        return;
    }

    foreach ($languages as $language) {
        if (!empty($language['current_lang']) || empty($language['url'])) {
            continue;
        }

        $label = isset($language['slug']) && $language['slug'] === 'en' ? 'EN' : 'TR';
        printf(
            '<a class="language-link" href="%1$s" lang="%2$s" hreflang="%2$s">%3$s <span aria-hidden="true">↗</span></a>',
            esc_url($language['url']),
            esc_attr($label === 'EN' ? 'en' : 'tr'),
            esc_html($label)
        );
    }
}
