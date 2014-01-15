<div class="wrap">
	<h2>Hearts &amp; Eyes Custom Post Types</h2>
    <form method="post" action="options.php"> 
		<?php @settings_fields('heartsandeyes_customposttypes-group'); ?>
		<?php @do_settings_fields('heartsandeyes_customposttypes-group'); ?>

		<?php do_settings_sections('heartsandeyes_customposttypes'); ?>

        <?php @submit_button(); ?>
    </form>
</div>
