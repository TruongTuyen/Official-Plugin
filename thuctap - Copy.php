<?php
/*
Plugin Name: WordPress Custom Table And WP_List_Table Example Backup
Description: A wordpress work with custom table in wordpress database and WP_List_Table to display data from database
Plugin URI: https://ide.c9.io/hieudieuluong/thuctapcs
Author URI: https://ide.c9.io/hieudieuluong/thuctapcs
Author: Truong Tuyen Anh
License: Public Domain
Version: 1.0
Text Domain: simple_plugin

*/

/**
 * Visit at: http://mac-blog.org.ua/wordpress-custom-database-table-example-full/ for more details.
 */
 /**
  * Phan I: Tao cac bang can thiet cho plugin su dung dbDelta() 
  */
global $db_version;
$db_version = '1.0'; 

function tt_custom_table_install()
{
    global $wpdb;
    global $db_version;
   
    /** 
     * Create Custom Table for this plugin
     */
    $sql = "CREATE TABLE {$wpdb->prefix}_duan (
                    id_duan BIGINT NOT NULL AUTO_INCREMENT,
                    tenduan VARCHAR(225) NOT NULL,
                    thoigianbatdau DATETIME NOT NULL,
                    thoigianketthuc DATETIME NOT NULL,
                    ghichu TEXT NULL,
                    PRIMARY KEY (id_duan)   
             );
             
            CREATE TABLE {$wpdb->prefix}_nhanvien ( 
                    ID BIGINT NOT NULL AUTO_INCREMENT , 
                    hoten VARCHAR(225) NOT NULL , 
                    namsinh DATETIME NOT NULL , 
                    gioitinh TINYINT(2) NOT NULL ,
                    quequan VARCHAR(225) NOT NULL , 
                    PRIMARY KEY (ID)
            );
            
            CREATE TABLE {$wpdb->prefix}_chitiet_duan (
            		ID BIGINT NOT NULL AUTO_INCREMENT,
                	id_duan BIGINT NOT NULL,
                	id_nhanvien BIGINT NOT NULL,
                	ghichu TEXT NOT NULL,
                	PRIMARY KEY (ID),
            );
            
            CREATE TABLE {$wpdb->prefix}_kynang (
                     id_kynang BIGINT NOT NULL AUTO_INCREMENT,
                     tenkynang VARCHAR(225) NOT NULL,
                     chuthich TEXT NULL,
                     PRIMARY KEY (id_kynang)
             );
             
            CREATE TABLE {$wpdb->prefix}_chitiet_kynang (
            		ID BIGINT NOT NULL AUTO_INCREMENT,
                	id_kynang BIGINT NOT NULL,
                	id_nhanvien BIGINT NOT NULL,
                	ghichu TEXT NULL,
                	PRIMARY KEY (ID)
            ); 
    ";//query for create table 
       
    // Chen file uprade.php de su dung ham dbDelta()
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // save current database version for later use (on upgrade)
    add_option('db_version', $db_version);

    $installed_ver = get_option('db_version');
    if ($installed_ver != $db_version) {
        $table_name[] = $wpdb->prefix . "_duan";
        $table_name[] = $wpdb->prefix . "_nhanvien";
        $table_name[] = $wpdb->prefix . "_chitiet_duan";
        $table_name[] = $wpdb->prefix . "_kynang";
        $table_name[] = $wpdb->prefix . "_chitiet_kynang";
        
        $list_table = implode( ',', $table_name );
        $query = "DROP TABLE IF EXISTS $table_name";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        dbDelta($sql);

        // notice that we are updating option, rather than adding it
        update_option('db_version', $db_version);
    }
}

register_activation_hook(__FILE__, 'tt_custom_table_install');//dang ky activation hook thong qua register_activation_hook()

