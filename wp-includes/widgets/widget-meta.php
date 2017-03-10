
<?php
// Remove the default WP_Widget_Meta, register the new one
add_action( 'widgets_init', 'remove_meta_widget' );
function remove_meta_widget() {
    unregister_widget( 'WP_Widget_Meta' );
    register_widget( 'xbs_WP_Widget_Meta' );
}

// Custom Meta widget class
class xbs_WP_Widget_Meta extends WP_Widget {

   public function __construct() {
      $widget_ops = array(
         'classname' => 'widget_meta',
         'description' => __( 'My custom widget Meta.' ),
         'customize_selective_refresh' => true,
      );
      parent::__construct( 'meta', __( 'Meta' ), $widget_ops );
   }

   public function widget( $args, $instance ) {
      /** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
      $title = apply_filters( 'widget_title', empty($instance['title']) ? __( 'Meta' ) : $instance['title'], $instance, $this->id_base );

      echo $args['before_widget'];
      if ( $title ) {
         echo $args['before_title'] . $title . $args['after_title'];
      }
         ?>
         <ul class="textwidget">
           <?php wp_register(); ?>
           <li><?php wp_loginout(); ?></li>
         </ul>
         <?php
      echo $args['after_widget'];
   }

   public function update( $new_instance, $old_instance ) {
      $instance = $old_instance;
      $instance['title'] = sanitize_text_field( $new_instance['title'] );

      return $instance;
   }

   public function form( $instance ) {
      $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
      $title = sanitize_text_field( $instance['title'] );
?>
      <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
<?php
   }
}
