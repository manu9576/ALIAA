<?php
/*
Plugin Name: Poll
Description: Un plugin de vote sous WordPress
Version: 0.1
Author: Manu9576
Author URI: http://manu9576.freeboxos.fr
License: GPL2
*/

include_once plugin_dir_path( __FILE__ ).'/pollwidget.php';

/**
* Classe Poll_Plugin
* Déclare le plugin
*/
class Poll_Plugin
{
  /**
  * Constructeur
  */
  public function __construct()
  {
    register_activation_hook(__FILE__, array($this, 'install'));
    register_uninstall_hook(__FILE__, array($this, 'uninstall'));

    add_action('widgets_init', function(){register_widget('Poll_Widget');});
    add_action('wp_loaded', array($this, 'add_doll'));
    //$hook =   add_action('widgets_init', function(){register_widget('Poll_Widget');});
    //add_action('load-'.$hook, array($this, 'add_doll'));
    add_action('admin_init', array($this, 'register_settings'));
    add_action('admin_menu', array($this, 'add_admin_menu'),20);

  }

  /**
  * Fonction d'installation
  */
  public function install()
  {
    global $wpdb;
    $wpdb->show_errors();
    $wpdb->query("CREATE TABLE IF NOT EXISTS wp_poll_options (id INT AUTO_INCREMENT PRIMARY KEY, label VARCHAR(255) NOT NULL);");
    $wpdb->query("CREATE TABLE IF NOT EXISTS wp_poll_results (option_id INT NOT NULL, total INT NOT NULL);");
  }

  /**
  * Fonction de désinstallation
  * Suppression des tables du sondage
  */
  public function uninstall()
  {
    global $wpdb;
    $wpdb->show_errors();
    $wpdb->query("DROP TABLE IF EXISTS wp_poll_options;");
    $wpdb->query("DROP TABLE IF EXISTS wp_poll_results;");
  }


  public function menu_presentation_html()
  {
    echo '<h1>'.get_admin_page_title().'</h1>';
    echo '<p>Bienvenue sur la page d\'accueil du plugin</p>';
  }

  /**
  * Menu d'administration
  */
  public function menu_html()
  {
    echo '<h1>Sondage</h1>';
    ?>

    <form method="post" action="options.php">
      <?php settings_fields('zero_poll_settings') ?>
      <?php do_settings_sections('zero_poll_settings') ?>
      <?php
      global $wpdb;
      $labels = $wpdb->get_results("SELECT label FROM wp_poll_options");
      foreach ($labels as $label)
      {
        echo '<input value=' .$label->label. ' readonly="true"></br>';
      }
      ?>
      <p>
        <label for="zero_ajout_option">Ajouter une nouvelle réponse</label>
        <input id="zero_ajout_option" name="zero_ajout_option" type="text"/>
      </p>

      <?php submit_button(); ?>
    </form>

    <form method="post" action="">
      <input type="hidden" name="reinit" value="1"/>
      <?php submit_button('Réinitialiser les options et les résultats') ?>
    </form>

    <?php
  }
  /**
  * ajout du menu d'administration
  */
  public function add_admin_menu()
  {
    add_menu_page('Le plugin Poll', 'Poll plugin', 'manage_options', 'zero_doll', array($this, 'menu_presentation_html'));
    $hook = add_submenu_page('zero_doll', 'Réglage', 'Réglage', 'manage_options', 'zero_reglage', array($this, 'menu_html'));
    add_action('load-'.$hook, array($this, 'process_action'));
  }

  /**
  * Fonction qui declare le setting question
  */
  public function register_settings()
  {
    $this->add_new_option();
    register_setting('zero_poll_settings', 'zero_poll_question');

    add_settings_section('zero_poll_section', null,null, 'zero_poll_settings');
    add_settings_field('zero_poll_question', 'Question', array($this, 'question_html'), 'zero_poll_settings', 'zero_poll_section');
  }

  /**
  * fonction appele pour l'ajout d'un nouveau choix de vote
  */
  public function add_new_option()
  {
    if (isset($_POST['zero_ajout_option']) && !empty($_POST['zero_ajout_option']))
    {
      global $wpdb;
      $option = $_POST['zero_ajout_option'];
      $row = $wpdb->get_row("SELECT * FROM wp_poll_options WHERE label = '$option'");

      if (is_null($row))
      {
        $wpdb->insert("wp_poll_options", array('label' => $option));
      }
    }
  }

  /**
  * fonction qui gere l'affichage du parametres question
  */
  public function question_html()
  {?>
    <input type="text" name="zero_poll_question" value="<?php echo get_option('zero_poll_question')?>"/>
    <?php
  }

  /**
  * Fonction qui va etre executer lors du post de la
  *page d'administration => detecter la demande de réinitialisation de l
  *la question et des options
  */
  public function process_action()
  {
    if (isset($_POST['reinit']))
    {
      $this->reset_options();
    }
  }

  /**
  * fonction appele en cas de reset de la question :
  * vide le contenu des tables options et résultat
  */
  public function reset_options()
  {
    global $wpdb;
    $wpdb->query("truncate wp_poll_options;");
    $wpdb->query("truncate wp_poll_results;");
  }

  /**
  *Fonction appele pour prendre en compte le
  *vote d'un visiteur
  */
  public function add_doll()
  {
    //error_log("** add_doll 1 \n",3,"/var/www/SiteWeb/WP_Test/php_log.txt");
    if (isset($_POST['vote']) && !empty($_POST['vote']) )
    {
      //error_log("** add_doll 2 \n",3,"/var/www/SiteWeb/WP_Test/php_log.txt
      setcookie("a_vote",1);
      //error_log("** add_doll 2 \n",3,"/var/www/SiteWeb/WP_Test/php_log.txt");

      global $wpdb;
      $vote = $_POST['vote'];
      $option = $wpdb->get_row("SELECT * FROM wp_poll_options WHERE label = '$vote'");

      if (!is_null($option))
      {
        $res =  $wpdb->get_row("SELECT * FROM wp_poll_results WHERE option_id = '$option->id'");
        if(!is_null($res))
        {
          $wpdb->update("wp_poll_results", array('total' => $res->total+1),array('option_id' => $option->id));
        }
        else
        {
          $wpdb->insert("wp_poll_results", array('option_id' => $option->id,'total'=> 1));
        }
      }
    }
  }
}

new Poll_Plugin();