//Tao ham insert du lieu mau cho cac bang
function tt_dummy_data()
{
    global $wpdb;

    //Them du lieu mau cho cac bang _duan, _nhanvien, _chitiet_duan, _kynang, _chitiet_kynang de tranh cac loi null gia tri khi thuc hien get data tu csdl
    
    //du lieu mau cho bang _duan
    $wpdb->insert( $wpdb->prefix . '_duan', array(
        'id_duan'            => 1,
        'tenduan'            => 'Website Ban Hang 01',
        'thoigianbatdau'     => '2015-10-16',
        'thoigianketthuc'    => '2015-11-16',
        'ghichu'             => null   
    ));
    $wpdb->insert( $wpdb->prefix . '_duan', array(
        'id_duan'            => 2,
        'tenduan'            => 'Website Ban Hang 02',
        'thoigianbatdau'     => '2015-11-10',
        'thoigianketthuc'    => '2015-12-10',
        'ghichu'             => 'ghi chu 01'   
    ));
    
    //du lieu mau cho bang _nhanvien
    $wpdb->insert( $wpdb->prefix . '_nhanvien', array(
        'ID'                => 1, 
        'hoten'             => 'Nguyen Van A', 
        'namsinh'           => '1990-01-01', 
        'gioitinh'          => 1,//quy uoc: gia tri: 1 <=> Nam, 2 <=> Nu,
        'quequan'           => 'Thai Nguyen', 
    ));
    $wpdb->insert( $wpdb->prefix . '_nhanvien', array(
        'ID'                => 2, 
        'hoten'             => 'Nguyen Van B', 
        'namsinh'           => '1991-01-01', 
        'gioitinh'          => 1,//quy uoc: gia tri: 1 <=> Nam, 2 <=> Nu,
        'quequan'           => 'Thai Binh', 
    ));
    
    //du lieu mau cho bang _kynang
    $wpdb->insert( $wpdb->prefix . '_kynang', array(
        'id_kynang'         => 1,
        'tenkynang'         => 'HTML',
        'chuthich'          => 'Ghi chu 01',
    ));
    $wpdb->insert( $wpdb->prefix . '_kynang', array(
        'id_kynang'         => 2,
        'tenkynang'         => 'CSS',
        'chuthich'          => 'Ghi chu 02',
    ));
    $wpdb->insert( $wpdb->prefix . '_kynang', array(
        'id_kynang'         => 3,
        'tenkynang'         => 'PHP',
        'chuthich'          => 'Ghi chu 03',
    ));
    
    //du lieu mau cho bang: _chitiet_duan
    $wpdb->insert( $wpdb->prefix . '_chitiet_duan', array(
        'ID'                => 1,
    	'id_duan'           => 1,
    	'id_nhanvien'       => 1,
    	'ghichu'            => 'Ghi chu 01',
    ));
    $wpdb->insert( $wpdb->prefix . '_chitiet_duan', array(
        'ID'                => 2,
    	'id_duan'           => 1,
    	'id_nhanvien'       => 2,
    	'ghichu'            => 'Ghi chu 02',
    ));
    $wpdb->insert( $wpdb->prefix . '_chitiet_duan', array(
        'ID'                => 3,
    	'id_duan'           => 2,
    	'id_nhanvien'       => 1,
    	'ghichu'            => 'Ghi chu 01',
    ));
    $wpdb->insert( $wpdb->prefix . '_chitiet_duan', array(
        'ID'                => 4,
    	'id_duan'           => 2,
    	'id_nhanvien'       => 2,
    	'ghichu'            => 'Ghi chu 02',
    ));
    
    //du lieu cho bang _chitiet_kynang
    $wpdb->insert( $wpdb->preix . '_chitiet_kynang', array(
        'ID'                => 1,
	    'id_kynang'         => 1,
	    'id_nhanvien'       => 1,
    	'ghichu'            => "Ghi chu 01"
    ));
    $wpdb->insert( $wpdb->preix . '_chitiet_kynang', array(
        'ID'                => 2,
	    'id_kynang'         => 2,
	    'id_nhanvien'       => 1,
    	'ghichu'            => "Ghi chu 02"
    ));
    $wpdb->insert( $wpdb->preix . '_chitiet_kynang', array(
        'ID'                => 3,
	    'id_kynang'         => 1,
	    'id_nhanvien'       => 2,
    	'ghichu'            => "Ghi chu 03"
    ));
    $wpdb->insert( $wpdb->preix . '_chitiet_kynang', array(
        'ID'                => 4,
	    'id_kynang'         => 2,
	    'id_nhanvien'       => 2,
    	'ghichu'            => "Ghi chu 04"
    ));
    
}

register_activation_hook(__FILE__, 'tt_dummy_data'); //Them du lieu mau thong qua register_activation_hook()

//Kiem tra version cua database hien tai
function tt_update_db_check()
{
    global $db_version;
    if (get_site_option('db_version') != $db_version) {
        tt_custom_table_install();
    }
}

add_action('plugins_loaded', 'tt_update_db_check'); //kich hoat ham thong qua hook "plugins_loads"

/**
 * Defining Custom Table List
 * ============================================================================
 * http://codex.wordpress.org/Class_Reference/WP_List_Table
 * http://wordpress.org/extend/plugins/custom-list-table-example/
 */

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * Custom_Table_Example_List_Table class 
 */
 
 //Tao class TT_Nhanvien extends tu WP_List_Table de hien thi danh sach nhan vien
