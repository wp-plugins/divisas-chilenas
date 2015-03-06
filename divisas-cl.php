<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/*
Plugin Name: Divisas Chilenas
Description: Despliega los valores de las principales divisas de Chile para la fecha actual obtenidos utilizando la API provista por mindicador.cl
Version: 1.0.0
Author: Gerson Apablaza
Author URI: http://gerson.cl
License: GPLv2
*/
/*  Copyright 2015  Gerson Apablaza  (email : gerson.eaa@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function agregar_css() {
    wp_register_style( 'divisas-cl-css', plugins_url('style.css', __FILE__) );
    wp_enqueue_style( 'divisas-cl-css' );
}
add_action( 'wp_enqueue_scripts', 'agregar_css' );


function desactivacion() {
	delete_option('divisas_cl_data');
}
register_deactivation_hook( __FILE__, 'desactivacion' );


/**
 * Definición de la clase Divisas_Chilenas
 */
class Divisas_Chilenas_Widget extends WP_Widget {
     
    public function __construct() {

    	parent::__construct(
	        'divisas_cl',
	        __('Divisas Chilenas', 'divisas-cl' ),
	        array (
	            'description' => __( 'Despliega los valores de las principales divisas de Chile para la fecha actual.', 'divisas-cl' )
	        )
	    );
    }

     
    public function form( $instance ) {
    
        //definimos valores por defecto para las variables
    	$instance = wp_parse_args( (array) $instance, array( 
    		'titulo' 	=> '',
            'class'     => '',
    		'uf' 		=> 1,
    		'dolar' 	=> 1,
            'utm'       => 1
    	));

    	$instance['titulo'] = esc_attr($instance['titulo']);
        $instance['class'] = esc_attr($instance['class']);

	    //generamos el formulario de configuración del plugin
	    ?>
	    <p>
	    	<label for="<?php echo $this->get_field_id('titulo'); ?>">Título:</label>
	        <input value="<?php echo $instance['titulo']; ?>" class="widefat" type="text" id="<?php echo $this->get_field_id('titulo'); ?>" name="<?php echo $this->get_field_name('titulo'); ?>">
	    </p>
	    <p>
	    	<label for="<?php echo $this->get_field_id('class'); ?>">Clase CSS adicional:</label>
	        <input value="<?php echo $instance['class']; ?>" class="widefat" type="text" id="<?php echo $this->get_field_id('class'); ?>" name="<?php echo $this->get_field_name('class'); ?>">
	    </p>
	    <p>
            <span>Divisas a mostrar:</span><br />
            <input class="checkbox" type="checkbox" name="<?php echo $this->get_field_name('uf'); ?>" id="<?php echo $this->get_field_id('uf'); ?>" <?php checked(isset($instance['uf']) ? 1 : 0); ?> />
            <label for="<?php echo $this->get_field_id('uf'); ?>">UF</label><br />
            <input class="checkbox" type="checkbox" name="<?php echo $this->get_field_name('dolar'); ?>" id="<?php echo $this->get_field_id('dolar'); ?>" <?php checked(isset($instance['dolar']) ? 1 : 0); ?> />
            <label for="<?php echo $this->get_field_id('dolar'); ?>">Dólar</label><br />
            <input class="checkbox" type="checkbox" name="<?php echo $this->get_field_name('utm'); ?>" id="<?php echo $this->get_field_id('utm'); ?>" <?php checked(isset($instance['utm']) ? 1 : 0); ?> />
            <label for="<?php echo $this->get_field_id('utm'); ?>">UTM</label><br />
        </p>
	    <?php
    }

     
    public function update( $new_instance, $old_instance ) {      

    	$instance = $old_instance;
	    $instance['titulo'] = strip_tags($new_instance['titulo']);
	    $instance['class'] = strip_tags($new_instance['class']);
	    $instance['uf'] = $new_instance['uf'];
	    $instance['dolar'] = $new_instance['dolar'];
        $instance['utm'] = $new_instance['utm'];
	    return $instance; 
    }

     
    public function widget( $args, $instance ) {

    	//extraemos los argumentos y obtenemos los valores
		extract($args, EXTR_SKIP);
		$titulo = empty($instance['titulo']) ? '' : apply_filters('widget_title', $instance['titulo']);
		$uf = $instance['uf'];
		$dolar = $instance['dolar'];
        $utm = $instance['utm'];

		$data = $this->consultarDivisas();

		//si es que se especificó una clase css personalizada la agregamos y seteamos el widget
		if ( !empty($instance['class']) ) {
			if( strpos($before_widget, 'class') === false ) {
				$before_widget = str_replace('>', 'class="'. $instance['class'] . '"', $before_widget);
			} else {
				$before_widget = str_replace('class="', 'class="'. $instance['class'] . ' ', $before_widget);
			}
		}
		echo (isset($before_widget) ? $before_widget : '');

        //seteamos el título si es que se especificó
		if (!empty($titulo)) {
			echo $before_title . $titulo . $after_title;
		}
		?>
		<ul>
			<?php if ($uf) { echo '<li class="divisas_cl-uf"><span class="divisas_cl_nombre">UF:</span><span class="divisas_cl_valor">'.dar_formato($data->uf->valor).'</span></li>'; } ?>
			<?php if ($dolar) { echo '<li class="divisas_cl-dolar"><span class="divisas_cl_nombre">Dólar:</span><span class="divisas_cl_valor">'.dar_formato($data->dolar->valor).'</span></li>'; } ?>
            <?php if ($utm) { echo '<li class="divisas_cl-utm"><span class="divisas_cl_nombre">UTM:</span><span class="divisas_cl_valor">'.dar_formato($data->utm->valor).'</span></li>'; } ?>
		</ul>
		<?php

		//seteamos el cierre del widget
		echo (isset($after_widget) ? $after_widget : '');
    }


