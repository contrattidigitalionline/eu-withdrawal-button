<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class EUWB_Admin {

    public function __construct() {
        add_action( 'admin_menu',            array( $this, 'register_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
        add_filter( 'set-screen-option',     array( $this, 'set_screen_option' ), 10, 3 );
    }

    public function enqueue( $hook ) {
        if ( strpos( $hook, 'eu-withdrawal' ) === false ) return;
        wp_enqueue_style( 'euwb-admin', EUWB_PLUGIN_URL . 'assets/css/euwb-admin.css', array(), EUWB_VERSION );
    }

    public function register_menu() {
        $hook = add_menu_page(
            __( 'EU Withdrawal Button', 'eu-withdrawal-button' ),
            __( 'EU Withdrawal', 'eu-withdrawal-button' ),
            'manage_woocommerce',
            'eu-withdrawal-log',
            array( $this, 'render_log_page' ),
            'dashicons-undo',
            57
        );
        add_submenu_page(
            'eu-withdrawal-log',
            __( 'Registro Recessi', 'eu-withdrawal-button' ),
            __( 'Registro Recessi', 'eu-withdrawal-button' ),
            'manage_woocommerce',
            'eu-withdrawal-log',
            array( $this, 'render_log_page' )
        );
        add_submenu_page(
            'eu-withdrawal-log',
            __( 'Impostazioni', 'eu-withdrawal-button' ),
            __( 'Impostazioni', 'eu-withdrawal-button' ),
            'manage_options',
            'eu-withdrawal-settings',
            array( $this, 'render_settings_page' )
        );

        add_action( "load-$hook", array( $this, 'add_screen_options' ) );
    }

    public function add_screen_options() {
        add_screen_option( 'per_page', array(
            'label'   => __( 'Recessi per pagina', 'eu-withdrawal-button' ),
            'default' => 20,
            'option'  => 'euwb_withdrawals_per_page',
        ) );
    }

    public function set_screen_option( $status, $option, $value ) {
        if ( 'euwb_withdrawals_per_page' === $option ) return absint( $value );
        return $status;
    }

    // -----------------------------------------------------------------------
    // Log page
    // -----------------------------------------------------------------------
    public function render_log_page() {
        $per_page    = (int) get_user_meta( get_current_user_id(), 'euwb_withdrawals_per_page', true ) ?: 20;
        $current_page = max( 1, absint( $_GET['paged'] ?? 1 ) );
        $status_filter = sanitize_text_field( $_GET['status'] ?? '' );
        $offset      = ( $current_page - 1 ) * $per_page;

        $items = EUWB_Withdrawal::get_all( array( 'limit' => $per_page, 'offset' => $offset, 'status' => $status_filter ) );
        $total = EUWB_Withdrawal::count( $status_filter );
        $pages = ceil( $total / $per_page );
        ?>
        <div class="wrap euwb-admin-wrap">
            <h1><?php esc_html_e( 'Registro Recessi EU', 'eu-withdrawal-button' ); ?>
                <span class="euwb-badge"><?php echo esc_html( EUWB_Withdrawal::count( 'pending' ) ); ?> <?php esc_html_e( 'In attesa', 'eu-withdrawal-button' ); ?></span>
            </h1>

            <ul class="subsubsub">
                <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=eu-withdrawal-log' ) ); ?>" <?php echo ! $status_filter ? 'class="current"' : ''; ?>><?php esc_html_e( 'Tutti', 'eu-withdrawal-button' ); ?> <span class="count">(<?php echo EUWB_Withdrawal::count(); ?>)</span></a> |</li>
                <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=eu-withdrawal-log&status=pending' ) ); ?>" <?php echo $status_filter === 'pending' ? 'class="current"' : ''; ?>><?php esc_html_e( 'In attesa', 'eu-withdrawal-button' ); ?> <span class="count">(<?php echo EUWB_Withdrawal::count( 'pending' ); ?>)</span></a> |</li>
                <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=eu-withdrawal-log&status=confirmed' ) ); ?>" <?php echo $status_filter === 'confirmed' ? 'class="current"' : ''; ?>><?php esc_html_e( 'Confermati', 'eu-withdrawal-button' ); ?> <span class="count">(<?php echo EUWB_Withdrawal::count( 'confirmed' ); ?>)</span></a></li>
            </ul>

            <table class="wp-list-table widefat fixed striped euwb-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'ID', 'eu-withdrawal-button' ); ?></th>
                        <th><?php esc_html_e( 'Ordine', 'eu-withdrawal-button' ); ?></th>
                        <th><?php esc_html_e( 'Cliente', 'eu-withdrawal-button' ); ?></th>
                        <th><?php esc_html_e( 'Email', 'eu-withdrawal-button' ); ?></th>
                        <th><?php esc_html_e( 'Motivo', 'eu-withdrawal-button' ); ?></th>
                        <th><?php esc_html_e( 'Stato', 'eu-withdrawal-button' ); ?></th>
                        <th><?php esc_html_e( 'Data richiesta', 'eu-withdrawal-button' ); ?></th>
                        <th><?php esc_html_e( 'Data conferma', 'eu-withdrawal-button' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php if ( $items ) : foreach ( $items as $row ) : ?>
                    <tr>
                        <td><?php echo absint( $row->id ); ?></td>
                        <td><a href="<?php echo esc_url( admin_url( 'post.php?post=' . $row->order_id . '&action=edit' ) ); ?>">#<?php echo absint( $row->order_id ); ?></a></td>
                        <td><?php echo esc_html( $row->first_name . ' ' . $row->last_name ); ?></td>
                        <td><?php echo esc_html( $row->email ); ?></td>
                        <td><?php echo esc_html( $row->reason ?: '—' ); ?></td>
                        <td><span class="euwb-status euwb-status--<?php echo esc_attr( $row->status ); ?>"><?php echo esc_html( $row->status === 'confirmed' ? 'Confermato' : 'In attesa' ); ?></span></td>
                        <td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $row->created_at ) ) ); ?></td>
                        <td><?php echo $row->confirmed_at ? esc_html( date_i18n( get_option( 'date_format' ), strtotime( $row->confirmed_at ) ) ) : '—'; ?></td>
                    </tr>
                <?php endforeach; else : ?>
                    <tr><td colspan="8"><?php esc_html_e( 'Nessun recesso trovato.', 'eu-withdrawal-button' ); ?></td></tr>
                <?php endif; ?>
                </tbody>
            </table>

            <?php if ( $pages > 1 ) : ?>
            <div class="tablenav bottom">
                <div class="tablenav-pages">
                    <?php
                    echo paginate_links( array(
                        'base'      => add_query_arg( 'paged', '%#%' ),
                        'format'    => '',
                        'current'   => $current_page,
                        'total'     => $pages,
                        'prev_text' => '&laquo;',
                        'next_text' => '&raquo;',
                    ) );
                    ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }

    // -----------------------------------------------------------------------
    // Settings page
    // -----------------------------------------------------------------------
    public function render_settings_page() {
        if ( isset( $_POST['euwb_save_settings'] ) && check_admin_referer( 'euwb_settings' ) ) {
            update_option( 'euwb_withdrawal_window', absint( $_POST['euwb_withdrawal_window'] ?? 14 ) );
            update_option( 'euwb_admin_email', sanitize_email( $_POST['euwb_admin_email'] ?? get_option( 'admin_email' ) ) );
            echo '<div class="notice notice-success"><p>' . esc_html__( 'Impostazioni salvate.', 'eu-withdrawal-button' ) . '</p></div>';
        }
        $window = get_option( 'euwb_withdrawal_window', 14 );
        $email  = get_option( 'euwb_admin_email', get_option( 'admin_email' ) );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Impostazioni EU Withdrawal Button', 'eu-withdrawal-button' ); ?></h1>
            <form method="post">
                <?php wp_nonce_field( 'euwb_settings' ); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="euwb_withdrawal_window"><?php esc_html_e( 'Finestra di recesso (giorni)', 'eu-withdrawal-button' ); ?></label></th>
                        <td><input type="number" id="euwb_withdrawal_window" name="euwb_withdrawal_window" value="<?php echo esc_attr( $window ); ?>" min="1" max="30" class="small-text">
                        <p class="description"><?php esc_html_e( 'La direttiva UE 2023/2673 prevede 14 giorni come minimo.', 'eu-withdrawal-button' ); ?></p></td>
                    </tr>
                    <tr>
                        <th><label for="euwb_admin_email"><?php esc_html_e( 'Email notifiche admin', 'eu-withdrawal-button' ); ?></label></th>
                        <td><input type="email" id="euwb_admin_email" name="euwb_admin_email" value="<?php echo esc_attr( $email ); ?>" class="regular-text">
                        <p class="description"><?php esc_html_e( 'Riceverà una notifica ad ogni recesso confermato.', 'eu-withdrawal-button' ); ?></p></td>
                    </tr>
                </table>
                <?php submit_button( __( 'Salva impostazioni', 'eu-withdrawal-button' ), 'primary', 'euwb_save_settings' ); ?>
            </form>
        </div>
        <?php
    }
}
