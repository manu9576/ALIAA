<?php

/**
* Classe Poll_Widget
*/
class Poll_Widget extends WP_Widget
{
  /**
  * Constructeur
  */
  public function __construct()
  {
    parent::__construct('zero_poll', 'Poll', array('description' => 'Un formulaire pour les votes.'));
  }

  /**
  * Affichage du widget
  */
  public function widget($args, $instance)
  {
    //error_log("** 1 -widget\n",3,"/var/www/SiteWeb/WP_Test/php_log.txt");
    echo $args['before_widget'];
    echo $args['before_title'];
    echo apply_filters('widget_title', $instance['title']);
    echo $args['after_title'];

    // on detecte affiche le resultat si on reviens d'une requete poste avec un vote ou
    // bien si le cookie a_vote est présent
    if((isset($_POST['vote']) && !empty($_POST['vote'])) || isset($_COOKIE["a_vote"]))
    {
      //error_log("** 2 -widget + cookie\n",3,"/var/www/SiteWeb/WP_Test/php_log.txt");
      ?>
      <p>
        <label>Résultats</label></br>
        <?php
        global $wpdb;
        $res = $wpdb->get_results("SELECT * FROM wp_poll_results");
        foreach ($res as $label)
        {
          $nom = $wpdb->get_row("SELECT label FROM wp_poll_options WHERE id = ' $label->option_id'");
          //error_log("affichage : SELECT label FROM wp_poll_options WHERE id = ' $label->option_id'\n",3,"/var/www/SiteWeb/WP_Test/php_log.txt");
          echo '<label>' .$nom->label. ' : ' .$label->total. ' vote(s) </label> </br>' ;
        }
        ?>
      </p>

      <?php
    }
    else
    {
      //error_log("** 2 -widget sans cookie\n",3,"/var/www/SiteWeb/WP_Test/php_log.txt");
      ?>
      <form action="" method="post">
        <p>
          <label ><?php echo get_option('zero_poll_question') ?></label></br>
          <?php
          global $wpdb;
          $labels = $wpdb->get_results("SELECT label FROM wp_poll_options");
          foreach ($labels as $label)
          {
            echo '<input type="radio" name="vote" value=' .$label->label. '>' .$label->label. '</br> ' ;
          }
          ?>
        </p>
        <input type="submit" value="Envoyer"/>
      </form>
      <?php
    }

    echo $args['after_widget'];
  }

  /**
  * Affichage du formulaire dans l'administration
  */
  public function form($instance)
  {
    $title = isset($instance['title']) ? $instance['title'] : '';
    ?>
    <p>
      <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo  $title; ?>" />
    </p>
    <?php
  }
}
