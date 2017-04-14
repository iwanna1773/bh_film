<?php
include_once(get_template_directory() . '/anps-framework/classes/AnpsUpgrade.php');
$tf_username = get_option("tf_username", "");
$tf_api_key = get_option("tf_api_key", "");
if(isset($_GET['save_user_data'])) {
    update_option('tf_username',$_POST['tf_username']);
    update_option('tf_api_key',$_POST['tf_api_key']);
    header("Location: themes.php?page=theme_options&sub_page=theme_upgrade");
}
?>
<form action="themes.php?page=theme_options&sub_page=theme_upgrade&save_user_data" method="post">

    <div class="content-inner">
        <!-- Theme upgrade data -->
        <div class="row">
            <div class="col-md-12">
                <h3><i class="fa fa-cloud-download"></i><?php esc_html_e('Theme upgrade', 'industrial'); ?></h3>
                <div class="alert">
                    <i class="fa fa-exclamation-triangle"></i>
                    <?php esc_html_e('Please backup all your files before upgrading (in case you did any custom work). The upgrade will overide all theme files, any custom changes will be lost.', 'industrial'); ?>
                </div>
                <!-- Username -->
            </div>
            <div class="col-md-12">
                <label for="tf_username"><?php esc_html_e("Themeforest username", 'industrial'); ?></label>
                <input type="text" name="tf_username" value="<?php echo esc_attr($tf_username); ?>" />
            </div>
            <!-- API key -->
            <div class="col-md-12">
                <label for="tf_api_key"><?php esc_html_e("Themeforest API key", 'industrial'); ?></label>
                <input type="text" name="tf_api_key" value="<?php echo esc_attr($tf_api_key); ?>" />
            </div>
            <!-- Submit button -->
            <div class="col-md-12">
            <br>
            <input type="submit" value="<?php esc_html_e('Save all changes', 'industrial'); ?>">
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <p><?php esc_html_e('To get your API key, log in to your themeforest account and click settings -> API Keys. Please see the screnshot below. If you do not have any generated API keys, please generate one.', 'industrial'); ?>
                <p><img class="shadow" src="<?php echo get_template_directory_uri(); ?>/anps-framework/images/api.jpg" /></p>
            </div>
        </div>
    </div>

</form>
<?php
if(empty($tf_username) || empty($tf_api_key)) :
?>
    <div class="content-bottom no-border top-margin-70">
    <p class="text-align: center;"><?php esc_html_e('Please enter username and api key.', 'industrial'); ?></p>
    </div>
    <?php elseif($update = AnpsUpgrade::check_theme_update()) :
        ob_start();
        wp_nonce_field('upgrade-core');
        $nonce = ob_get_clean();
    ?>
    <h3 class="action-required"><i class="fa fa-cloud-download"></i><?php esc_html_e("You are using and outdated version of the theme, please upgrade it to get the latest features and ensure safety!", 'industrial'); ?></h3>
    <form action="<?php echo network_admin_url('update-core.php?action=do-theme-upgrade'); ?>" method="post">
        <div class="content-bottom no-border">
            <input type="hidden" name="checked[]" value="<?php echo AnpsUpgrade::get_theme_name(); ?>" />
            <?php echo esc_html($nonce); ?>
            <input type="hidden" name="_wp_http_referer" value="/wp-admin/update-core.php?action=do-theme-upgrade" />
            <div class="twothirds">
                <div class="alert">
                <i class="fa fa-exclamation-triangle"></i>
                <?php esc_html_e('Please backup all your files before upgrading (in case you did any custom work). The upgrade will overide all theme files, any custom changes will be lost.', 'industrial'); ?> ?>
                </div>
            </div>
            <div class="onethird">
                <input class="green right" onclick="return confirm('<?php esc_html_e('I want to upgrade the theme, I understand that this will overwrite all theme files.', 'industrial'); ?>');" type="submit" value="<?php esc_html_e('Upgrade theme', 'industrial'); ?>">
            </div>
            <div class="clear"></div>
        </div>
    </form>
<?php else: ?>
    <div class="content-bottom no-border top-margin-70 text-center">
        <p><?php esc_html_e('You are using the latest version of Construction theme.', 'industrial'); ?></p>
    </div>
<?php endif;
