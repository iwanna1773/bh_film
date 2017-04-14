<?php
class AnpsText extends WP_Widget {
    private $allowed_html;

    public function __construct() {
        parent::__construct(
            'AnpsText', 'AnpsThemes - Text and icon', array('description' => esc_html__('Enter text and/or icon to show on page. Can only be used in the Top bar widget areas.', 'industrial'),)
        );

        $this->allowed_html = array(
            'a' => array(
                'href' => array(),
                'title' => array(),
                'target' => array(),
                'class' => array(),
                'style' => array(),
            ),
            'span' => array(
                'style' => array(),
                'class' => array()
            ),
            'em' => array(
                'class' => array()
            ),
            'br' => array(),
            'strong' => array(
                'class' => array()
            )
        );

        add_action( 'admin_enqueue_scripts', array( $this, 'anps_enqueue_scripts' ) );
        add_action( 'admin_footer-widgets.php', array( $this, 'anps_print_scripts' ), 9999 );
    }

    function anps_enqueue_scripts( $hook_suffix ) {
        wp_enqueue_style('fontawesome');
    }

    function anps_print_scripts() {
        ?>
        <script>
            jQuery(function($) {
                $(document).on('widget-added widget-updated', anpsColorPickerUpdate);
                $(document).ready(anpsColorPickerReady);
            });
        </script>
        <?php
    }

    function form($instance) {
        $instance = wp_parse_args((array) $instance, array(
                'title'  => '',
                'texts' => ''
            )
        );

        $texts = explode('|', $instance['texts']);
        ?>

        <!-- Title -->
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e("Title", 'industrial'); ?></label>
            <input id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" class="widefat" value="<?php echo esc_attr($instance['title']); ?>" />
        </p>

        <div data-anps-repeat>
            <!-- Social Icons field (hidden) -->
            <input data-anps-repeat-field id="<?php echo esc_attr($this->get_field_id('texts')); ?>" name="<?php echo esc_attr($this->get_field_name('texts')); ?>" type="hidden" value="<?php echo esc_attr($instance['texts']); ?>">

            <!-- Repeater items wrapper -->
            <div class="anps-repeat-items" data-anps-repeat-items>
                <?php foreach($texts as $text) : ?>
                <div class="anps-repeat-item" data-anps-repeat-item>
                    <!-- Fields -->
                    <p>

                        <?php
                            $text = explode(';', $text);
                            $icon = '';
                            $icon_color = '';
                            $title = '';
                            $title_color = '';
                            $subtitle = '';
                            $subtitle_color = '';

                            /* Get all values */
                            if( count($text) <= 3 ) {
                                /* Legacy */
                                if( isset($text[0]) ) { $icon = $text[0]; }
                                if( isset($text[1]) ) { $title = $text[1]; }
                                if( isset($text[2]) ) { $icon_color = $text[2]; }
                            } else {
                                if( isset($text[0]) ) { $icon = $text[0]; }
                                if( isset($text[1]) ) { $title = $text[1]; }
                                if( isset($text[2]) ) { $subtitle = $text[2]; }
                                if( isset($text[3]) ) { $icon_color = $text[3]; }
                                if( isset($text[4]) ) { $title_color = $text[4]; }
                                if( isset($text[5]) ) { $subtitle_color = $text[5]; }
                            }
                        ?>
                        <div class="anps-iconpicker">
                            <i class="fa <?php echo esc_attr($icon); ?>"></i>
                            <input type="text" value="<?php echo esc_attr($icon); ?>">
                            <button type="button"><?php esc_html_e('Select icon', 'industrial'); ?></button>
                        </div>
                    </p>

                    <p>
                        <label><?php esc_html_e('Title', 'industrial'); ?></label>
                        <input type="text" class="widefat" value="<?php echo htmlspecialchars(wp_kses($title, $this->allowed_html)); ?>" />
                    </p>
                    <p>
                        <label><?php esc_html_e('Subtitle', 'industrial'); ?></label>
                        <input type="text" class="widefat" value="<?php echo htmlspecialchars(wp_kses($subtitle, $this->allowed_html)); ?>" />
                    </p>

                    <!-- Icon color -->
                    <p>
                        <label><?php esc_html_e('Icon color', 'industrial'); ?></label><br>
                        <input class="anps-color-picker" type="text" value="<?php echo esc_attr($icon_color); ?>" />
                    </p>
                    <!-- Title color -->
                    <p>
                        <label><?php esc_html_e('Title color', 'industrial'); ?></label><br>
                        <input class="anps-color-picker" type="text" value="<?php echo esc_attr($title_color); ?>" />
                    </p>
                    <!-- Subtitle color -->
                    <p>
                        <label><?php esc_html_e('Subtitle color', 'industrial'); ?></label><br>
                        <input class="anps-color-picker" type="text" value="<?php echo esc_attr($subtitle_color); ?>" />
                    </p>

                    <!-- Repeater buttons -->
                    <div class="anps-repeat-buttons">
                        <button class="anps-repeat-remove" type="button" data-anps-repeat-remove>-</button>
                        <button class="anps-repeat-add" type="button" data-anps-repeat-add>+</button>
                    </div>
                </div>
                <?php endforeach; ?>
             </div>
        </div>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['texts'] = $new_instance['texts'];
        return $instance;
    }

    function widget($args, $instance) {
        extract($args, EXTR_SKIP);
        $title = '';
        if( isset($instance['title']) ) {
            $title = $instance['title'];
        }
        $texts = $instance['texts'];
        $texts = explode('|', $instance['texts']);

        echo $before_widget;

        ?>

        <?php if(isset($title) && $title != '') : ?>
            <h3 class="widget-title"><?php echo esc_html($title); ?></h3>
        <?php endif; ?>

        <?php
        echo '<ul class="contact-info">';
        foreach($texts as $text) {
            $text = explode(';', $text);
            $icon = '';
            $icon_color = '';
            $title = '';
            $title_color = '';
            $subtitle = '';
            $subtitle_color = '';

            /* Get all values */
            if( count($text) <= 4 ) {
                /* Legacy */
                if( isset($text[0]) ) { $icon = $text[0]; }
                if( isset($text[1]) ) { $title = $text[1]; }
                if( isset($text[2]) ) { $icon_color = ' style="color: ' . $text[2] . ';"'; }
            } else {
                if( isset($text[0]) ) { $icon = $text[0]; }
                if( isset($text[1]) ) { $title = $text[1]; }
                if( isset($text[2]) ) { $subtitle = $text[2]; }
                if( isset($text[3]) ) { $icon_color = ' style="color: ' . $text[3] . ';"'; }
                if( isset($text[4]) ) { $title_color = ' style="color: ' . $text[4] . ' !important;"'; }
                if( isset($text[5]) ) { $subtitle_color = ' style="color: ' . $text[5] . ';"'; }
            }

            if( count($text) <= 3 ) {
                /* Legacy */
                echo '<li><i class="fa ' . esc_attr($icon) . '"' . $icon_color . '></i>' . wp_kses($title, $this->allowed_html) . '</li>';
            } else {
                echo '<li><i class="fa ' . esc_attr($icon) . '"' . $icon_color . '></i><span class="important"' . $title_color . '>' . wp_kses($title, $this->allowed_html) . '</span><br><span' . $subtitle_color . '>' . wp_kses($subtitle, $this->allowed_html) . '</span></li>';
            }
        }
        echo '</ul>';
        echo $after_widget;
    }

}

add_action( 'widgets_init', create_function('', 'return register_widget("AnpsText");') );
