<?php
class Wp_Scss_Settings
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'network_admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
        add_action( 'network_admin_edit_update_wpscss_options', array( $this, 'update_wpscss_options' ), 10, 0 );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_submenu_page(
            'settings.php',
            'Settings Admin',
            'WP-SCSS',
            'manage_options',
            'wpscss_options',
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_site_option( 'wpscss_options' );
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>WP-SCSS Settings</h2>
            <p>
              <span class="version">Version <em><?php echo get_site_option('wpscss_version'); ?></em>
              <br/>
              <span class="author">By: <a href="http://connectthink.com" target="_blank">Connect Think</a></span>
              <br/>
              <span class="repo">Help &amp; Issues: <a href="https://github.com/ConnectThink/WP-SCSS" target="_blank">Github</a></span>
            </p>
            <form method="post" action="edit.php?action=update_wpscss_options">
            <?php
                // This prints out all hidden setting fields
                do_settings_sections( 'wpscss_options' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        // Paths to Directories
        add_settings_section(
            'wpscss_paths_section', // ID
            'Configure Paths', // Title
            array( $this, 'print_paths_info' ), // Callback
            'wpscss_options' // Page
        );
        add_settings_field(
            'wpscss_scss_dir', // ID
            'Scss Location', // Title
            array( $this, 'scss_dir_callback' ), // Callback
            'wpscss_options', // Page
            'wpscss_paths_section' // Section
        );
        add_settings_field(
            'wpscss_css_dir',
            'CSS Location',
            array( $this, 'css_dir_callback' ),
            'wpscss_options',
            'wpscss_paths_section'
        );

        // PHP Variables Options
        add_settings_section(
            'wpscss_variables_section', // ID
            'Set SCSS Variables From PHP', // Title
            array( $this, 'print_variables_info' ), // Callback
            'wpscss_options' // Page
        );
        add_settings_field(
            'wpscss_vars_file',
            'PHP Variables Location',
            array( $this, 'vars_file_callback' ),
            'wpscss_options',
            'wpscss_variables_section'
        );

        // Compiling Options
        add_settings_section(
            'wpscss_compile_section', // ID
            'Compiling Options', // Title
            array( $this, 'print_compile_info' ), // Callback
            'wpscss_options' // Page
        );
        add_settings_field(
            'Compiling Mode',
            'Compiling Mode',
            array( $this, 'compiling_mode_callback' ), // Callback
            'wpscss_options', // Page
            'wpscss_compile_section' // Section
        );
        add_settings_field(
            'Error Display',
            'Error Display',
            array( $this, 'errors_callback' ), // Callback
            'wpscss_options', // Page
            'wpscss_compile_section' // Section
        );

        // Compiling Options
        add_settings_section(
            'wpscss_compile_section', // ID
            'Compiling Options', // Title
            array( $this, 'print_compile_info' ), // Callback
            'wpscss_options' // Page
        );
        add_settings_field(
            'Compiling Mode',
            'Compiling Mode',
            array( $this, 'compiling_mode_callback' ), // Callback
            'wpscss_options', // Page
            'wpscss_compile_section' // Section
        );
        add_settings_field(
            'Error Display',
            'Error Display',
            array( $this, 'errors_callback' ), // Callback
            'wpscss_options', // Page
            'wpscss_compile_section' // Section
        );

        // Enqueuing Options
        add_settings_section(
            'wpscss_enqueue_section', // ID
            'Enqueuing Options', // Title
            array( $this, 'print_enqueue_info' ), // Callback
            'wpscss_options' // Page
        );
        add_settings_field(
            'Enqueue Stylesheets',
            'Enqueue Stylesheets',
            array( $this, 'enqueue_callback' ), // Callback
            'wpscss_options', // Page
            'wpscss_enqueue_section' // Section
        );

    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input ) {

        if( !empty( $input['wpscss_scss_dir'] ) )
            $input['wpscss_scss_dir'] = sanitize_text_field( $input['wpscss_scss_dir'] );

        if( !empty( $input['wpscss_css_dir'] ) )
            $input['wpscss_css_dir'] = sanitize_text_field( $input['wpscss_css_dir'] );

        if( !empty( $input['wpscss_vars_file'] ) )
            $input['wpscss_vars_file'] = sanitize_text_field( $input['wpscss_vars_file'] );

        return $input;
    }

    /**
     * Print the Section text
     */
    public function print_paths_info() {
        print 'Add the paths to your directories below. Paths should start with the root of your theme. example: "/library/scss/"';
    }
    public function print_variables_info() {
        print 'SCSS variables can be set via PHP in the same manner as leafo\'s lessphp compiler (<a href="http://leafo.net/lessphp/docs/#setting_variables_from_php" target="_blank">Documentation</a>).<br> Add the path to your file below. Paths should start with the root of your theme. example: "/library/php/vars.php"';
    }
    public function print_compile_info() {
        print 'Choose how you would like SCSS to be compiled and how you would like the plugin to handle errors';
    }
    public function print_enqueue_info() {
        print 'WP-SCSS can enqueue your css stylesheets in the header automatically.';
    }

    /**
     * Text Fields' Callbacks
     */
    public function scss_dir_callback() {
        printf(
            '<input type="text" id="scss_dir" name="wpscss_options[scss_dir]" value="%s" />',
            esc_attr( $this->options['scss_dir'])
        );
    }
    public function css_dir_callback() {
        printf(
            '<input type="text" id="css_dir" name="wpscss_options[css_dir]" value="%s" />',
            esc_attr( $this->options['css_dir'])
        );
    }
    public function vars_file_callback() {
        printf(
            '<input type="text" id="vars_file" name="wpscss_options[vars_file]" value="%s" />',
            esc_attr( $this->options['vars_file'])
        );
    }

    /**
     * Select Boxes' Callbacks
     */
    public function compiling_mode_callback() {
        $this->options = get_site_option( 'wpscss_options' );

        $html = '<select id="compiling_options" name="wpscss_options[compiling_options]">';
            $html .= '<option value="scss_formatter"' . selected( $this->options['compiling_options'], 'scss_formatter', false) . '>Expanded</option>';
            $html .= '<option value="scss_formatter_nested"' . selected( $this->options['compiling_options'], 'scss_formatter_nested', false) . '>Nested</option>';
            $html .= '<option value="scss_formatter_compressed"' . selected( $this->options['compiling_options'], 'scss_formatter_compressed', false) . '>Compressed</option>';
        $html .= '</select>';

    echo $html;
    }
    public function errors_callback() {
        $this->options = get_site_option( 'wpscss_options' );

        $html = '<select id="errors" name="wpscss_options[errors]">';
            $html .= '<option value="show"' . selected( $this->options['errors'], 'show', false) . '>Show in Header</option>';
            $html .= '<option value="show-logged-in"' . selected( $this->options['errors'], 'show-logged-in', false) . '>Show to Logged In Users</option>';
            $html .= '<option value="log"' . selected( $this->options['errors'], 'hide', false) . '>Print to Log</option>';
        $html .= '</select>';

    echo $html;
    }

    /**
     * Checkboxes' Callbacks
     */
    function enqueue_callback() {
      $this->options = get_site_option( 'wpscss_options' );

      $html = '<input type="checkbox" id="enqueue" name="wpscss_options[enqueue]" value="1"' . checked( 1, isset($this->options['enqueue']) ? $this->options['enqueue'] : 0, false ) . '/>';
      $html .= '<label for="enqueue"></label>';

    echo $html;
    }

    /**
     * Update Settings
     */
    function update_wpscss_options() {
      error_log(implode(',', $_POST['wpscss_options']));
      $updated_options = $this->sanitize( $_POST['wpscss_options'] );
      update_site_option( 'wpscss_options', $updated_options );

      wp_redirect(add_query_arg(array('page' => 'wpscss_options', 'updated' => 'true'), network_admin_url('settings.php')));
      exit();
    }
}