class TT_NhanVien extends WP_List_Table
{
    /**
     * [REQUIRED] You must declare constructor and give some basic params
     * Khai bao __construct va cac tham so co ban
     */
    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'staff', //Ten so it: 1 nhan vien
            'plural'   => 'staffs', //Ten so nhieu: nhieu nhan vien
        ));
    }

    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }

    function column_age($item)
    {
        return '<em>' . $item['age'] . '</em>';
    }

    function column_name($item)
    {
        // links going to /admin.php?page=[your_plugin_page][&other_params]
        // notice how we used $_REQUEST['page'], so action will be done on curren page
        // also notice how we use $this->_args['singular'] so in this example it will
        // be something like &person=2
        $actions = array(
            'edit'    => sprintf('<a href="?page=form_them_nv&id=%s">%s</a>', $item['id'], __('Sua', 'simple_plugin')),
            'delete'  => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __('Xoa', 'simple_plugin')),
        );

        return sprintf('%s %s',
            $item['name'],
            $this->row_actions($actions)
        );
    }

    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }

    function get_columns()
    {
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'hoten'     => __('Ho Ten', 'simple_plugin'),
            'namsinh'   => __('Nam Sinh', 'simple_plugin'),
            'gioitinh'  => __('Gioi Tinh', 'simple_plugin'),
        );
        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = array(
            'hoten'      => array('hoten', true),
            'namsinh'    => array('namsinh', false),
            'gioitinh'   => array('gioitinh', false),
        );
        return $sortable_columns;
    }
    
    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

    function process_bulk_action()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . '_nhanvien'; // do not forget about tables prefix

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
            }
        }
    }

    function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . '_nhanvien'; 

        $per_page = 5; //so ban ghi se hien thi o moi bang de co the phan trang 

        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();

        //Cau hinh cho colum_header
        $this->_column_headers = array($columns, $hidden, $sortable);

        //lua chon xu ly cac bulk_action
        $this->process_bulk_action();

        //Lay tong so ban ghi de tien hanh phan trang
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

        // Tinh toan cac tham so query params, VD: current page, order by va order 
        $paged   = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'hoten';
        $order   = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);

        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
}

/**
 * Tao Admin page
 * ============================================================================
 * http://codex.wordpress.org/Administration_Menus
 */

/**
 * Dang ky menu moi trong admin menu
 */
function tt_new_admin_menu()
{
    add_menu_page(__('Manage Teamwork', 'simple_plugin'), __('Manage Teamwork', 'simple_plugin'), 'activate_plugins', 'manage_teamwork', 'tt_nhavien_page_handler');
    add_submenu_page('manage_teamwork', __('Nhan Vien', 'simple_plugin'), __('Nhan Vien', 'simple_plugin'), 'activate_plugins', 'manage_teamwork', 'tt_nhavien_page_handler');
    // add new will be described in next part
    add_submenu_page('manage_teamwork', __('Them Moi Nhan Vien', 'simple_plugin'), __('Them Moi Nhan Vien', 'simple_plugin'), 'activate_plugins', 'them_moi_nv_form', 'tt_nhavien_add_new_form_handler');
}

add_action('admin_menu', 'tt_new_admin_menu');

/**
 * List page handler
 * Look into /wp-admin/includes/class-wp-*-list-table.php for examples
 */
function tt_nhavien_page_handler()
{
    global $wpdb;

    $table = new TT_NhanVien();
    $table->prepare_items();

    $message = '';
    if ('delete' === $table->current_action()) {
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Ban ghi da bi xoa: %d', 'custom_table_example'), count($_REQUEST['id'])) . '</p></div>';
    }
    ?>
<div class="wrap">

    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Persons', 'custom_table_example')?> 
        <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=them_moi_nv_form');?>"><?php _e('Them Moi Nhan Vien', 'simple_plugin')?></a>
    </h2>
    <?php echo $message; ?>

    <form id="persons-table" method="GET">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php $table->display() ?>
    </form>

</div>
<?php
}

/**
 * Phan 4. Form them va sua du lieu
 * ============================================================================
 * http://codex.wordpress.org/Data_Validation
 * http://codex.wordpress.org/Function_Reference/selected
 */