    public function consultarDivisas() {

    	//si existen los datos en nuestra base de datos los obtenemos de ahí
    	if ($data = get_option('divisas_cl_data')) {
    		$info = json_decode($data);

    		$fecha_actual = date("Y-m-d");
    		$fecha_info = date("Y-m-d", strtotime($info->fecha));

    		//si es que la fecha de nuestros datos es la de hoy devolvemos los datos
    		if ($fecha_actual == $fecha_info) {
    			return $info;
    		
    		} else {
                //en caso contrario obtenemos los datos de una fuente externa
    			if ($this->actualizarDivisas()) {
	    			$data = get_option('divisas_cl_data');
	    			return json_decode($data);
	    		} else {
	    			return FALSE;	
	    		}
    		}
    	
    	} else {
            //si no existen los obtenemos de una fuenta externa
    		if ($this->actualizarDivisas()) {
    			$data = get_option('divisas_cl_data');
    			return json_decode($data);
    		} else {
    			return FALSE;	
    		}
    	}
    }


    public function actualizarDivisas() {

        $apiUrl = 'http://www.mindicador.cl/api';
        //Es necesario tener habilitada la directiva allow_url_fopen para usar file_get_contents
        if ( ini_get('allow_url_fopen') ) {
            $data = file_get_contents($apiUrl);
        } else {
            //De otra forma utilizamos cURL
            $curl = curl_init($apiUrl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($curl);
            curl_close($curl);
        }

        //si es que obtuvimos los datos los guardamos
        if (!empty($data)) {
        	update_option('divisas_cl_data', $data);
        	return TRUE;
        } else {
            //en caso contrario retornamos error
        	return FALSE;
        }
    }

}


function dar_formato($valor) {
    if (!empty($valor) && is_numeric($valor)) {
        return number_format($valor,2,',','.');
    } else {
        return "S/I";
    }
}


function registrar_widget() {
    register_widget( 'Divisas_Chilenas_Widget' );
}
add_action( 'widgets_init', 'registrar_widget' );


?>