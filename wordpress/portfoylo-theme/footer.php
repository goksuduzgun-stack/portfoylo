<?php
if (!defined('ABSPATH')) {
    exit;
}
$language = gd_portfolio_current_language();
?>
</main>
<footer class="site-footer">
    <div class="container footer-main">
        <a class="brand" href="<?php echo esc_url(gd_portfolio_page_url('home')); ?>"><span class="brand-mark">gd.</span><span>Göksu Düzgün</span></a>
        <p><?php echo esc_html($language === 'en' ? 'Stay curious. Make with care.' : 'Merakla öğren, özenle üret.'); ?></p>
        <div class="footer-links">
            <a href="<?php echo esc_url(gd_portfolio_page_url('contact')); ?>"><?php echo esc_html($language === 'en' ? 'Contact' : 'İletişim'); ?></a>
            <a href="https://github.com/goksuduzgun-stack" target="_blank" rel="noopener noreferrer">GitHub</a>
        </div>
    </div>
    <div class="container footer-bottom">
        <span>© <?php echo esc_html(wp_date('Y')); ?> Göksu Düzgün</span>
        <span><?php echo esc_html($language === 'en' ? 'Made with WordPress' : 'WordPress ile tasarlandı'); ?></span>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
