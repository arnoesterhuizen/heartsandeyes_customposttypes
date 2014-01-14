<div class="wrap">
    <h2>WP Hearts & Eyes Plugin</h2>
    <form method="post" action="options.php"> 
        <?php @settings_fields('wp_heartsandeyes_plugin-group'); ?>
        <?php @do_settings_fields('wp_heartsandeyes_plugin-group'); ?>

        <?php do_settings_sections('wp_heartsandeyes_plugin'); ?>

        <?php @submit_button(); ?>
    </form>
</div>
