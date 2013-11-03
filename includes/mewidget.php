<?php
/*
Plugin Name: Meetup Events Widget
Plugin URI: http://vicentegarcia.com
Description: A simple widget for displaying open events from meetup.com
Author: Vicente García
Author URI: http://vicentegarcia.com
Author email: v@vicentegarcia.com
Version: 1.0
License: GPLv2
*/

class mewidget extends WP_Widget {
    // Constructor function
    function __construct() {
        // Define the widget:
        $mewidget_optionss = array('classname' => 'mewidget', 'description' => "Un sencillo widget para mostrar los eventos de tu ciudad y alrededores extraidos desde meetup.com" );
        parent::__construct('mewidget','Meetup Events Widget', $mewidget_optionss);

        wp_enqueue_style( $this->widgetName.'-styles', plugins_url( 'mewidget.css', __FILE__ ));
    }

    // Function that displays the widget content on the site frontend.
    function widget($args,$instance) {
        // Expand the passed in args into the function space - creates $before_widget, $after_widget, $before_title, $after_title
        extract($args);

         // display anything passed in the $before_widget parameter
        echo $before_widget;

        // Display the content
        ?>
        <aside id='mewidget'>
            <?php echo $this->get_events($instance["mew_country"],$instance["mew_city"],$instance["key_meetup"],$instance["mew_text"]);?>
        </aside>

        <?php
        // display anything passed in the $after_widget parameter
        echo $after_widget;
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance["mew_country"] = strip_tags($new_instance["mew_country"]);
        $instance["mew_city"] = strip_tags($new_instance["mew_city"]);
        $instance["key_meetup"] = strip_tags($new_instance["key_meetup"]);
        $instance["mew_text"] = strip_tags($new_instance["mew_text"]);
        return $instance;
    }

    function form($instance){
        // Formulario de opciones del Widget, que aparece cuando añadimos el Widget a una Sidebar
        ?>
         <p>
            <label for="<?php echo $this->get_field_id('mew_country'); ?>">Código país (ejp: es)</label>
            <input class="widefat" id="<?php echo $this->get_field_id('mew_country'); ?>" name="<?php echo $this->get_field_name('mew_country'); ?>" type="text" value="<?php echo esc_attr($instance["mew_country"]); ?>" />
            <p></p>
            <label for="<?php echo $this->get_field_id('mew_city'); ?>">Ciudad (ejp: madrid)</label>
            <input class="widefat" id="<?php echo $this->get_field_id('mew_city'); ?>" name="<?php echo $this->get_field_name('mew_city'); ?>" type="text" value="<?php echo esc_attr($instance["mew_city"]); ?>" />
            <p></p>
            <label for="<?php echo $this->get_field_id('key_meetup'); ?>">Clave (key meetup.com)</label>
            <input class="widefat" id="<?php echo $this->get_field_id('key_meetup'); ?>" name="<?php echo $this->get_field_name('key_meetup'); ?>" type="text" value="<?php echo esc_attr($instance["key_meetup"]); ?>" />
            <p></p>
            <label for="<?php echo $this->get_field_id('mew_text'); ?>">Términos relacionados con los eventos (separados por espacios)</label>
            <input class="widefat" id="<?php echo $this->get_field_id('mew_text'); ?>" name="<?php echo $this->get_field_name('mew_text'); ?>" type="text" value="<?php echo esc_attr($instance["mew_text"]); ?>" />
        </p>
         <?php
    }

    //Return events at $city
    public function get_events($country,$city,$keymeetup,$text) {
        $base = 'https://api.meetup.com';
        $parameters = array('key' => $keymeetup, 'sign' => 'true', 'city' => $city, 'country' => $country, 'text' => $text, 'order' => 'time');
        $path = '/2/open_events';

        $url = $base . $path . '?' . http_build_query($parameters);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept-Charset: utf-8"));
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array("charset: utf-8"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $content = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception("Fallo al recuperar  '" . $url . "' por el error ' " . $error . "'.");
        }

        $response = json_decode($content);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

         if ($status == 200 && isset($response) != false)
        {
            $eventos = '<div class="mewidgetbox">';
            $eventos .= '<h3 class="widgettitle">Próximos eventos</h3>';
            $eventos .= '<div class="mewidgetcontent">';

            $events =  $response->results;

            foreach ($events as $event) {
                $eventos .= '<div class="mewidgetsingle">';
                $eventos .= (!empty($event->event_url))? '<a href="'.$event->event_url.'" target="_blank">'.$event->name.'</a>' : '<strong>'.$event->name.'</strong>';
                $eventos .= (!empty($event->venue->address_1))? '<br>Lugar: '.$event->venue->address_1 : '';
                $eventos .= (!empty($event->venue->city))? ' - '.$event->venue->city : '';
                $eventos .= (!empty($event->time))? '<br>Fecha: ' . date('d/m/Y H:i', $event->time / 1000) : '';
                $eventos .= '</div>';

           }
           $eventos .= '</div>';
           $eventos .= '</div>';
        }

        return $eventos;
    }
}
