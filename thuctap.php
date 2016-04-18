<?php
/*
Plugin Name: WordPress Custom Table And WP_List_Table Example
Description: A wordpress work with custom table in wordpress database and WP_List_Table to display data from database
Plugin URI: https:localhost
Author URI: https:localhost
Author: Truong Tuyen Anh
License: Public Domain
Version: 1.0
Text Domain: simple_plugin
*/
define( TT_DIR_PATH, plugin_dir_path( __FILE__ ) ); //Lấy ra dường dẫn tuyệt đối tới thu muc của plugin này 
define( TT_DIR_URL, plugin_dir_url( __FILE__ ) ); //Lấy ra url của plugin này

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );//sử dụng file này để có thể dùng hàm dbDelta();
require_once TT_DIR_PATH . 'classes/class.TT_KyNang.php';  //Class Ky Nang xu ly thong tin liên quan den ky nang
require_once TT_DIR_PATH . 'classes/class.TT_Nhanvien.php';//Class Nhan Vien xu ly thong tin lien quan den nhan vien
require_once TT_DIR_PATH . 'classes/class.TT_Duan.php';//Class Du An xu ly thong tin lien quan den du an

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class TT_Teamwork{
    public $db_version = '1.0';
    
    function __construct(){
        register_activation_hook( __FILE__,  array( $this, 'create_table' ) );
        register_activation_hook( __FILE__,  array( $this, 'dummy_data' ) );
        register_deactivation_hook( __FILE__, array( $this, 'delete_table' ) );
        
        add_action( 'admin_menu', array( $this, 'register_setting_menu' ) );
        add_action( 'init', array( $this, 'tt_load_languages' ) );
    }
    
    public function create_table(){
        global $wpdb;
        $query = "
            CREATE TABLE {$wpdb->prefix}duan(
                id_duan BIGINT NOT NULL AUTO_INCREMENT,
                tenduan VARCHAR(225) NOT NULL,
                thoigianbatdau DATETIME NOT NULL,
                thoigianketthuc DATETIME NOT NULL,
                trangthai VARCHAR(225) NOT NULL,
                ghichu TEXT NULL,
                PRIMARY KEY (id_duan)   
            );
                         
            CREATE TABLE {$wpdb->prefix}nhanvien( 
                id_nhanvien BIGINT NOT NULL AUTO_INCREMENT , 
                hoten VARCHAR(225) NOT NULL , 
                namsinh VARCHAR(4) NOT NULL , 
                gioitinh VARCHAR(3) NOT NULL ,
                quequan VARCHAR(225) NOT NULL , 
                PRIMARY KEY (id_nhanvien)
            );
            
            CREATE TABLE {$wpdb->prefix}kynang(
                id_kynang BIGINT NOT NULL AUTO_INCREMENT,
                tenkynang VARCHAR(225) NOT NULL,
                chuthich TEXT NULL,
                PRIMARY KEY (id_kynang)
            );
            
            CREATE TABLE {$wpdb->prefix}chitiet_duan(
                id BIGINT NOT NULL AUTO_INCREMENT,
                id_duan BIGINT NOT NULL,
                id_nhanvien BIGINT NOT NULL,
                PRIMARY KEY (id)
            );
            
            CREATE TABLE {$wpdb->prefix}chitiet_kynang(
                id BIGINT NOT NULL AUTO_INCREMENT,
                id_kynang BIGINT NOT NULL,
                id_nhanvien BIGINT NOT NULL,
                PRIMARY KEY (id)
            ); 
        ";
        dbDelta( $query );
    }
    
    public function db_version_option( $version = '1.0' ){
        add_option( 'db_version', $version );
    }
    
    public function del_db_version(){
        delete_option( 'db_version' );
    }
    
    public function update_db_version( $new_version ){
        update_option( 'db_version', $new_version );
    }
    
    public function delete_table(){
        global $wpdb;
        $sql = "DROP TABLE IF EXISTS `{$wpdb->prefix}duan`, `{$wpdb->prefix}nhanvien`, `{$wpdb->prefix}kynang`, `{$wpdb->prefix}chitiet_duan`, `{$wpdb->prefix}chitiet_kynang`";
        $wpdb->query( $sql );
        $this->del_db_version();
    }
    
    public function check_db_version(){
        $current_version = get_option( 'db_version' );
        if( $this->db_version != $current_version ){
            $this->delete_table();
            $this->create_table();
            $this->update_db_version( $this->db_version );
        }
    }
    
    public function dummy_data(){
        global $wpdb;
        $wpdb->insert( $wpdb->prefix . 'duan', array(
            'id_duan'            => 1,
            'tenduan'            => 'Website bán hàng 01',
            'thoigianbatdau'     => '2015-10-16',
            'thoigianketthuc'    => '2015-11-16',
            'trangthai'          => 'Đã hoàn thành', // Đã hoàn thành, Đang triển khai, Chưa hoàn thành, Đã hủy   
            'ghichu'             => null   
        ));
        $wpdb->insert( $wpdb->prefix . 'duan', array(
            'id_duan'            => 2,
            'tenduan'            => 'Website bán hàng 02',
            'thoigianbatdau'     => '2015-11-10',
            'thoigianketthuc'    => '2015-12-10',
            'trangthai'          => 'Đang triển khai',
            'ghichu'             => 'ghi chu 01'   
        ));
        
        //du lieu mau cho bang _nhanvien
        $wpdb->insert( $wpdb->prefix . 'nhanvien', array(
            'id_nhanvien'       => 1, 
            'hoten'             => 'Nguyen Van An', 
            'namsinh'           => '1990', 
            'gioitinh'          => "Nam",
            'quequan'           => 'Thai Nguyen', 
        ));
        $wpdb->insert( $wpdb->prefix . 'nhanvien', array(
            'id_nhanvien'       => 2, 
            'hoten'             => 'Nguyen Thị Ba', 
            'namsinh'           => '1991', 
            'gioitinh'          => "Nữ",
            'quequan'           => 'Thai Binh', 
        ));
        
        //du lieu mau cho bang _kynang
        $wpdb->insert( $wpdb->prefix . 'kynang', array(
            'id_kynang'         => 1,
            'tenkynang'         => 'HTML',
            'chuthich'          => 'Ghi chu 01',
        ));
        $wpdb->insert( $wpdb->prefix . 'kynang', array(
            'id_kynang'         => 2,
            'tenkynang'         => 'CSS',
            'chuthich'          => 'Ghi chu 02',
        ));
        $wpdb->insert( $wpdb->prefix . 'kynang', array(
            'id_kynang'         => 3,
            'tenkynang'         => 'PHP',
            'chuthich'          => 'Ghi chu 03',
        ));
        
        //du lieu mau cho bang: _chitiet_duan
        $wpdb->insert( $wpdb->prefix . 'chitiet_duan', array(
            'id'                => 1,
        	'id_duan'           => 1,
        	'id_nhanvien'       => 1,
        ));
        $wpdb->insert( $wpdb->prefix . 'chitiet_duan', array(
            'id'                => 2,
        	'id_duan'           => 1,
        	'id_nhanvien'       => 2,
        ));
        $wpdb->insert( $wpdb->prefix . 'chitiet_duan', array(
            'id'                => 3,
        	'id_duan'           => 2,
        	'id_nhanvien'       => 1,
        ));
        $wpdb->insert( $wpdb->prefix . 'chitiet_duan', array(
            'id'                => 4,
        	'id_duan'           => 2,
        	'id_nhanvien'       => 2,
        ));
        
        //du lieu cho bang _chitiet_kynang
        $wpdb->insert( $wpdb->prefix . 'chitiet_kynang', array(
            'id'                => 1,
    	    'id_kynang'         => 1,
    	    'id_nhanvien'       => 1,
        	'ghichu'            => "Ghi chu 01"
        ));
        $wpdb->insert( $wpdb->prefix . 'chitiet_kynang', array(
            'id'                => 2,
    	    'id_kynang'         => 2,
    	    'id_nhanvien'       => 1,
        ));
        $wpdb->insert( $wpdb->prefix . 'chitiet_kynang', array(
            'id'                => 3,
    	    'id_kynang'         => 1,
    	    'id_nhanvien'       => 2,
        ));
        $wpdb->insert( $wpdb->prefix . 'chitiet_kynang', array(
            'id'                => 4,
    	    'id_kynang'         => 2,
    	    'id_nhanvien'       => 2,
        ));
    }  
    
    public function tt_teamwork_callback(){
        
    }
    
    public function register_setting_menu(){
        add_menu_page( __("TT Teamwork", "simple_plugin"), __("TT Teamwork","simple_plugin"), "activate_plugins", "tt_teamwork", array( $this, "tt_teamwork_callback" ) );
        
        add_submenu_page( "tt_teamwork", __( "Danh sách nhân viên","simple_plugin" ), __( "Danh sách nhân viên","simple_plugin" ), "activate_plugins", "ds_nhanvien", array( "TT_Nhanvien", "tt_page_nhanvien_callback" ) );
        add_submenu_page( "tt_teamwork", __( "Thêm mới nhân viên", "simple_plugin" ), __( "Thêm mới nhân viên", "simple_plugin" ), "activate_plugins", "new_nhanvien", array( "TT_Nhanvien", "tt_new_nhanvien_callback" ) );
        
        add_submenu_page( 'tt_teamwork', __( "Danh sách kỹ năng", "simple_plugin" ), __( "Danh sách kỹ năng", "simple_plugin" ), "activate_plugins", "ds_ky_nang", array( "TT_KyNang", "tt_kynang_page_callback" ) );
        add_submenu_page( 'tt_teamwork', __( "Thêm mới kỹ năng", "simple_plugin" ), __( "Thêm mới kỹ năng", "simple_plugin" ), "activate_plugins", "new_kynang", array( "TT_KyNang", "tt_new_kynang_callback" ) );
    
        add_submenu_page( 'tt_teamwork', __( "Danh sách dự án", "simple_plugin" ), __( "Danh sách dự án", "simple_plugin" ), "activate_plugins", "ds_duan", array( "TT_Duan", "tt_duan_page_callback") );
        add_submenu_page( 'tt_teamwork', __( "Thêm mới dự án", "simple_plugin" ), __( "Thêm mới dự án", "simple_plugin" ), "activate_plugins", "new_duan", array( "TT_Duan", "tt_new_duan_page_callback") );
    }
    
    public function tt_load_languages(){
        load_plugin_textdomain( 'simple_plugin', false, dirname(plugin_basename(__FILE__) ));
    }
    
    public static function tt_selected( $select, $value ){
        if( isset( $select ) && !empty( $select ) ){
            if( $select == $value){
                echo 'selected="selected"';
            }
        }
    }
    
    public static function tt_checked( $check, $value ){
        if( isset( $check ) && !empty( $check ) ){
            if( $check == $value){
                echo 'checked="checked"';
            }
        }
    }
    
}

