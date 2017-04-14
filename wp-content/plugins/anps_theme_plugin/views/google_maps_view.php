<?php
include_once ANPS_PLUGIN_PATH.'classes/AnpsAdminPlugin.php';
if (isset($_GET['save_ga'])) {
    $anps_plugin->save_options('google_maps');
}
?>
<form action="themes.php?page=anps_plugin_options&sub_page=google_maps&save_ga" method="post">
    <div class="content-inner">
        <div class="row">
            <div class="col-md-12">
                <h3><i class="fa fa-map"></i><?php esc_html_e("Google Maps", 'industrial'); ?></h3>
                <?php echo $anps_plugin->anps_create_text_option('anps_google_maps'); ?>
            </div>
        </div>

            <p><strong><?php esc_html_e('How to Generate an API Key for Google Maps', 'industrial'); ?></strong></p>

            <br>

            <ol>
                <li><?php esc_html_e('Go to the', 'industrial'); ?> <a href="https://console.developers.google.com/flows/enableapi?apiid=maps_backend,geocoding_backend,directions_backend,distance_matrix_backend,elevation_backend,places_backend&keyType=CLIENT_SIDE&reusekey=true" target="_blank">Google API Console</a>.</li>
                <li><?php esc_html_e('Create or select a project.', 'industrial'); ?></li>
                <li><?php esc_html_e('Click Continue to enable the API and any related services.', 'industrial'); ?></li>
                <li><?php esc_html_e('On the Credentials page, get a Browser key (and set the API Credentials).', 'industrial'); ?></li>
                <li><?php esc_html_e('Note: If you have an existing Browser key, you may use that key.', 'industrial'); ?></li>
                <li><?php esc_html_e('For the Accept requests from these HTTP referrers option, add your domain. An example for adding anpsthemes.com:', 'industrial'); ?>
                    <ul>
                        <li>*.anpsthemes.com*</li>
                        <li>http://anpsthemes.com/*</li>
                    </ul>
                </li>
            </ol>
        <p><?php esc_html_e('You may also need to enable the Google Maps APIs. Inside the Google API console navigate to Library and under Google Maps APIs enable Google Maps JavaScript API and Google Maps Embed API.', 'industrial'); ?></p>

        <p><?php printf(esc_html__('For more information take a look at %sour blog post%s.', 'industrial'), '<a href="https://anpsthemes.com/using-google-maps-themes/" target="_blank">', '</a>'); ?></p>
    </div>
    <div class="fixsave"><button type="submit"><?php esc_html_e('Save', 'industrial'); ?><i class="fa fa-floppy-o"></i></button></div>
    <button type="submit" class="bottom-save absolute"><i class="fa fa-floppy-o"></i><?php esc_html_e('Save all changes', 'industrial'); ?></button>
</form>