function tt_nhavien_add_new_form_handler()
{
    global $wpdb;
    $table_name = $wpdb->prefix . '_nhanvien'; 

    $message = '';
    $notice = '';

    //Khai bao mang gia tri mac dinh cho ban ghi moi
    $default = array(
        'id' => 0,
        'hoten' => '',
        'namsinh' => '',
        'gioitinh' => null,
    );

    //kiem tra gia tri cua nonce
    if (wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        // gộp các gtri mặc định  vào $_REQUEST
        $item = shortcode_atts($default, $_REQUEST);
        // Validate dữ liệu để lưu vào CSDL
        // nếu ID là 0 thì tiến hành insert, ngược lại thì update
        $item_valid = tt_validate_nhanvien_data($item);
        if ($item_valid === true) {
            if ($item['id'] == 0) {
                $result = $wpdb->insert($table_name, $item);
                $item['id'] = $wpdb->insert_id;
                if ($result) {
                    $message = __('Đã lưu', 'simple_plugin');
                } else {
                    $notice = __('Có lỗi trong quá trình lưu', 'simple_plugin');
                }
            } else {
                $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                if ($result) {
                    $message = __('Cập nhật thành công', 'simple_plugin');
                } else {
                    $notice = __('Xảy ra lỗi trong quá trình cập nhật', 'simple_plugin');
                }
            }
        } else {
            //Nếu $item_valid không trả về true thì nó sẽ chứa lỗi
            $notice = $item_valid;
        }
    }
    else {
        //Ngược lại sẽ load item để hiển thị hoặc tạo mới
        $item = $default;
        if (isset($_REQUEST['id'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Không tìm thấy dữ liệu ', 'simple_plugin');
            }
        }
    }

    // Tạo metabox
    add_meta_box('nhanvien_form_meta_box', 'Nhân viên', 'nhanvien_form_meta_box_handler', 'nhanvien', 'normal', 'default');

    ?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Person', 'custom_table_example')?> 
        <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=nhanvien');?>"><?php _e('Xem danh sách', 'custom_table_example')?></a>
    </h2>

    <?php if (!empty($notice)): ?>
    <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif;?>
    <?php if (!empty($message)): ?>
    <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif;?>

    <form id="form" method="POST">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
        <?php /* chúng ta lưu trữ id để xác đinh thao tác là insert hay update */ ?>
        <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                    <?php /* Gọi metabox */ ?>
                    <?php do_meta_boxes('nhanvien', 'normal', $item); ?>
                    <input type="submit" value="<?php _e('Gửi', 'simple_plugin')?>" id="submit" class="button-primary" name="submit">
                </div>
            </div>
        </div>
    </form>
</div>
<?php
}

/**
 * This function renders our custom meta box
 * $item is row
 *
 * @param $item
 */
function nhanvien_form_meta_box_handler($item)
{
    ?>

<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
    <tbody>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="hoten"><?php _e('Họ Tên', 'simple_plugin')?></label>
        </th>
        <td>
            <input id="hoten" name="hoten" type="text" style="width: 95%" value="<?php echo esc_attr($item['hoten'])?>"
                   size="50" class="code" placeholder="<?php _e('Vui lòng điền đầy đủ họ tên', 'simple_plugin')?>" required>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="namsinh"><?php _e('Năm Sinh', 'custom_table_example')?></label>
        </th>
        <td>
            <input id="namsinh" name="namsinh" type="date" style="width: 95%" value="<?php echo esc_attr($item['namsinh'])?>"
                   size="50" class="code" placeholder="<?php _e('Your E-Mail', 'simple_plugin')?>" required>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="gioitinh"><?php _e('Giới Tính', 'simple_plugin')?></label>
        </th>
        <td>
            <input id="gioitinh" name="gioitinh" type="radio"  value="1" class="code" <?php if( $item['gioitinh'] == 1 ){ echo 'selected="selected"'; } ?> >Nam 
            <input id="gioitinh" name="gioitinh" type="radio"  value="2" class="code" <?php if( $item['gioitinh'] == 2 ){ echo 'selected="selected"'; } ?> >Nữ 
        </td>
    </tr>
    </tbody>
</table>
<?php
}

/**
 * function validate dữ liệu để đảm bảo chấp nhận dữ liệu hợp lệ
 */
function tt_validate_nhanvien_data($item)
{
    $messages = array();

    if (empty($item['hoten'])) $messages[] = __('Trường "họ tên" là bắt buộc', 'simple_plugin');
    if (empty($item['namsinh'])) $messages[] = __('Trường "năm sinh" là bắt buộc', 'simple_plugin');
    if (empty($item['gioitinh'])) $messages[] = __('Trường "giới tính" là bắt buộc', 'simple_plugin');
    if (!is_int($item['namsinh'])) $messages[] = __('Giá trị "năm sinh" không hợp lệ', 'simple_plugin');
    if (empty($messages)) return true;
    return implode('<br />', $messages);
}


function tt_support_languages()
{
    load_plugin_textdomain('simple_plugin', false, dirname(plugin_basename(__FILE__)));
}

add_action('init', 'tt_support_languages');

function tt_delete_tables(){
    global $wpdb;
    $table_name = array();
    $table_name[] = $wpdb->prefix . "_duan";
    $table_name[] = $wpdb->prefix . "_nhanvien";
    $table_name[] = $wpdb->prefix . "_chitiet_duan";
    $table_name[] = $wpdb->prefix . "_kynang";
    $table_name[] = $wpdb->prefix . "_chitiet_kynang";
    
    $list_table = implode( ',', $table_name );
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query( $sql );
    delete_option( 'db_version' );
}
register_deactivation_hook( __FILE__, 'tt_delete_tables' );