new TT_Teamwork();

function enqueue_script(){
    wp_enqueue_script( 'jquery_min', TT_DIR_URL . 'assets/js/jquery-1.12.3.min.js', array('jquery'), null, true );    
    wp_enqueue_script( 'jquery_ui', TT_DIR_URL . 'assets/js/jquery-ui.min.js', array('jquery'), null, true );
    wp_enqueue_script( 'jquer_choosen', TT_DIR_URL . 'assets/js/chosen.jquery.min.js', array('jquery'), null, true );
    wp_enqueue_script( 'jquery_function', TT_DIR_URL . 'assets/js/function.js', array('jquery'), null, true );
    
    wp_enqueue_style( 'jquery-ui-css', TT_DIR_URL . 'assets/css/jquery-ui.min.css', false, '' );
    wp_enqueue_style( 'jquery-ui-theme-css', TT_DIR_URL . 'assets/css/jquery-ui.theme.min.css', false, '' );
    wp_enqueue_style( 'jquery-ui-structure-css', TT_DIR_URL . 'assets/css/jquery-ui.structure.min.css', false, '' );
    wp_enqueue_style( 'choosen.min.js', TT_DIR_URL . 'assets/css/chosen.min.css', false, '' );
}
add_action( 'admin_enqueue_scripts', 'enqueue_script' );
















