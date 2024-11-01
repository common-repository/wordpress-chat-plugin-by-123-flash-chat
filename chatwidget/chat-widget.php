<?php
/** Wordpress Widget Helper Class */
if (!class_exists('Chat_Widget')){
	abstract class Chat_Widget extends WP_Widget {
	    /***********************************************************
	    **  Abstract Functions
	    ***********************************************************/
	
	    /** Return the pugin id, ie: my-plugin */
	    abstract function w_id();
	    /** Return the pugin name, ie: My Plugin */
	    abstract function w_name();
	
	    /** Return the form content to show in admin dashboard */
	    abstract function w_form($instance);
	    /** Return the real widget content */
	    abstract function w_content($instance);
	    /** Return the plugin default options, a name=>value array, ie: array('title'=>'My Plugin Title') */
	    abstract function w_defaults();
	
	    /** Title field to retrieve from options */
	    function w_title_field(){ return 'title'; }
		/** Possibility to set a custom title */
		function w_title($instance){
			return $instance['title'];
		}
	
	    /***********************************************************
	    **  Static Functions
	    ***********************************************************/
	
	    /** Register the Widget using Wordpress Actions */
	    static function w_init($classname=null){
	        if (is_null($classname)){
	            $classname=get_called_class();
	        }
	        add_action( 'widgets_init', create_function('', 'return register_widget("'.$classname.'");') );
	    }
	
	    /***********************************************************
	    **  Wordpress Hooks
	    ***********************************************************/
	
	    /** Class constructor */
	    function Chat_Widget(){
	        $widget_ops=$this->w_defaults();
	        $this->WP_Widget($this->w_id(), $this->w_name(), $widget_ops);
	    }
	
	    /** Admin Dashboard Form */
		function form($instance) {
			$instance = $this->parse_args( $instance );
	        echo $this->w_form($instance);
		}
	
	    /** Widget Code */
		function widget($args, $instance) {
	        extract($args);
	        $tfld=$this->w_title_field();
	        echo $before_widget;
	        if (!empty($tfld)){
				$t=$this->w_title($instance);
	            $title = apply_filters('widget_title', empty($t) ? '&nbsp;' : $t, $instance, $this->id_base);
	            if ( $title ){ echo $before_title . $title . $after_title; }
	        }
	        echo '<div  class="'.$this->id_base.'-wrapper">'.$this->w_content($instance).'</div>';
	        echo $after_widget;
	    }
	    /** Called By Wordpress when saving settings */
		function update($new_instance, $old_instance) {
	        $instance = $old_instance;
	        $new_instance = $this->parse_args( $new_instance );
	        $def=$this->w_defaults();
	        foreach($new_instance as $k=>$v){
	            $instance[$k] = strip_tags($v);
	            if (empty($instance[$k]) && !empty($def[$k])){
	                $instance[$k]=$def[$k];
	            }
	        }
	        return $instance;
		}
	
	
	    /***********************************************************
	    **  Utility Functions
	    ***********************************************************/
	
	    /** Return an input type=text field ready for admin dashboard */
	    function w_form_inputset($instance, $name, $title=null){
	        if (is_null($title)) { $title=ucwords($name); }
	        return  '<label for="'.$this->get_field_id($name).'"  style=" display: block;">'.__($title).
	                '<input type="text" style="width:222px;" id="'.$this->get_field_id($name).'" name="'.$this->get_field_name($name).'" value="'.esc_attr($instance[$name]).'"/>'.
	                '</label>';
	    }
	
	    /** Return a textarea ready for admin dashboard */
	    function w_form_textarea($instance, $name, $title=null){
	        if (is_null($title)) { $title=ucwords($name); }
	        return  '<label for="'.$this->get_field_id($name).'"  style="line-height: 35px; display: block;">'.__($title).'</label>'.
	                '<textarea class="widefat" id="'.$this->get_field_id($name).'" name="'.$this->get_field_name($name).'">'.esc_attr($instance[$name]).'</textarea>';
	    }
	
	    /** Simplify wp_parse_args */
	    function parse_args($instance){
	        return wp_parse_args( (array)$instance, $this->w_defaults());
	    }
	
		/** jQuery like extend */
		function extend() {
			$args = func_get_args();
			$extended = array();
			if(is_array($args) && count($args)) {
				foreach($args as $array) {
					if(is_array($array)) {
						$extended = array_merge($extended, $array);
					}
				}
			}
			return $extended;
		}
	}
}