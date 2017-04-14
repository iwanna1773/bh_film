<?php
/*
 * Shortcodes functions
 */
/* Alert */
if(!function_exists('anps_alert_func')) {
    function anps_alert_func($atts, $content) {
        extract( shortcode_atts( array(
            'type' => ''
        ), $atts ) );
        wp_enqueue_script('alert');
        switch($type) {
            case "info":
                $type_class = " alert-info";
                $icon = "bell-o";
                break;
            case "danger":
                $type_class = " alert-danger";
                $icon = "exclamation";
                break;
            case "warning":
                $type_class = " alert-warning";
                $icon = "info";
                break;
            case "success":
                $type_class = " alert-success";
                $icon = "check";
                break;
            case "useful":
                $type_class = " alert-useful";
                $icon = "lightbulb-o";
                break;
            case "normal":
                $type_class = " alert-normal";
                $icon = "hand-o-right";
                break;
            case "info-2":
                $type_class = " alert-info-style-2";
                $icon = "bell-o";
                break;
            case "danger-2":
                $type_class = " alert-danger-style-2";
                $icon = "exclamation";
                break;
            case "warning-2":
                $type_class = " alert-warning-style-2";
                $icon = "info";
                break;
            case "success-2":
                $type_class = " alert-success-style-2";
                $icon = "check";
                break;
            case "useful-2":
                $type_class = " alert-useful-style-2";
                $icon = "lightbulb-o";
                break;
            case "normal-2":
                $type_class = " alert-normal-style-2";
                $icon = "hand-o-right";
                break;
        }
        return '<div class="alert'.$type_class.'">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i></button>
                    <i class="fa fa-'.$icon.'"></i> '.$content.'
                </div>';
    }
}
/* END Alert */
/* Appointment (added ps) */
if(!function_exists('anps_appointment_func')) {
    function anps_appointment_func($atts, $content) {
        extract( shortcode_atts( array(
            'image_u' => '',
            'location' => '',
            'title' => '',
            'text' => '',
            'contact_form' => ''
        ), $atts ) );
        $data = '';
        $data .= '<div class="appointment">';
            /* Left side */
            $data .= '<div class="appointment-content">';
                $data .= '<div class="appointment-media">';
                if($image_u) {
                    $data .= wp_get_attachment_image($image_u, 'full');
                } elseif($location!='') {
                    $data .= do_shortcode("[google_maps zoom='14' map_type='ROADMAP' height='300' scroll='true'][google_maps_item marker_center='' pin='' location='".$location."'][/google_maps_item][/google_maps]");
                }
                $data .= '</div>';
                $data .= '<div class="text-center"><h4 class="title appointment-title">' . $title . '</h4></div>';
                $data .= '<p class="appointment-text">' . $text . '</p>';
            $data .= '</div>';

            /* Right side */
            $data .= '<div class="appointment-form">';
                $data .= do_shortcode("[contact-form-7 id='$contact_form']");
            $data .= '</div>';
        $data .= '</div>';
        return $data;
    }
}
/* END Appointment */
/* Blog */
if(!function_exists('anps_blog_func')) {
    function anps_blog_func($atts, $content) {
        extract( shortcode_atts( array(
    		'category' => '',
    		'orderby' => '',
    		'order' => '',
            'columns' => '1'
        ), $atts ) );
        global $wp_rewrite;

        if(get_query_var('paged') > 1) {
            $current = get_query_var('paged');
        } elseif(get_query_var('page') > 1) {
            $current = get_query_var('page');
        } else {
            $current = 1;
        }
        $args = array(
            'posts_per_page'   => $content,
            'category_name'    => $category,
            'orderby'          => $orderby,
            'order'            => $order,
            'post_type'        => 'post',
            'post_status'      => 'publish',
            'paged'            => $current
        );

        $posts = new WP_Query( $args );

        $pagination = array(
            'base' => @esc_url(add_query_arg('page','%#%')),
            'format' => '',
            'total' => $posts->max_num_pages,
            'current' => $current,
            'show_all' => false,
            'prev_text' => '<i class="fa fa-angle-left"></i> ' . esc_html__( 'Previous ', 'industrial' ),
            'next_text' => esc_html__( 'Next', 'industrial' ) . ' <i class="fa fa-angle-right"></i>',
            'type' => 'list',
            );


        switch($columns) {
            case '1':
                $blog_type = 'col-md-12';
                break;
            case '2':
                $blog_type = 'col-md-6';
                break;
            case '3':
                $blog_type = 'col-md-4';
                break;
            case '4':
                $blog_type = 'col-md-3';
                break;
            default :
                $blog_type = 'col-md-12';
                break;
        }
        $post_text = "";
        if($posts->have_posts()) :
            $post_text .= "<div class='row anps-blog'>";
            global $counter_blog;
            $counter_blog = 1;
            while($posts->have_posts()) :
                $posts->the_post();
                ob_start();
                get_template_part('templates/content-blog', $blog_type);
                $counter_blog++;
                $post_text .= ob_get_clean();
            endwhile;
            if( $wp_rewrite->using_permalinks() ) {
                $pagination['base'] = user_trailingslashit( trailingslashit( esc_url(remove_query_arg('s',get_pagenum_link(1)) ) ) . 'page/%#%/', 'paged');
            }
            if( !empty($wp_query->query_vars['s']) ) {
                $pagination['add_args'] = array('s'=>get_query_var('s'));
            }
            $post_text .= "</div>";
            $post_text .= paginate_links( $pagination );
            wp_reset_postdata();
        else :
            $post_text .= "<h2>".esc_html__('Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'industrial')."</h2>";
        endif;
        return $post_text;
    }
}
/* END Blog */
/* Button */
if(!function_exists('anps_button_func')) {
    $anps_button_counter = 0;
    function anps_button_func($atts, $content) {
        extract( shortcode_atts( array(
            'link'       => '',
            'target'     => '_self',
            'size'       => 'small',
            'style_button'      => 'btn-normal',
            'color'      => '',
            'background' => '',
            'color_hover' => '',
            'background_hover' => '',
            'icon'=>''
        ), $atts ) );
        global $anps_button_counter;

        $style_attr = "";
        if($color && $color!="#ffffff") {
            $style_attr .= "color: $color;";
        }
        if($background && $style_button!='btn-minimal') {
            $style_attr .= "background-color: $background";
        }

        switch($size) {
            case 'large': $size = ' btn-lg'; break;
            case 'medium': $size = ' btn-md'; break;
            case 'small': $size = ' btn-sm'; break;
        }

        $icon_class = "";
        if($icon) {
            $icon_class = "<i class='fa $icon'></i> ";
        }

        //custom id
        $style_id = "button-id-".$anps_button_counter;
        $anps_button_counter++;

        $style_css='';
        if(!$link) {
            $style_css .= "<button id='$style_id' class='btn $style_button$size' style='$style_attr'>$icon_class$content</button>";
        } else {
            $style_css .= "<a id='$style_id' target='$target' href='$link' class='btn $style_button$size' style='$style_attr'>$icon_class$content</a>";
        }
        /* Hover background and hover color */
        if($color_hover || $background_hover) {
            $style_css .= '<style>';
            $style_css .= "#$style_id:hover{";
            if($color_hover) {
               $style_css .= "color: $color_hover !important;";
            }
            if($background_hover && $style_button!='btn-minimal') {
               $style_css .= "background-color: $background_hover !important;";
            }
            $style_css .= '}';
            $style_css .= '</style>';
        }
        return $style_css;
    }
}
/* END Button */
/* Contact info card */
if(!function_exists('anps_contact_info_card_func')) {
    function anps_contact_info_card_func($atts, $content) {
        extract(shortcode_atts( array(
            'num_row' => '3',
        ), $atts));
        global $num_row_global;
        $num_row_global = $num_row;
        $data = '<div class="contact-cards">';
        $data .= '<div class="row">';
        $data .= do_shortcode($content);
        $data .= '</div></div>';
        return $data;
    }
}
//contact info item card
if(!function_exists('anps_contact_info_card_item_func')) {
    function anps_contact_info_card_item_func($atts, $content) {
        extract(shortcode_atts( array(
            'icon' => '',
            'card_title' => '',
            'card_text' => '',
            'title_color' => '#a3baca',
            'desc_color' => '#fff',
            'background_color' => '#215070',
            'icon_color' => '#fff',
            'icon_back_color' => '#3498db',
        ), $atts));
        global $num_row_global;
        $class_row = 'col-md-4';
        if($num_row_global == '2') {
            $class_row = 'col-md-6';
        }
        $data = '';
        $data .= "<div class='$class_row'>";
        $data .= '<div class="card" style="background-color:' . $background_color . '; color:' . $icon_back_color. '">';
        $data .= "<span class='icon'>";
        $data .= "<i class='$icon' style='color: $icon_color'></i>";
        $data .= '</span>';
        $data .= '<div class="content-wrap">';
        if($card_title != '') {
            $data .= "<span class='item-title' style='color:$title_color;'>$card_title</span>";
        }
        if($card_text != '') {
            $data .= "<p class='text font1' style='color:". $desc_color ."' >$card_text</p>";
        }
        $data .= '</div>';
        $data .= '</div>';
        $data .= '</div>';
        return $data;
    }
}
/* END Contact info */
/* Contact info */
if(!function_exists('anps_contact_info_func')) {
    function anps_contact_info_func($atts, $content) {
        return "<table class='info-table'></tbody>".do_shortcode($content)."</table></tbody>";
    }
}
//contact info item
if(!function_exists('anps_contact_info_item_func')) {
    function anps_contact_info_item_func($atts, $content) {
        extract( shortcode_atts( array(
            'icon' => '',
            'url' => ''
        ), $atts ) );
        $contact_info_content = "";
        $contact_info_content .= "<tr class='info-table-row'>";
        $contact_info_content .= "<th class='info-table-icon'><i class='fa ".$icon."'></i></th>";
        $contact_info_content .= "<td class='info-table-content'>";
        if($url) {
            $contact_info_content .= "<strong><a href='$url'>";
        }
        $contact_info_content .= $content;
        if($url) {
            $contact_info_content .= "</a></strong>";
        }
        $contact_info_content .= "</td>";
        $contact_info_content .= "</tr>";
        return $contact_info_content;
    }
}
/* END Contact info */
/* Counter */
if(!function_exists('anps_counter_func')) {
    $counter_number = 0;
    function anps_counter_func($atts, $content) {
        extract( shortcode_atts( array(
                    'max' => "",
                    "number_color" => "",
                    "divider_color" => "",
                    "title_color" => ""
            ), $atts ) );
        global $counter_number;
        $counter_number++;
        wp_enqueue_script('countto');
        /* Number color */
        $style = '';
        if($number_color) {
            $style .= "#anps-counter-$counter_number .title:after {background-color: $number_color;} #anps-counter-$counter_number .title span {color: $number_color;}";
        }
        if($divider_color) {
            $style .= "#anps-counter-$counter_number .title:before {background-color: $divider_color;}";
        }
        if($title_color) {
            $style .= "#anps-counter-$counter_number h4 {color: $title_color;}";
        }
        $data = "<div class='counter-wrap' id='anps-counter-$counter_number'>
                    <h2 class='title'><span class='text-center counter-number' data-to='$max'>0</span></h2>
                    <h4 class='text-center'>$content</h4>
                </div>";
        if($style) {
            $data .= "<style>$style</style>";
        }
        return $data;
    }
}
/* END Counter */
/* Download */
if(!function_exists('anps_download_func')) {
    function anps_download_func($atts, $content) {
        extract( shortcode_atts( array(
            'icon_c' => '',
            'icon_d' => '',
            'text_color' => '#fff',
            'url' => '',
            'download_text' => 'Download'
        ), $atts ) );
        $icon_content = '';
        if($icon_c) {
            $icon_content = "<i class='fa $icon_c'></i> ";
        }
        $icon_download = '';
        if($icon_d) {
            $icon_download = "<i class='fa $icon_d'></i> ";
        }
        $data = '';
        $data .= '<div class="download">';
        $data .= "<div class='download-content text-uppercase' style='color: $text_color'>$icon_content$content</div>";
        if($url) {
            $data .= "<a href='$url' class='btn btn-md btn-dark btn-shadow'>$icon_download$download_text</a>";
        }
        $data .= '</div>';
        return $data;
    }
}
/* END Download */
/* CTA */
if(!function_exists('anps_cta_func')) {
    function anps_cta_func($atts, $content) {
        extract( shortcode_atts( array(
            'text_color' => '#242424',
            'url' => '',
            'button_text' => 'Button',
            'button_type' => 'btn-normal',
            'target' => '_self'
        ), $atts ) );

        $data = '';
        $data .= '<div class="anps_cta">';
        $data .= "<div class='cta-content font1' style='color: $text_color'>$content</div>";
        if($url) {
            $data .= "<a href='$url' target='$target' class='btn btn-lg $button_type '>$button_text</a>";
        }
        $data .= '</div>';
        return $data;
    }
}
/* END CTA */
/* Error 404 */
if(!function_exists('anps_error_404_func')) {
    function anps_error_404_func($atts, $content) {
        extract( shortcode_atts( array(
            'title' => '',
            'image_u' => ''
        ), $atts ) );

        $data = '';
        $data .= '<div class="text-center">';
        $data .= "<h2 class='title fs30'>$title</h2>";
        $data .= '<br>';
        $data .= "<span>$content</span>";
        $data .= '<br>';
        if( $image_u ) {
            $data .= wp_get_attachment_image($image_u, 'full', 0, array('class' => 'error404'));
        }
        $data .= '</div>';

        return $data;
    }
}
/* END Error 404 */
/* Featured content */
if(!function_exists('anps_featured_content_func')) {
    function anps_featured_content_func($atts, $content) {
        extract( shortcode_atts( array(
            'title' => '',
            'image_u' => '',
            'link' => '',
            'button_text' => 'Read more',
            'video' => '',
            'absolute_img' =>'',
            'exposed' =>'',
            'lightbox' => '',
            'icon' => '',
            'icon_custom' => '',
            'icon_custom_hover' => '',
            'style' => ''
        ), $atts ) );

        if($image_u) {
            $image = wp_get_attachment_image_src($image_u, 'anps-featured');
            $image = $image[0];
        }

        $has_icon = '';
        if($icon) {
            $icon = "<i class='fa fa-$icon'></i>";
            $has_icon = ' featured-has-icon';
        }

        if($icon_custom) {
            if($icon_custom_hover) {
                $icon_custom_hover = wp_get_attachment_image($icon_custom_hover, 'full', false, array('class' => 'featured-custom-icon-hover'));
            }

            $icon =  '<div class="featured-custom-icon">' . wp_get_attachment_image($icon_custom, 'full') . $icon_custom_hover . '</div>';

            $has_icon = ' featured-has-icon';
        }

        $img_class = 'relative';
        $push_top_class = '';
        if($absolute_img!="") {
            $push_top_class = ' featured-push-top';
        }

        /* Media type */
        $media_class = '';
        if($video!="" && $lightbox!="") {
            $media_class = ' featured-video';
        } elseif($video=='' && $lightbox!="") {
            $media_class = ' featured-image';
        }

        $style_class = "";
        if($style!="") {
            $style_class = ' '.$style;
        }

        /* Larger content */
        $large_class = '';
        if($exposed!='') {
            $large_class = ' featured-large';
        }

        $has_link = '';
        if($link!="") {
            $has_link = ' featured-has-link';
        }

        $has_content = '';
        if($content!='') {
            $has_content = ' featured-has-content';
        }

        /* Get image from vimeo or youtube if there is no other image */
        if($video!="" && $image_u=='') {
            if(strpos($video,'vimeo') !== false) {
                $video_id = explode('/', $video);
                $thumbnail = wp_remote_get( 'http://vimeo.com/api/v2/video/'.$video_id[3].'.json' );
                if ($thumbnail) {
                    $body = json_decode($thumbnail['body']);
                    $image = $body[0]->thumbnail_large;
                }
            } else {
                $video_explode = explode('v=', $video);
                $image = "http://img.youtube.com/vi/$video_explode[1]/maxresdefault.jpg";
            }
        }

        $output = "<div class='featured$media_class$large_class$push_top_class$has_link$has_icon$has_content$style_class'>";
        $output .= "<div class='featured-header'>";

        $output .= "<img alt='$title' src='$image'/>";

        $output .= "</div>";
        $output .= "<div class='featured-content'>";

        $output .= "<h3 class='featured-title text-uppercase'>{$icon}$title</h3>";

        if($content!="") {
        $output .= "<p class='featured-desc'>$content</p>";
        }
        if($link!="") {
            $output .= "<a class='btn btn-md btn-minimal' href='$link'>$button_text</a>";
        }
        if($lightbox!="") {
            if($video=='' && $image!='') {
                $output .= "<a href='$image' class='featured-lightbox-link'><i class='fa fa-image'></i>";
            } elseif($video!="") {
                if(strpos($video,'vimeo') !== false) {
                    $video_type = 'vimeo';
                } else {
                    $video_type = 'youtube';
                }
                $output .= "<a data-rel='$video_type' href='$video' class='featured-lightbox-link'><svg><use xlink:href='#featured-video-dark'></use></svg>";
            }
            $output .= '</a>';
        }
        $output .= "</div>";
        $output .= "</div>";

        return $output;
    }
}
/* END Featured content */
/* Featured Horizontal content */
if(!function_exists('anps_featured_horizontal_content_func')) {
    function anps_featured_horizontal_content_func($atts, $content) {
        extract( shortcode_atts( array(
            'title' => '',
            'image_u' => '',
            'link' => '',
        ), $atts ) );

        $image = '';
        $link_el_start = '';
        $link_el_end = '';

        if($image_u) {
            $image = wp_get_attachment_image_src($image_u, 'full');
            $image = $image[0];
        }

        if( $link ) {
            $link_el_start = "<a href='$link'>";
            $link_el_end = "</a>";
        }

        $output = "<div class='featured-horizontal'>";
            $output .= "<div class='featured-horizontal-header'>";
                $output .= "$link_el_start<img alt='$title' src='$image'>$link_el_end";
            $output .= "</div>";
            $output .= "<div class='featured-horizontal-content'>";
                $output .= "$link_el_start<h3 class='featured-horizontal-title'>$title</h3>$link_el_end";
                $output .= "<p class='featured-horizontal-desc'>$content</p>";
            $output .= "</div>";
        $output .= "</div>";

        return $output;
    }
}
/* END Featured Horizontal content */
/* Gallery */
if(!function_exists('anps_gallery_slider_func')) {
    function anps_gallery_slider_func($atts, $content) {
        extract( shortcode_atts( array(
            'images' => ''
        ), $atts ) );
        $explode_images = explode(',', $images);
        $output = '';
        $output .= '<div class="gallery-fs">';
        /* First image */
        $output .= '<figure>';
        $image_src_first = wp_get_attachment_image_src($explode_images[0], 'full');
        $image_title_first = get_the_title($explode_images[0]);
        $output .= "<img src='$image_src_first[0]' alt='$image_title_first'>";
        $output .= "<figcaption>$image_title_first</figcaption>";
        $output .= '</figure>';
        /* If there is more than 1 image, thumbnails gallery code */
        if(count($explode_images)>1) {
            $output .= '<div class="gallery-fs-nav">';
            $output .= '<button class="gallery-fs-fullscreen"><i class="fa fa-arrows-alt"></i></button>';
            $output .= '</div>';

            $output .= '<div class="gallery-fs-thumbnails gallery-fs-thumbnails-col-5 owl-carousel">';
            $i=0;
            foreach($explode_images as $item) {
                $image_src = wp_get_attachment_image_src($item, 'full');
                $image_title = get_the_title($item);
                $class = '';
                if($i==0) {
                    $class = ' class="selected"';
                }
                $output .= "<a href='$image_src[0]' title='$image_title'$class>";
                $output .= "<img alt='$image_title' src='$image_src[0]'>";
                $output .= '</a>';
                $i++;
            }
            $output .= '</div>';
        }
        $output .= '</div>';
        return $output;
    }
}
/* END Gallery */
/* Google maps */
if(!function_exists('anps_google_maps_func')) {
    $google_maps_counter = 0;
    function anps_google_maps_func( $atts,  $content ) {
        global $google_maps_counter;
        $google_maps_counter++;
        extract( shortcode_atts( array(
            'zoom'     => '15',
            'scroll'   => '',
            'height'   => '550',
            'map_type' => 'ROADMAP',
            'style'   => ''
        ), $atts ) );

        if(isset($style) && $style!='') {
            $style = str_replace('``', '"', $style);
            $style = str_replace('`{`', '[', $style);
            $style = str_replace('`}`', ']', $style);
            $style = str_replace('`', '', $style);
            $style = str_replace('<br />', '', $style);
        } else {
            $style = '[{"featureType":"landscape","stylers":[{"saturation":-100},{"lightness":65},{"visibility":"on"}]},{"featureType":"poi","stylers":[{"saturation":-100},{"lightness":51},{"visibility":"simplified"}]},{"featureType":"road.highway","stylers":[{"saturation":-100},{"visibility":"simplified"}]},{"featureType":"road.arterial","stylers":[{"saturation":-100},{"lightness":30},{"visibility":"on"}]},{"featureType":"road.local","stylers":[{"saturation":-100},{"lightness":40},{"visibility":"on"}]},{"featureType":"transit","stylers":[{"saturation":-100},{"visibility":"simplified"}]},{"featureType":"administrative.province","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"labels","stylers":[{"visibility":"on"},{"lightness":-25},{"saturation":-100}]},{"featureType":"water","elementType":"geometry","stylers":[{"hue":"#ffff00"},{"lightness":-25},{"saturation":-97}]}]';
        }
        $scroll_option = "true";
        if($scroll==true) {
            $scroll_option = "false";
        }
        wp_enqueue_script('gmap3_link');
        wp_enqueue_script('gmap3');

        return "<div data-styles='{$style}' class='map' id='map$google_maps_counter' style='height: {$height}px;' data-type='$map_type' data-zoom='$zoom' data-scroll='{$scroll_option}' data-markers='" . do_shortcode($content) . "'></div>";
    }
}
//google maps item
if(!function_exists('anps_google_maps_item_func')) {
    function anps_google_maps_item_func($atts, $content) {
        extract( shortcode_atts( array(
            'location'      => '',
            'pin'           => '',
            'pin_url'           => '',
            'marker_center' => '',
        ), $atts) );

        $content = preg_replace('/[\n\r]+/', "", $content);
        $content = str_replace('"', '\"', $content);
        if(isset($pin) && $pin!="") {
            $pin_icon = wp_get_attachment_image_src($pin, 'full');
            $pin_icon = $pin_icon[0];
        } else if(isset($pin_url) && $pin_url!="") {
            $pin_icon = $pin_url;
        } else {
            $pin_icon = get_template_directory_uri()."/images/marker.png";
        }

        return '{ "address": "' . $location . '",  "center": "' . $marker_center . '", "data": "' . $content . '", "options": { "icon": "' . $pin_icon . '" } }|';
    }
}
/* END Google maps */
/* Heading */
if(!function_exists('anps_heading_func')) {
    function anps_heading_func( $atts,  $content ) {
        extract( shortcode_atts( array(
            'size' => '1',
            'heading_class' => "heading",
            'subtitle' => '',
            'h_class' => '',
            'h_id' => '',
            'heading_style' => 'style-1',
            'color' => '',
            'subtitle_color' => ''
        ), $atts ) );
        $id = '';
        if($h_id) {
            $id = " id='".$h_id."'";
        }
        switch($heading_class) {
            case "content_heading" : $head_class = " class='heading-content $h_class $heading_style'"; break;
            case "heading" : $head_class = " class='heading-middle $h_class $heading_style'"; break;
            case "style-3" : $head_class = " class='heading-left $h_class $heading_style'"; break;
        }
        /* heading color */
        $color_h = '';
        if(isset($color)&&$color!='') {
            $color_h = " style='color: $color'";
        }

        /* Subtitle color */
        if(isset($subtitle_color)&&$subtitle_color!='') {
            $subtitle_color = " style='color: $subtitle_color'";
        }

        /* Subtitle */
        if( $subtitle != '' ) {
            $subtitle = '<em class="heading-subtitle"' . $subtitle_color . '>' . $subtitle . '</em>';
        }

        if( $heading_style == 'divider-modern' ) {
            return '<h'.$size.$head_class.' '.$id.$color_h.'><span>' . $subtitle . $content . '</span></h'.$size.'>';
        } else {
            return '<h'.$size.$head_class.' '.$id.$color_h.'><span>' . $content . $subtitle . '</span></h'.$size.'>';
        }
    }
}
/* END Heading */
/* Icon shortcode */
if(!function_exists('anps_icon_func')) {
    function anps_icon_func($atts, $content) {
            extract( shortcode_atts( array(
                'url' => '',
                'target' => '_self',
                'icon' => '',
                'title' => '',
                'position' => ''
            ), $atts ) );

        $icon_content = "";
        $icon_content .= "<div class='icon $position'>";
        $icon_content .= "<div class='icon-header'>";
        $icon_content .= "<div class='icon-media'><i class='fa $icon'></i></div>";
        $icon_content .= "<h3 class='icon-title text-uppercase'>$title</h3>";
        $icon_content .= "</div>";
        $icon_content .= "<p>$content</p>";
        if($url!='') {
            $icon_content .= "<a href='".esc_url($url)."' target='$target' class='btn btn-md btn-minimal'>".esc_html__('Read more', 'industrial')."</a>";
        }
        $icon_content .= "</div>";
        return $icon_content;
    }
}
/* END Icon */
/* List */
if(!function_exists('anps_list_func')) {
    $list_number = 0;
    function anps_list_func($atts, $content) {
        extract( shortcode_atts( array(
            'class' => ''
        ), $atts ) );

        global $list_number;

        if( $class == "number" ) {
            $list_number = true;
            $return = "<ol class='list'>".do_shortcode($content)."</ol>";
            $list_number = false;
            return $return;
        }
        return "<ul class='list list-".$class."'>".do_shortcode($content)."</ul>";
    }
}
//list item
if(!function_exists('anps_list_item_func')) {
    function anps_list_item_func($atts, $content) {
        global $list_number;
        if($list_number) {
            return "<li><span>".$content."</span></li>";
        } else {
            return "<li>".$content."</li>";
        }
    }
}
/* END List */
/* Logos */
if(!function_exists('anps_logos_func')) {
    function anps_logos_func($atts, $content) {
        extract( shortcode_atts( array(
        'in_row' => '6',
        'style' => 'style-1'
        ), $atts ) );

    $logos_array = explode("[/logo]", $content);
    foreach($logos_array as $key=>$item) {
        if($item=="") {
            unset($logos_array[$key]);
        }
    }
    $data_col = "";
    $logos_class = "";

    $count_logos = count($logos_array);
    if ($style == 'style-2') {
        if ($count_logos > $in_row ) {
           $data_col = "data-col=" . $in_row;
           $logos_class = 'owl-carousel general style-2';
        } else {
            $logos_class = 'style-2';
        }
    } else if ($style == 'style-1') {
        $logos_class = "clients-col-".$in_row;
    }

    $return = "<div class='logos-wrapper $style'>";
    $return .= "<ul class='clients ". $logos_class ."' ".$data_col.">";

    $i = 0;
    foreach($logos_array as $item) {
        if($i%$in_row==0 && $i!=0 && $style == 'style-1') {
                $return .= "</ul><ul class='clients ". $logos_class ."'>".do_shortcode($item."[/logo]");
        } else {
            $return .= do_shortcode($item."[/logo]");
        }
        $i++;
    }
    $return .= "</ul></div>";
    return $return;
    }
}
//single logo
if(!function_exists('anps_logo_func')) {
    function anps_logo_func($atts, $content) {
        extract( shortcode_atts( array(
            'url' => '',
            'alt' => '',
            'image_u' => ''
        ), $atts ) );
        if($image_u) {
            $content = wp_get_attachment_image_src($image_u, 'full');
            $content = $content[0];
        }
        if($url) {
            return "<li class='client'><a href='".esc_url($url)."' target='_blank'><img src='".$content."' alt='".$alt."'></a></li>";
        } else {
            return "<li class='client'><span><img src='".esc_url($content)."' alt='".$alt."'></span></li>";
        }
    }
}
/* END Logos */
/* Portfolio */
if(!function_exists('anps_portfolio_func')) {
    function anps_portfolio_func($atts, $content) {
        extract( shortcode_atts( array(
                'filter' => 'on',
                'columns' => '3',
                'category'=> '',
                'orderby' => '',
                'order' => '',
                'per_page' => -1,
                'mobile_class' => '2'
            ), $atts ) );
        wp_enqueue_script('anps-isotope');

        $tax_query='';
        $parent_cat = "";
        if($category && $category!='All') {
            $parent_cat = $category;
            $tax_query = array(
                array(
                    'taxonomy' => 'portfolio_category',
                    'field' => 'id',
                    'terms' => (int)$category
                )
           );
        }
        $args = array(
            'post_type' => 'portfolio',
            'orderby' => $orderby,
            'order' => $order,
            'showposts' => $per_page,
            'tax_query' => $tax_query
        );
        $portfolio_posts = new WP_Query( $args );

        /*desktop-class*/
        $mdclass = " col-md-4";
        if($columns=="2") {
            $mdclass = " col-md-6";
        }

        /* Mobile class */
        $m_class = " col-xs-6";
        if($mobile_class=="1") {
            $m_class = " col-xs-12";
        }

        /* Portfolio isotope filter */
        $portfolio_data = "";
        $portfolio_data .= '<div class="projects">';
        if($filter=="on") {
            $portfolio_data .= "<ul class='filter'>";
            $portfolio_data .= '<li><button data-filter="*" class="selected">'.esc_html__("All projects", 'industrial')."</button></li>";
            $filters = get_terms('portfolio_category', "orderby=none&hide_empty=true&parent=$parent_cat");
            foreach ($filters as $item) {
                $portfolio_data .= '<li><button data-filter="' . strtolower(str_replace(" ", "-", $item->name)) . '">' . $item->name . '</button></li>';
            }
            $portfolio_data .= "</ul>";
        }
        /* Portfolio isotope filter enabled posts */
        $portfolio_data .= "<div class='row projects-content'>";
        while($portfolio_posts->have_posts()) :
            $portfolio_posts->the_post();
            $portfolio_cat = "";
            if (get_the_terms(get_the_ID(), 'portfolio_category')) {
                $first_item = false;
                foreach (get_the_terms(get_the_ID(), 'portfolio_category') as $cat) {
                    if($first_item) {
                        $portfolio_cat .= " ";
                    }
                    $first_item = true;
                    $portfolio_cat .= strtolower(str_replace(" ", "-", $cat->name));
                }
            }
            $image_class = 'anps-portfolio';
            if(has_post_thumbnail(get_the_ID())) {
                $image = get_the_post_thumbnail(get_the_ID(), $image_class);
            } elseif(get_post_meta(get_the_ID(), $key ='gallery_images', $single = true )) {
                $exploded_images = explode(',',get_post_meta(get_the_ID(), $key ='gallery_images', $single = true ));
                $image =  wp_get_attachment_image($exploded_images[0], $image_class);
            }
            $portfolio_data .= "<div class='projects-item ".$portfolio_cat.$mdclass.$m_class."'>";
            $portfolio_data .= "<div class='projects-item-wrap'>";
            if(isset($image)&&$image!="") {
                $portfolio_data .= $image;
            }
            $portfolio_data .= '<div class="project-hover">';
            $portfolio_data .= "<h3 class='project-title text-uppercase'>".get_the_title()."</h3>";
            $portfolio_data .= "<p class='project-desc'>".get_the_excerpt()."</p>";
            $portfolio_data .= "<a class='btn btn-md' href='".get_permalink()."'>".esc_html__('Read more', 'industrial')."</a>";
            $portfolio_data .= "</div>";
            $portfolio_data .= "</div>";
            $portfolio_data .= "</div>";
        endwhile;
        wp_reset_postdata();
        $portfolio_data .= "</div>";
        $portfolio_data .= "</div>";
        return $portfolio_data;
    }
}
/* END Portfolio */
/* Quote */
if(!function_exists('anps_quote_func')) {
    function anps_quote_func( $atts,  $content ) {
        extract( shortcode_atts( array(
            'style' => "style-1"
        ), $atts ) );
        $style_class = '';
        if($style) {
            $style_class = " class='$style'";
        }
        return "<blockquote$style_class><p>$content</p></blockquote>";
    }
}
/* END Quote */
/* Recent blog slider */
if(!function_exists('anps_recent_blog_func')) {
    function anps_recent_blog_func($atts, $content) {
        extract( shortcode_atts( array(
            'recent_title' => "",
            'number' => '',
            'number_in_row' => "3",
            'slider' => '1',
            'content_length' => '130',
            'cat_ids' => '',
            'style' => 'default'
        ), $atts ) );

        $recent_new_class = 'recent-news';

        if( $style == 'minimal-light' ) {
            $recent_new_class .= ' recent-news-light';
        }

        $args = array(
            'posts_per_page'   => $number,
            'cat'              => $cat_ids,
            'orderby'          => "date",
            'order'            => "DESC",
            'post_type'        => 'post',
            'post_status'      => 'publish',
        );
        $posts = new WP_Query( $args );

        if($posts->post_count <= intval($number_in_row)) {
            $slider = 0;
        }

        if($slider=='1') {
            if($number_in_row=='4') {
                $owl_att = "class='$recent_new_class recent-news-$style' data-owlcolumns='4'";
            } else {
                $owl_att = "class='$recent_new_class' data-owlcolumns='3'";
            }
        } else {
            $owl_att = "class='$recent_new_class row'";
            if($number_in_row=='4') {
                $columns_class = 'col-md-3';
            } else {
                $columns_class = 'col-md-4';
            }
        }


     $recent_post_text = '';

     $icon_prev_class = 'fa-angle-left';
     $icon_next_class = 'fa-angle-right';
     $owl_class = 'owl-nav';

     if($style == 'minimal-dark' || $style == 'minimal-light') {
         $icon_prev_class = 'fa-chevron-left';
         $icon_next_class = 'fa-chevron-right';
         $owl_class = 'owl-nav-style-2';
     }

     if($posts->have_posts()) :
        $recent_post_text .= "<div $owl_att>";
        if($style == 'default' || $recent_title != '') {
            if($slider=='1') :
                $recent_post_text .= '<div class="row">';
            endif;
            $recent_post_text .= '<div class="col-md-12">';
            if($recent_title != '') {
                $recent_post_text .= "<h2 class='title'>$recent_title</h2>";
            }
            $recent_post_text .= '</div>';
        }
        if($slider=='1') :
            if($style == 'default' || $recent_title != '') {
                $recent_post_text .= '</div>';
            }
            if($style == 'minimal-dark' || $style == 'minimal-light') {
                $recent_post_text .= "<div class='owl-wrap'>";
            }
            $recent_post_text .= "<div class='owl-carousel'>";
        endif;
        while($posts->have_posts()) :
            $posts->the_post();
            if($slider=='0') :
                $recent_post_text .= "<div class='$columns_class'>";
            endif;

            if($style == 'minimal-dark' || $style == 'minimal-light') {
                $recent_post_text .= "<article class='post-minimal post-$style'>";
                if(get_the_post_thumbnail(get_the_ID())!="") {
                    $recent_post_text .= "<header>";
                    $recent_post_text .= get_the_post_thumbnail(get_the_ID(), 'post-thumb');
                    $recent_post_text .= "</header>";
                }
                $recent_post_text .= '<div class="post-minimal-wrap">';
                $recent_post_text .= "<a href='".get_permalink()."'><h3 class='post-minimal-title'>".get_the_title()."</h3></a>";
                $recent_post_text .= '<ul class="post-minimal-meta">';
                $recent_post_text .= "<li><i class='fa fa-calendar'></i><time datetime='".get_the_time('Y-m-d h:s')."'>".get_the_date()."</time></li>";
                $recent_post_text .= '</ul>';
                $recent_post_text .= '</div>';
                $recent_post_text .= "</article>";
            } else {
                $recent_post_text .= "<article class='post'>";
                if(get_the_post_thumbnail(get_the_ID())!="") {
                    $recent_post_text .= "<header>";
                    $recent_post_text .= get_the_post_thumbnail(get_the_ID(), 'post-thumb');
                    $recent_post_text .= "</header>";
                }
                $recent_post_text .= "<a href='".get_permalink()."'><h3 class='post-title'>".get_the_title()."</h3></a>";
                $recent_post_text .= '<ul class="post-meta">';
                $recent_post_text .= "<li><i class='fa fa-calendar'></i><time datetime='".get_the_time('Y-m-d h:s')."'>".get_the_date()."</time></li>";
                $recent_post_text .= "<li><i class='fa fa-commenting-o'></i>".get_comments_number()." ".esc_html__("comments", 'industrial')."</li>";
                $recent_post_text .= '</ul>';
                $recent_post_text .= '<div class="post-content">';
                $recent_post_text .= "<div>".apply_filters('the_excerpt', mb_substr(get_the_excerpt(), 0, $content_length))."</div>";
                $recent_post_text .= "<a href='".get_permalink()."' class='btn btn-md btn-gradient btn-shadow'>".esc_html__('Read More', 'industrial')."</a>";
                $recent_post_text .= '</div>';
                $recent_post_text .= "</article>";
            }
            if($slider=='0') :
                $recent_post_text .= '</div>';
            endif;
        endwhile;
        if($slider=='1') :
            $recent_post_text .= "</div>";
            $recent_post_text .= "<div class='$owl_class text-right'>";
            $recent_post_text .= "<button class='owlprev'><i class='fa $icon_prev_class'></i></button>";
            $recent_post_text .= "<button class='owlnext'><i class='fa $icon_next_class'></i></button>";
            if($style == 'minimal-dark' || $style == 'minimal-light') {
                $recent_post_text .= '</div>';
            }
            $recent_post_text .= "</div>";
        endif;
        $recent_post_text .= "</div>";
     endif;
     wp_reset_postdata();
     return $recent_post_text;
    }
}
/* END Recent blog slider */
/* Recent portfolio slider */
if(!function_exists('anps_recent_portfolio_func')) {
    function anps_recent_portfolio_func($atts, $content) {
        extract( shortcode_atts( array(
            'recent_title' => "",
            'number' => '',
            'number_in_row' => "3",
            'category'=> '',
            'orderby' => 'post_date',
            'order' => 'DESC',
            'hide_filter' => ''
        ), $atts ) );
        $tax_query='';
        if($category && $category!='0') {
            $tax_query = array(
                array(
                    'taxonomy' => 'portfolio_category',
                    'field' => 'id',
                    'terms' => (int)$category
                )
           );
        }

        $args = array(
            'post_type' => 'portfolio',
            'orderby' => $orderby,
            'order' => $order,
            'showposts' => $number,
            'tax_query' => $tax_query
        );
        $portfolio_posts = new WP_Query( $args );

        /* 3 or 4 columns */
        $projects_class = 'col-md-4';
        if($number_in_row==4) {
            $projects_class = 'col-md-3';
        }

        $portfolio_data = '';
        $portfolio_data .= '<div class="projects projects-recent">';
        /* Header */
        $portfolio_data .= '<div class="projects-header clearfix">';

        /* Title */
        if($recent_title) {
            $portfolio_data .= "<h2 class='title projects-title visible-lg pull-left'>".$recent_title."</h2>";
        }

        /* Filter */
        $filter_class = 'filter filter-dark pull-right';
        if($hide_filter == 'true') {
            $filter_class .= ' filter-hidden';
        }
        $portfolio_data .= '<ul class="' . $filter_class . '">';
        $portfolio_data .= '<li><button data-filter="*" class="selected">'.esc_html__('All projects', 'industrial').'</button></li>';
        $filters = get_terms('portfolio_category', "orderby=none&hide_empty=true");
        foreach ($filters as $item) {
            $portfolio_data .= '<li><button data-filter="' . $item->slug . '">' . $item->name . '</button></li>';
        }
        $portfolio_data .= '</ul>';

        $portfolio_data .= '</div>';
        /* END Header */
        /* Content */
        $portfolio_data .= '<div class="row projects-content">';
        while($portfolio_posts->have_posts()) :
            $portfolio_posts->the_post();
            $portfolio_cat = "";
            if (get_the_terms(get_the_ID(), 'portfolio_category')) {
                $first_item = false;
                foreach (get_the_terms(get_the_ID(), 'portfolio_category') as $cat) {
                    if($first_item) {
                        $portfolio_cat .= " ";
                    }
                    $first_item = true;
                    $portfolio_cat .= $cat->slug;
                }
            }
            if(has_post_thumbnail(get_the_ID())) {
                $image = get_the_post_thumbnail(get_the_ID(), 'post-thumb');
            }
            elseif(get_post_meta(get_the_ID(), $key ='gallery_images', $single = true )) {
                $exploded_images = explode(',',get_post_meta(get_the_ID(), $key ='gallery_images', $single = true ));
                $image_url = wp_get_attachment_image_src($exploded_images[0], array(360, 267));
                $image = "<img src='".$image_url[0]."' />";
            }
            $portfolio_data .= "<div class='projects-item col-xs-6 $projects_class $portfolio_cat'>";
            $portfolio_data .= '<div class="projects-item-wrap">';
            $portfolio_data .= $image;
            $portfolio_data .= '<div class="project-hover">';
            $portfolio_data .= "<h3 class='project-title text-uppercase'>".get_the_title()."</h3>";
            $portfolio_data .= "<p>".get_the_excerpt()."</p>";
            $portfolio_data .= "<a class='btn btn-md' href='".get_permalink()."'>".esc_html__('Read More', 'industrial')."</a>";
            $portfolio_data .= '</div>';
            $portfolio_data .= '</div>';
            $portfolio_data .= '</div>';
        endwhile;
        wp_reset_postdata();
        $portfolio_data .= '</div>';
        /* END Content */
        /* Buttons left/right */
        $portfolio_data .= '<div class="projects-pagination">';
        $portfolio_data .= '<button class="prev"><i class="fa fa-angle-left"></i></button>';
        $portfolio_data .= '<button class="next"><i class="fa fa-angle-right"></i></button>';
        $portfolio_data .= '</div>';
        /* END Buttons left/right */
        $portfolio_data .= '</div>';
        return $portfolio_data;
    }
}
/* END Recent portfolio slider */
/* Subscribe */
if(!function_exists('anps_subscribe_func')) {
    function anps_subscribe_func($atts, $content) {
        extract(shortcode_atts( array(
                'title' => '',
                'input_bg_color' => '',
                'title_color' => ''
            ), $atts ) );
        //description = $content

        $input_colors = '';

        if($title_color != '') {
            $input_colors .= "color: $title_color;";
            $title_color = " style='color: $title_color;'";
        }

        if($input_bg_color != '') {
            $input_colors .= "background-color: $input_bg_color;";
            $input_bg_color = " style='background-color: $input_bg_color;'";
        }

        if($input_colors != '') {
            $input_colors = " style='$input_colors'";
        }

        $return = '<div class="subscribe">';
        $return .= do_shortcode("[newsletter_form default_css='false'$input_colors][newsletter_field name='email' label='Submit your email'][/newsletter_form]");
        $return .= '<div class="subscribe-content">';
        $return .= "<div class='subscribe-title'$title_color>$title</div>";
        $return .= "<div class='subscribe-description'>$content</div>";
        $return .= '</div>';
        $return .= '</div>';
        return $return;
    }
}
/* END Subscribe */
/* Social icons */
if(!function_exists('anps_social_icons_func')) {
    function anps_social_icons_func($atts, $content) {
        extract( shortcode_atts( array(
                'class' => ''
            ), $atts ) );
        $class_s = 'social';
        if($class=='transparent') {
            $class_s = 'social transparent';
        } elseif($class=='border') {
            $class_s = 'social border';
        }
        return "<ul class='$class_s'>".do_shortcode($content)."</ul>";
    }
}
//single social icon
if(!function_exists('anps_social_icon_item_func')) {
    function anps_social_icon_item_func($atts, $content) {
        extract( shortcode_atts( array(
                'url' => '#',
                'icon' => '',
                'target' => '_blank'
            ), $atts ) );
            return "<li><a href='".esc_url($url)."' target='".$target."'><i class='fa ".$icon."'></i></a></li>";
    }
}
/* END Social icons */
/* Table */
if(!function_exists('anps_table_func')) {
    function anps_table_func( $atts,  $content ) {
        extract( shortcode_atts( array(
                'striped' => '',
                'bordered' => '',
                'head_style' => ''
            ), $atts ) );
        $striped_class = '';
        if($striped) {
            $striped_class = ' table-striped';
        }
        $bordered_class = '';
        if($bordered) {
            $bordered_class = ' table-bordered';
        }
        if($head_style) {
            $content = str_replace('[table_head]', '[table_head class="bg-primary"]', $content);
        }
        return "<div class='table-responsive'><table class='table$striped_class$bordered_class'>".do_shortcode($content)."</table></div>";
    }
}
//thead
if(!function_exists('anps_table_thead_func')) {
    function anps_table_thead_func( $atts,  $content ) {
        extract( shortcode_atts( array(
                'class' => ''
            ), $atts ) );
        $head_class = '';
        if($class) {
            $head_class = " class='$class'";
        }
        return "<thead $head_class>".do_shortcode($content)."</thead>";
    }
}
//tbody
if(!function_exists('anps_table_tbody_func')) {
    function anps_table_tbody_func( $atts,  $content ) {
        return "<tbody>".do_shortcode($content)."</tbody>";
    }
}
//tfoot
if(!function_exists('anps_table_tfoot_func')) {
    function anps_table_tfoot_func( $atts,  $content ) {
        return "<tfoot>".do_shortcode($content)."</tfoot>";
    }
}
//data row
if(!function_exists('anps_table_row_func')) {
    function anps_table_row_func( $atts,  $content ) {
        return "<tr>".do_shortcode($content)."</tr>";
    }
}
//data column
if(!function_exists('anps_table_td_func')) {
    function anps_table_td_func( $atts,  $content ) {
        return "<td>".do_shortcode($content)."</td>";
    }
}
//data head column
if(!function_exists('anps_table_th_func')) {
    function anps_table_th_func( $atts,  $content ) {
        return "<th>".do_shortcode($content)."</th>";
    }
}
/* END Table */
/* Team with description*/
if(!function_exists('anps_team_with_description_func')) {
    function anps_team_with_description_func($atts, $content) {
        extract(shortcode_atts(array(
            'ids' => '',
            'content_title' => '',
            'button_text' => '',
            'button_url' => '#',
            'target' => '_self',
            'title_color' => '',
            'desc_color' => '',
        ), $atts));
        /*
         * Description = $content
         */

        /* Colors */

        if($title_color != '') {
            $title_color = " style='color: $title_color;'";
        }

        if($desc_color != '') {
            $desc_color = " style='color: $desc_color;'";
        }

        /* Select team by member id */
        $array_ids = '';
        $order_by = 'date';
        if($ids) {
            $array_ids = explode(',', $ids);
            $array_ids = array_map('trim', $array_ids);
            $order_by = 'post__in';
        }

        $args = array(
            'post_type' => 'team',
            'post__in' => $array_ids,
            'posts_per_page' => '3',
            'orderby' => $order_by
        );

        $team_posts = new WP_Query( $args );
        $team_data = '<div class="team-desc team-desc-size-' . count($team_posts->posts) . '">';
        $team_data .= '<div class="team-desc-main">';
        $team_data .= "<div class='team'>";
        while($team_posts->have_posts()):
            $team_posts->the_post();
            $subtitle = get_post_meta( get_the_ID(), $key = 'anps_team_subtitle', $single = true );
            $team_data .= "<div class='member'>";
            $team_data .= "<div class='member-wrap'>";
            $team_data .= "<div class='member-image'>";
            $team_data .= "<a href='".get_permalink()."'>";
            $team_data .= get_the_post_thumbnail(get_the_ID(), 'anps-team-desc');
            $team_data .= '</a>';
            $team_data .= "</div>";
            $team_data .= "<a href='".get_permalink()."'>";
            $team_data .= "<h3 class='member-name'>".get_the_title()."</h3>";
            $team_data .= '</a>';
            if( $subtitle != '' ) {
                $team_data .= "<span class='member-title'>".$subtitle."</span>";
            }
            $team_data .= "</div></div>";
        endwhile;
        wp_reset_postdata();
        $team_data .= "</div>";
        $team_data .= "</div>";
        $team_data .= '<div class="team-desc-side">';
        if( $content_title != '' ) {
            $team_data .= "<h3 class='team-desc-title'$title_color>$content_title</h3>";
        }
        if( $content != '' ) {
            $team_data .= "<div class='team-desc-description'$desc_color>$content</div>";
        }
        if( $button_text != '' ) {
            $team_data .= "<a href='$button_url' target='$target' class='btn team-desc-btn'>$button_text</a>";
        }
        $team_data .= "</div>";
        $team_data .= "</div>";
        return $team_data;
    }
}
/* END Team with description */
/* Team */
if(!function_exists('anps_team_func')) {
    function anps_team_func($atts, $content) {
        extract( shortcode_atts( array(
            'columns' => '4',
            'ids' => '',
            'number_items' => '-1'
        ), $atts ) );

        /* Select team by member id */
        $array_ids = '';
        $order_by = 'date';
        if($ids) {
            $array_ids = explode(',', $ids);
            $array_ids = array_map('trim', $array_ids);
            $order_by = 'post__in';
        }

        $args = array(
            'post_type' => 'team',
            'showposts' => $number_items,
            'columns' => $columns,
            'post__in' => $array_ids,
            'orderby' => $order_by
        );

        $team_posts = new WP_Query( $args );
        $team_data = "<div class='team team-col-$columns'>";
        while($team_posts->have_posts()) :
            $team_posts->the_post();
            $subtitle = get_post_meta( get_the_ID(), $key = 'anps_team_subtitle', $single = true );
            $team_data .= "<div class='member'>";
            $team_data .= "<div class='member-wrap'>";
            $team_data .= "<div class='member-image'>";
            $team_data .= "<a href='".get_permalink()."'>";
            $team_data .= get_the_post_thumbnail(get_the_ID(), 'anps-team');
            $team_data .= '</a>';
            $team_data .= "</div>";
            $team_data .= "<a href='".get_permalink()."'>";
            $team_data .= "<h3 class='member-name'>".get_the_title()."</h3>";
            $team_data .= '</a>';
            if( $subtitle != '' ) {
                $team_data .= "<span class='member-title'>".$subtitle."</span>";
            }
            $team_data .= "<p class='member-desc'>".get_the_excerpt()."</p>";
            $team_data .= "</div></div>";
        endwhile;
        wp_reset_postdata();
        $team_data .= "</div>";
        return $team_data;
    }
}
/* END Team */
/* Testimonials */
if(!function_exists('anps_testimonials')) {
    function anps_testimonials($atts,  $content) {

        extract( shortcode_atts( array(
            'testimonialheading' => '',
            'style' => 'style-1',
        ), $atts ) );

        $icon_prev_class = 'fa-angle-left';
        $icon_next_class = 'fa-angle-right';
        $owl_class = 'owl-nav';

        if($style == 'style-2') {
            $icon_prev_class = 'fa-chevron-left';
            $icon_next_class = 'fa-chevron-right';
            $owl_class = 'owl-nav-style-2';
        }

        $testimonials_number = substr_count($content, "[testimonial");
        $data_return = '';
        $data_return .= '<div class="testimonials testimonials-' . $style . '">';
        $data_return .= "<div class='testimonials-header'>";
        if($testimonialheading != '') {
            $data_return .= "<h3 class='title'>".$testimonialheading."</h3>";
        }
        $data_return .= '</div>';
        $data_return .= "<div class='testimonials-outer-wrap'>";
        $data_return .= '<ul class="testimonial-wrap owl-carousel">';
        $data_return .= do_shortcode($content);
        $data_return .= '</ul>';
        /* Slider left/right navigation buttons */
        if($testimonials_number>1) {
            $data_return .= '<div class="' . $owl_class . '">';
            $data_return .= '<button class="owlprev"><i class="fa ' . $icon_prev_class . '"></i></button>';
            $data_return .= '<button class="owlnext"><i class="fa ' . $icon_next_class . '"></i></button>';
            $data_return .= '</div>';
        }
        /* END Slider left/right navigation buttons */
        $data_return .= '</div>';
        $data_return .= '</div>';
        return $data_return;
    }
}
//testimonial item
if(!function_exists('anps_testimonial')) {
    function anps_testimonial($atts,  $content) {
        extract( shortcode_atts( array(
            'image' => '',
            'image_u' => '',
            'user_name' => '',
            'user_name_subtitle' => ''

        ), $atts ) );

		$grid_class = '';
        if($image_u) {
            $image = wp_get_attachment_image_src($image_u, 'full');
            $image = $image[0];
            $grid_class = 'col-xs-12 col-sm-6';
            $grid_class = '';
        }
        $data = '';
        $data .= '<li class="clearfix row">';
        $data .= '<div class="user pull-left">';
        if($image) {
        	$data .= '<div class="user-image">';
            $data .= "<img class='user-photo' src='".$image."' alt='".$user_name."'>";
            $data .= '</div>';
        }
        $data .= '<div class="content pull-left">';
        $data .= "<h3 class='name-user'>$user_name</h3>";

		if($user_name_subtitle) {
        	$data .= '<h4 class="jobtitle">'.$user_name_subtitle.'</h4>';
		}

        $data .= '<p>'.$content.'</p>';
        $data .= '</div>';
        $data .= '</div>';
        $data .= '</li>';
        return $data;
    }
}
/* END Testimonials */
/* Twitter */
if(!function_exists('anps_twitter_func')) {
    function anps_twitter_func($atts, $content) {

        extract( shortcode_atts( array(
                    'title' => '',
                    'color' => '',
                    'slug' => '',
                    'title_color' => '#000000',
                    'text_color' => '#7f7f7f',
                    'tw_number' => '3'
            ), $atts ) );

        $tw_number = intval($tw_number);
        if ( $tw_number <= 0 ) {
            $tw_number = 3;
        }
        if ( $tw_number > 20 ) {
            $tw_number = 20;
        }

       include_once WP_PLUGIN_DIR . '/anps_theme_plugin/twitter/TwitterAPIExchange.php';
        $settings = array(
            'oauth_access_token' => "1485322933-oo8YU1ZTz5E4Zt92hTTbCdJoZxIJIabghjnsPkX",
            'oauth_access_token_secret' => "RfXHN2OXMkBYp3IaEqrBmPhUYR2N61P8pyHf8QXqM",
            'consumer_key' => "Zr397FLlTFM4RVBsoLVgA",
            'consumer_secret' => "3Z2wNAG2vvunam2mfJATxnJcThnqw1qu02Xy8QlqFI"
        );
        $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
        $getfield = '?screen_name=' . $content . '&count=' . $tw_number;
        $requestMethod = 'GET';
        $twitter = new TwitterAPIExchange($settings);
        $tweets = json_decode($twitter->setGetfield($getfield)
                     ->buildOauth($url, $requestMethod)
                     ->performRequest());
        $return = '<section class="carousel anps-twitter text-center" >';
        if ($title != '') {
            $return .= '<h2 class="title" style="color:' . $title_color .'">'.$title.'</h2>';
        }
        $return .= '<div class="owl-carousel">';
        $j=0;
        foreach( $tweets as $tweet ) {
            if($j=="0") {
                $class_active = ' active';
            } else {
                $class_active = '';
            }
            $tweet_text = $tweet->text;
            $tweet_text = preg_replace('/http:\/\/([a-z0-9_\.\-\+\&\!\#\~\/\,]+)/i', '<a href="http://$1" target="_blank">http://$1</a>', $tweet_text); //replace links
            $tweet_text = preg_replace('/@([a-z0-9_]+)/i', '<a href="http://twitter.com/$1" target="_blank">@$1</a>', $tweet_text); //replace users
            $return .= '<div class="owl-item text-center" style="color:' . $text_color . '">' . $tweet_text . '</div>';
            $j++;
        }
        $return .= "</div>";

        $return .= '<div class="twitter-owl-nav owl-nav-pull-right">';
        $return .= '<button class="owlprev"><i class="fa fa-angle-left"></i></button>';
        $return .= '<button class="owlnext"><i class="fa fa-angle-right"></i></button>';
        $return .= '</div>';

        $return .= '</section>';
        return $return;
    }
}
/* END Twitter */

/* Timeline */
if(!function_exists('anps_timeline_func')) {
    function anps_timeline_func($atts, $content) {
        extract( shortcode_atts( array(), $atts ) );

        return '<div class="timeline">' . do_shortcode($content) . '</div>';
    }
}
/* END Timeline */

/* Timeline Item */
if(!function_exists('anps_timeline_item_func')) {
    function anps_timeline_item_func($atts, $content) {
        extract( shortcode_atts( array(
            'title' => '',
            'year'  => '2016'
        ), $atts ) );

        $return = '<div class="timeline-item">';
            $return .= '<div class="timeline-year">' . $year . '</div>';
            $return .= '<div class="timeline-content">';
                $return .= '<h3 class="timeline-title">' . $title . '</h3>';
                $return .= '<div class="timeline-text">' . $content . '</div>';
            $return .= '</div>';
        $return .= '</div>';

        return $return;
    }
}
/* END Timeline Item */
