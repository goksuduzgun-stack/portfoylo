<?php
/**
 * Plugin Name: Portfoylo Bilingual Content Setup
 * Description: Imports the Turkish and English portfolio pages and blog posts, then links their Polylang translations.
 * Version: 1.2.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Göksu Düzgün
 */

if (!defined('ABSPATH')) {
    exit;
}

function gd_portfolio_register_contact_route()
{
    register_rest_route('portfoylo/v1', '/contact', array(
        'methods' => WP_REST_Server::CREATABLE,
        'callback' => 'gd_portfolio_send_contact_message',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'gd_portfolio_register_contact_route');

function gd_portfolio_send_contact_message(WP_REST_Request $request)
{
    $data = $request->get_json_params();
    if (!is_array($data)) {
        return new WP_Error('gd_portfolio_contact_invalid', 'Please submit the contact form fields.', array('status' => 400));
    }
    foreach (array('name', 'email', 'subject', 'message', 'website') as $field) {
        if (isset($data[$field]) && !is_string($data[$field])) {
            return new WP_Error('gd_portfolio_contact_invalid', 'Contact form fields must be text.', array('status' => 400));
        }
    }

    if (!empty($data['website'])) {
        return new WP_REST_Response(array('message' => 'Message accepted.'), 200);
    }

    $name = isset($data['name']) ? sanitize_text_field($data['name']) : '';
    $email = isset($data['email']) ? sanitize_email($data['email']) : '';
    $subject = isset($data['subject']) ? sanitize_text_field($data['subject']) : '';
    $message = isset($data['message']) ? sanitize_textarea_field($data['message']) : '';

    if (strlen($name) > 360 || strlen($email) > 254 || strlen($subject) > 450 || strlen($message) > 8000) {
        return new WP_Error('gd_portfolio_contact_too_long', 'One or more fields exceed the allowed length.', array('status' => 400));
    }
    if ($name === '' || !is_email($email) || $message === '') {
        return new WP_Error('gd_portfolio_contact_invalid', 'Enter your name, a valid email address, and a message.', array('status' => 400));
    }

    $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
    $rate_key = 'gd_portfolio_contact_' . hash_hmac('sha256', $ip, wp_salt('auth'));
    $attempts = (int) get_transient($rate_key);
    if ($attempts >= 5) {
        return new WP_Error('gd_portfolio_contact_rate_limited', 'Too many messages were submitted. Please try again later.', array('status' => 429));
    }
    set_transient($rate_key, $attempts + 1, 10 * MINUTE_IN_SECONDS);

    $sent = wp_mail(
        'goksuduzgun@gmail.com',
        $subject !== '' ? $subject : 'Portfolio website message',
        "Name: {$name}\nEmail: {$email}\n\n{$message}",
        array('Reply-To: ' . $email)
    );

    if (!$sent) {
        return new WP_Error('gd_portfolio_contact_send_failed', 'The email could not be sent. Please use the direct email link instead.', array('status' => 500));
    }

    return new WP_REST_Response(array('message' => 'Your message has been sent.'), 200);
}

function gd_portfolio_import_page_map()
{
    return array(
        'tr' => array(
            'home' => array('file' => 'index.html', 'slug' => 'ana-sayfa', 'title' => 'Ana Sayfa'),
            'about' => array('file' => 'hakkimda.html', 'slug' => 'hakkimda', 'title' => 'Hakkımda'),
            'projects' => array('file' => 'projeler.html', 'slug' => 'projeler', 'title' => 'Projeler'),
            'blog' => array('file' => 'blog.html', 'slug' => 'blog', 'title' => 'Makaleler'),
            'gallery' => array('file' => 'galeri.html', 'slug' => 'galeri', 'title' => 'Galeri'),
            'contact' => array('file' => 'iletisim.html', 'slug' => 'iletisim', 'title' => 'İletişim'),
        ),
        'en' => array(
            'home' => array('file' => 'index.html', 'slug' => 'home', 'title' => 'Home'),
            'about' => array('file' => 'about.html', 'slug' => 'about', 'title' => 'About'),
            'projects' => array('file' => 'projects.html', 'slug' => 'projects', 'title' => 'Projects'),
            'blog' => array('file' => 'blog.html', 'slug' => 'articles', 'title' => 'Articles'),
            'gallery' => array('file' => 'gallery.html', 'slug' => 'gallery', 'title' => 'Gallery'),
            'contact' => array('file' => 'contact.html', 'slug' => 'contact', 'title' => 'Contact'),
        ),
    );
}

function gd_portfolio_import_post_map()
{
    return array(
        'tr' => array(
            'semantic-html' => array('slug' => 'semantik-html', 'category' => 'web-development', 'category_slug' => 'web-gelistirme', 'category_name' => 'Web Geliştirme', 'tags' => array('HTML', 'Semantik', 'Erişilebilirlik')),
            'accessibility' => array('slug' => 'web-erisilebilirligi', 'category' => 'accessibility', 'category_slug' => 'erisilebilirlik', 'category_name' => 'Erişilebilirlik', 'tags' => array('Kapsayıcı tasarım', 'Kontrast', 'Klavye')),
            'responsive-design' => array('slug' => 'responsive-tasarim', 'category' => 'css', 'category_slug' => 'css-tr', 'category_name' => 'CSS', 'tags' => array('CSS', 'Mobil', 'Responsive')),
        ),
        'en' => array(
            'semantic-html' => array('slug' => 'semantic-html', 'category' => 'web-development', 'category_slug' => 'web-development', 'category_name' => 'Web Development', 'tags' => array('HTML', 'Semantics', 'Accessibility')),
            'accessibility' => array('slug' => 'web-accessibility', 'category' => 'accessibility', 'category_slug' => 'accessibility', 'category_name' => 'Accessibility', 'tags' => array('Inclusive design', 'Contrast', 'Keyboard')),
            'responsive-design' => array('slug' => 'responsive-design', 'category' => 'css', 'category_slug' => 'css-en', 'category_name' => 'CSS', 'tags' => array('CSS', 'Mobile', 'Responsive')),
        ),
    );
}

function gd_portfolio_inner_html($node, $document)
{
    $html = '';
    foreach ($node->childNodes as $child) {
        $html .= $document->saveHTML($child);
    }

    return $html;
}

function gd_portfolio_load_source($language, $file)
{
    $path = plugin_dir_path(__FILE__) . 'data/' . $language . '/' . $file;
    if (!is_readable($path)) {
        return new WP_Error('gd_portfolio_source_missing', sprintf('Required source file is missing: %s', $file));
    }

    $html = file_get_contents($path);
    if ($html === false) {
        return new WP_Error('gd_portfolio_source_read_failed', sprintf('Could not read source file: %s', $file));
    }

    $previous_errors = libxml_use_internal_errors(true);
    $document = new DOMDocument('1.0', 'UTF-8');
    $loaded = $document->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
    libxml_clear_errors();
    libxml_use_internal_errors($previous_errors);

    if (!$loaded) {
        return new WP_Error('gd_portfolio_source_invalid', sprintf('Could not parse source file: %s', $file));
    }

    return array($document, new DOMXPath($document));
}

function gd_portfolio_source_page_html($language, $file, $page_key)
{
    $source = gd_portfolio_load_source($language, $file);
    if (is_wp_error($source)) {
        return $source;
    }

    list($document, $xpath) = $source;
    $main_nodes = $xpath->query('//main[@id="main-content"]');
    if (!$main_nodes || !$main_nodes->length) {
        return new WP_Error('gd_portfolio_main_missing', sprintf('Main content was not found in %s.', $file));
    }
    $main = $main_nodes->item(0);

    if ($page_key === 'blog') {
        $hero_nodes = $xpath->query('./section[contains(concat(" ", normalize-space(@class), " "), " page-hero ")]', $main);
        if (!$hero_nodes || !$hero_nodes->length) {
            return new WP_Error('gd_portfolio_blog_intro_missing', sprintf('Blog introduction was not found in %s.', $file));
        }
        return gd_portfolio_inner_html($hero_nodes->item(0), $document);
    }

    return gd_portfolio_inner_html($main, $document);
}

function gd_portfolio_source_posts($language)
{
    $map = gd_portfolio_import_post_map();
    $page_map = gd_portfolio_import_page_map();
    $source = gd_portfolio_load_source($language, $page_map[$language]['blog']['file']);
    if (is_wp_error($source)) {
        return $source;
    }

    list($document, $xpath) = $source;
    $article_nodes = $xpath->query('//div[contains(concat(" ", normalize-space(@class), " "), " article-notes ")]/article');
    if (!$article_nodes || $article_nodes->length !== 3) {
        return new WP_Error('gd_portfolio_blog_posts_missing', sprintf('Expected three article entries in the %s blog.', $language));
    }

    $posts = array();
    foreach ($article_nodes as $article) {
        $source_id = $article->getAttribute('id');
        $english_ids = array('semantic-html' => 'semantic-html', 'accessibility' => 'accessibility', 'responsive-design' => 'responsive-design');
        $turkish_ids = array('semantik-html' => 'semantic-html', 'erisilebilirlik' => 'accessibility', 'responsive-tasarim' => 'responsive-design');
        $post_key = $language === 'en' ? (isset($english_ids[$source_id]) ? $english_ids[$source_id] : '') : (isset($turkish_ids[$source_id]) ? $turkish_ids[$source_id] : '');
        if (!$post_key || !isset($map[$language][$post_key])) {
            return new WP_Error('gd_portfolio_blog_post_unknown', sprintf('Unrecognised article in the %s blog.', $language));
        }

        $title_nodes = $xpath->query('./h2', $article);
        if (!$title_nodes || !$title_nodes->length) {
            return new WP_Error('gd_portfolio_blog_title_missing', sprintf('Article title is missing in the %s blog.', $language));
        }
        $title = trim($title_nodes->item(0)->textContent);
        $content = gd_portfolio_inner_html($article, $document);
        $content = preg_replace('/<p class="eyebrow">.*?<\/p>/s', '', $content, 1);
        $content = preg_replace('/<h2>.*?<\/h2>/s', '', $content, 1);
        $paragraph_nodes = $xpath->query('./p', $article);
        $excerpt = $paragraph_nodes && $paragraph_nodes->length ? trim($paragraph_nodes->item(1)->textContent) : '';

        $posts[$post_key] = array(
            'slug' => $map[$language][$post_key]['slug'],
            'title' => $title,
            'content' => trim($content),
            'excerpt' => $excerpt,
            'category' => $map[$language][$post_key]['category'],
            'category_slug' => $map[$language][$post_key]['category_slug'],
            'category_name' => $map[$language][$post_key]['category_name'],
            'tags' => $map[$language][$post_key]['tags'],
        );
    }

    return $posts;
}

function gd_portfolio_imported_post_id($slug, $post_type, $title)
{
    $existing = get_page_by_path($slug, OBJECT, $post_type);
    if ($existing) {
        if (get_post_meta($existing->ID, '_gd_portfolio_imported', true) !== '1') {
            return new WP_Error('gd_portfolio_slug_conflict', sprintf('A %s already uses the required slug "%s". Rename it before importing.', $post_type, $slug));
        }

        return (int) $existing->ID;
    }

    $post_id = wp_insert_post(array(
        'post_type' => $post_type,
        'post_status' => 'publish',
        'post_title' => $title,
        'post_name' => $slug,
        'post_content' => '',
    ), true);
    if (is_wp_error($post_id)) {
        return $post_id;
    }

    update_post_meta($post_id, '_gd_portfolio_imported', '1');
    return (int) $post_id;
}

function gd_portfolio_get_or_create_term($name, $slug, $taxonomy)
{
    $existing = term_exists($slug, $taxonomy);
    if ($existing) {
        return (int) (is_array($existing) ? $existing['term_id'] : $existing);
    }

    $created = wp_insert_term($name, $taxonomy, array('slug' => $slug));
    if (is_wp_error($created)) {
        return $created;
    }

    return (int) $created['term_id'];
}

function gd_portfolio_import_content()
{
    if (!class_exists('DOMDocument')) {
        return new WP_Error('gd_portfolio_dom_missing', 'The PHP DOM extension is required to import the supplied portfolio pages.');
    }
    if (
        !function_exists('pll_languages_list')
        || !function_exists('pll_set_post_language')
        || !function_exists('pll_save_post_translations')
        || !function_exists('pll_set_term_language')
        || !function_exists('pll_save_term_translations')
    ) {
        return new WP_Error('gd_portfolio_polylang_missing', 'Install and activate Polylang before importing the bilingual pages.');
    }
    $available_languages = pll_languages_list(array('fields' => 'slug'));
    if (!is_array($available_languages) || !in_array('tr', $available_languages, true) || !in_array('en', $available_languages, true)) {
        return new WP_Error('gd_portfolio_languages_missing', 'Add Turkish (tr) and English (en) in Languages → Languages before importing.');
    }

    $page_map = gd_portfolio_import_page_map();
    $post_map = gd_portfolio_import_post_map();
    $page_ids = array('tr' => array(), 'en' => array());
    $post_ids = array('tr' => array(), 'en' => array());
    $term_ids = array();
    $tag_ids = array();
    $source_contents = array('tr' => array(), 'en' => array());
    $source_posts = array('tr' => array(), 'en' => array());

    foreach (array('tr', 'en') as $language) {
        foreach ($page_map[$language] as $key => $page) {
            $content = gd_portfolio_source_page_html($language, $page['file'], $key);
            if (is_wp_error($content)) {
                return $content;
            }
            $source_contents[$language][$key] = $content;

            $post_id = gd_portfolio_imported_post_id($page['slug'], 'page', $page['title']);
            if (is_wp_error($post_id)) {
                return $post_id;
            }
            $page_ids[$language][$key] = $post_id;
            pll_set_post_language($post_id, $language);
        }

        $posts = gd_portfolio_source_posts($language);
        if (is_wp_error($posts)) {
            return $posts;
        }
        $source_posts[$language] = $posts;

        foreach ($posts as $key => $post) {
            $post_id = gd_portfolio_imported_post_id($post['slug'], 'post', $post['title']);
            if (is_wp_error($post_id)) {
                return $post_id;
            }
            $post_ids[$language][$key] = $post_id;
            pll_set_post_language($post_id, $language);

            $category_id = gd_portfolio_get_or_create_term($post['category_name'], $post['category_slug'], 'category');
            if (is_wp_error($category_id)) {
                return $category_id;
            }
            $category_ids = isset($term_ids[$post['category']][$language]) ? $term_ids[$post['category']][$language] : array();
            $category_ids[] = $category_id;
            $term_ids[$post['category']][$language] = array_unique($category_ids);
            pll_set_term_language($category_id, $language);

            $assigned_tags = array();
            $tag_position = 0;
            foreach ($post['tags'] as $tag_name) {
                $tag_slug = sanitize_title($tag_name . '-' . $language);
                $tag_id = gd_portfolio_get_or_create_term($tag_name, $tag_slug, 'post_tag');
                if (is_wp_error($tag_id)) {
                    return $tag_id;
                }
                pll_set_term_language($tag_id, $language);
                $assigned_tags[] = $tag_id;
                $tag_ids[$key][$tag_position][$language] = $tag_id;
                $tag_position++;
            }
            $assigned_categories = wp_set_object_terms($post_id, array($category_id), 'category', false);
            if (is_wp_error($assigned_categories)) {
                return $assigned_categories;
            }
            $assigned_tags_result = wp_set_object_terms($post_id, $assigned_tags, 'post_tag', false);
            if (is_wp_error($assigned_tags_result)) {
                return $assigned_tags_result;
            }
        }
    }

    foreach (array_keys($page_map['tr']) as $key) {
        pll_save_post_translations(array('tr' => $page_ids['tr'][$key], 'en' => $page_ids['en'][$key]));
    }
    foreach (array_keys($post_map['tr']) as $key) {
        pll_save_post_translations(array('tr' => $post_ids['tr'][$key], 'en' => $post_ids['en'][$key]));
    }
    foreach ($term_ids as $languages) {
        if (isset($languages['tr'][0], $languages['en'][0])) {
            pll_save_term_translations(array('tr' => $languages['tr'][0], 'en' => $languages['en'][0]));
        }
    }
    foreach ($tag_ids as $post_tags) {
        foreach ($post_tags as $languages) {
            if (isset($languages['tr'], $languages['en'])) {
                pll_save_term_translations(array('tr' => $languages['tr'], 'en' => $languages['en']));
            }
        }
    }

    foreach (array('tr', 'en') as $language) {
        foreach ($page_map[$language] as $key => $page) {
            $content = $source_contents[$language][$key];
            $content = preg_replace_callback(
                '/(<img\b[^>]*\bsrc=["\'])(?:\.\.\/)?images\/([^"\']+)(["\'])/i',
                function ($matches) {
                    return $matches[1] . get_theme_file_uri('assets/images/' . rawurlencode($matches[2])) . $matches[3];
                },
                $content
            );
            $content = preg_replace_callback(
                '/(<source\b[^>]*\bsrc=["\'])(?:\.\.\/)?images\/([^"\']+)(["\'])/i',
                function ($matches) {
                    return $matches[1] . get_theme_file_uri('assets/images/' . rawurlencode($matches[2])) . $matches[3];
                },
                $content
            );
            $content = preg_replace_callback(
                '/(<video\b[^>]*\bposter=["\'])(?:\.\.\/)?images\/([^"\']+)(["\'])/i',
                function ($matches) {
                    return $matches[1] . get_theme_file_uri('assets/images/' . rawurlencode($matches[2])) . $matches[3];
                },
                $content
            );
            $content = preg_replace_callback(
                '/(<a\b[^>]*\bhref=["\'])([^"\']+\.html(?:#[^"\']*)?)(["\'])/i',
                function ($matches) use ($language, $page_map, $page_ids) {
                    $target_path = parse_url($matches[2], PHP_URL_PATH);
                    $target_file = basename($target_path);
                    $file_to_key = array(
                        'index.html' => 'home',
                        'about.html' => 'about',
                        'hakkimda.html' => 'about',
                        'projects.html' => 'projects',
                        'projeler.html' => 'projects',
                        'blog.html' => 'blog',
                        'gallery.html' => 'gallery',
                        'galeri.html' => 'gallery',
                        'contact.html' => 'contact',
                        'iletisim.html' => 'contact',
                    );
                    if (!isset($file_to_key[$target_file])) {
                        return $matches[0];
                    }
                    $target_key = $file_to_key[$target_file];
                    $target_id = $page_ids[$language][$target_key];
                    $fragment = parse_url($matches[2], PHP_URL_FRAGMENT);
                    $url = get_permalink($target_id) . ($fragment !== null ? '#' . $fragment : '');
                    return $matches[1] . esc_url($url) . $matches[3];
                },
                $content
            );

            $updated = wp_update_post(array(
                'ID' => $page_ids[$language][$key],
                'post_content' => $content,
            ), true);
            if (is_wp_error($updated)) {
                return $updated;
            }
        }

        foreach ($source_posts[$language] as $key => $post) {
            $updated = wp_update_post(array(
                'ID' => $post_ids[$language][$key],
                'post_content' => $post['content'],
                'post_excerpt' => $post['excerpt'],
            ), true);
            if (is_wp_error($updated)) {
                return $updated;
            }
        }
    }

    update_option('show_on_front', 'page');
    update_option('page_on_front', $page_ids['tr']['home']);
    update_option('gd_portfolio_content_installed', '1');

    return true;
}

function gd_portfolio_importer_menu()
{
    add_management_page(
        'Portfolio Content Setup',
        'Portfolio Content Setup',
        'manage_options',
        'gd-portfolio-content',
        'gd_portfolio_importer_page'
    );
}
add_action('admin_menu', 'gd_portfolio_importer_menu');

function gd_portfolio_importer_page()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have permission to access this page.', 'goksu-portfolio'));
    }
    $message = get_transient('gd_portfolio_import_message');
    delete_transient('gd_portfolio_import_message');
    ?>
    <div class="wrap">
        <h1>Portfolio Content Setup</h1>
        <?php if (is_array($message)) : ?>
            <div class="notice <?php echo $message['success'] ? 'notice-success' : 'notice-error'; ?> is-dismissible">
                <p><?php echo esc_html($message['text']); ?></p>
            </div>
        <?php endif; ?>
        <?php if (get_option('gd_portfolio_content_installed') === '1') : ?>
            <div class="notice notice-success"><p>The bilingual portfolio pages and blog posts are installed. Re-running updates only content created by this importer.</p></div>
        <?php endif; ?>
        <p>Before importing, install Polylang and add Turkish (<code>tr</code>) and English (<code>en</code>) under Languages → Languages.</p>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="gd_portfolio_import">
            <?php wp_nonce_field('gd_portfolio_import'); ?>
            <?php submit_button(get_option('gd_portfolio_content_installed') === '1' ? 'Update imported portfolio content' : 'Install bilingual portfolio content'); ?>
        </form>
    </div>
    <?php
}

function gd_portfolio_importer_handle_request()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have permission to import portfolio content.', 'goksu-portfolio'));
    }
    check_admin_referer('gd_portfolio_import');

    $result = gd_portfolio_import_content();
    if (is_wp_error($result)) {
        set_transient('gd_portfolio_import_message', array('success' => false, 'text' => $result->get_error_message()), 60);
    } else {
        set_transient('gd_portfolio_import_message', array('success' => true, 'text' => 'Bilingual portfolio pages, translations, categories, tags, and posts were installed.'), 60);
    }

    wp_safe_redirect(admin_url('tools.php?page=gd-portfolio-content'));
    exit;
}
add_action('admin_post_gd_portfolio_import', 'gd_portfolio_importer_handle_request');
