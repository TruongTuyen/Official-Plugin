<?php
class TT_Nhanvien extends WP_List_Table{
    public function __construct(){
       
       global $status, $page;
        parent::__construct(
            array(
                'singular' => "mot_nhanvien",
                'plural'   => "nhieu_nhanvien" 
            )
        );
        
        
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script' ) );
    }
    
    function column_default( $item, $column_name ){
        return $item[ $column_name ];
    }
    
    function column_id_nhanvien( $item ){
        return '<em>' . $item['id_nhanvien'] . '</em>';
    }
    
    function column_namsinh( $item ){
        return '<em>' . $item['namsinh'] . '</em>';
    }
    
    function column_gioitinh( $item ){
        return '<em>' . $item['gioitinh'] . '</em>';
    }
    
    function column_cac_duan( $item ){
        return '<em>' . $item['cac_duan'] . '</em>';
    }
    
    function column_cac_kynang( $item ){
        return '<em>' . $item['cac_kynang'] . '</em>';
    }
    
    function column_quequan( $item ){
        return '<em>' . $item['quequan'] . '</em>';
    }

    function column_hoten( $item ){
        $actions = array(
            'edit'   => sprintf( '<a href="?page=new_nhanvien&id_nhanvien=%s">%s</a>', $item['id_nhanvien'], __( 'Sửa dữ liệu', 'simple_plugin' ) ),
            'delete' => sprintf( '<a href="?page=%s&action=delete&id_nhanvien=%s">%s</a>', $_REQUEST['page'], $item['id_nhanvien'], __( 'Xóa dữ liệu', 'simple_plugin' ) ),
        );
        return sprintf('%s %s', $item['hoten'], $this->row_actions( $actions ) );
    }

    function column_cb( $item ){
        return sprintf( '<input type="checkbox" name="id[]" value="%s" />', $item['id_duan'] );
    }

    function get_columns()
    {
        $columns = array(
            'cb'                => '<input type="checkbox" />', //Tạo nut checkbox
            'id_nhanvien'       => __( 'ID', 'simple_plugin' ),
            'hoten'             => __( 'Họ tên', 'simple_plugin' ),
            'namsinh'           => __( 'Năm sinh', 'simple_plugin' ),
            'gioitinh'          => __( 'Giới tính', 'simple_plugin' ),
            'quequan'           => __( 'Quê quán', 'simple_plugin' ),    
            'cac_duan'          => __( 'Các kỹ năng', 'simple_plugin' ),
            'cac_kynang'        => __( 'Các dự án', 'simple_plugin' ),
        );
        return $columns;
    }

    function get_sortable_columns(){
        $sortable_columns = array(
            'id_nhanvien'     => array( 'id_duan', true ),
            'hoten'           => array( 'tenduan', false ),
            'namsinh'         => array( 'namsinh', false )
        );
        return $sortable_columns;
    }

    function get_bulk_actions() {
        $actions = array(
            'delete' => 'Xóa dữ liệu'
        );
        return $actions;
    }

    function process_bulk_action(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'nhanvien'; 

        if ( 'delete' === $this->current_action() ) {
            $ids = isset($_REQUEST['id_nhanvien']) ? $_REQUEST['id_nhanvien'] : array();
            if (is_array($ids)) {
                $ids = implode(',', $ids);
            }  
            if ( !empty( $ids ) ) {
                $wpdb->query( "DELETE FROM {$table_name} WHERE id IN( {$ids} )" );
            }
        }
    }
    function prepare_items(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'nhanvien';
        $per_page = 5;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array( $columns, $hidden, $sortable );
        $this->process_bulk_action();
        $total_items = $wpdb->get_var( "SELECT COUNT(id_nhanvien) FROM {$table_name}" );

        $paged   = isset( $_REQUEST['paged'] ) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset( $_REQUEST['orderby'] ) && in_array($_REQUEST['orderby'], array_keys( $this->get_sortable_columns())) ) ? $_REQUEST['orderby'] : 'id_nhanvien';
        $order   = (isset( $_REQUEST['order'] ) && in_array($_REQUEST['order'], array( 'asc', 'desc' ))) ? $_REQUEST['order'] : 'asc';
 
        $this->items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged ), ARRAY_A );

        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page'    => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
    
    public function tt_page_nhanvien_callback(){
        global $wpdb;
        $table = new TT_Nhanvien();
        $table->prepare_items();
        $message = '';
        if ('delete' === $table->current_action()) {
            $message = '<div class="updated below-h2" id="message"><p>' . sprintf( __( 'Số bản ghi đã xóa: %d', 'custom_table_example'), count( $_REQUEST['id_duan']) ) . '</p></div>';
        }
?>
    <div class="wrap">
        <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
        <h2><?php _e( 'Danh sách nhân viên', 'simple_plugin' )?> <a class="add-new-h2" href="<?php echo get_admin_url( get_current_blog_id(), 'admin.php?page=new_duan');?>"><?php _e( 'Thêm mới nhân viên', 'simple_plugin' )?></a></h2>
        <?php echo $message; ?>
        <form id="nhanvien-table" method="GET">
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
            <?php $table->display(); ?>
        </form>
    </div>
<?php     
    }//End function tt_nhanvien_page_callback
    
    
    public function tt_new_nhanvien_callback(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'nhanvien'; 
        $message = '';
        $notice = '';
    
        $default = array(
            'id_nhanvien'       => 0,
            'hoten'             => '',
            'namsinh'           => '',
            'gioitinh'          => '',
            'quequan'           => '',
            'cac_duan'          => '',
            'cac_kynang'        => ''
        );
        if ( wp_verify_nonce( $_REQUEST['nonce'], basename(__FILE__)) ) {
            $item = shortcode_atts( $default, $_REQUEST );
            
            $item_valid = self::tt_validate_data_nhanvien( $item );
            if ( $item_valid === true ) {
                if ( $item['id_nhanvien'] == 0 ) {
                    $result = $wpdb->insert( $table_name, $item );
                    $item['id_nhanvien'] = $wpdb->insert_id;
                    if ( $result ) {
                        $message = __( 'Thêm dữ liệu thành công', 'simple_plugin' );
                    } else {
                        $notice = __( 'Xảy ra lỗi trong quá trình thêm dữ liệu', 'simple_plugin' );
                    }
                } else {
                    $result = $wpdb->update( $table_name, $item, array( 'id_nhanvien' => $item['id_nhanvien']) );
                    if ( $result ) {
                        $message = __( 'Cập nhật dữ liêu thành công', 'simple_plugin' );
                    } else {
                        $notice = __( 'Xảy ra lỗi trong quá trình cập nhật dữ liệu', 'simple_plugin' );
                    }
                }
            } else {
                $notice = $item_valid;
            }
        }
        else {
            $item = $default;
            if ( isset( $_REQUEST['id_nhanvien'] ) ) {
                $item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id_nhanvien = %d", $_REQUEST['id_nhanvien']), ARRAY_A );
                if ( !$item ) {
                    $item = $default;
                    $notice = __( 'Không tìm thấy dữ liệu', 'simple_plugin' );
                }
            }
        }
        ?>
        <div class="wrap">
            <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
            <h2><?php _e( 'Thêm mới nhân viên', 'simple_plugin')?> <a class="add-new-h2" href="<?php echo get_admin_url( get_current_blog_id(), 'admin.php?page=ds_duan');?>"><?php _e( 'Danh sách nhân viên', 'simple_plugin' ); ?></a></h2>
            <?php if ( !empty( $notice ) ){ ?>
                <div id="notice" class="error"><p><?php echo $notice ?></p></div>
            <?php }// !empty( $notice ) ?>
            <?php if ( !empty($message) ){ ?>
                <div id="message" class="updated"><p><?php echo $message ?></p></div>
            <?php }//!empty( $message ) ?>
            <form id="form" method="POST">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
                <input type="hidden" name="id_nhanvien" value="<?php echo $item['id_nhanvien'] ?>"/>
                <div class="metabox-holder" id="poststuff">
                    <div id="post-body">
                        <div id="post-body-content">
                            <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
                                <tbody>
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="hoten"><?php _e( 'Họ tên', 'simple_plugin' ); ?></label>
                                        </th>
                                        <td>
                                            <input id="hoten" name="hoten" type="text" style="width: 95%" value="<?php if( !empty( $item['hoten'] ) ) echo esc_attr( $item['hoten'] );?>" class="code"  required />
                                        </td>
                                    </tr>
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="namsinh"><?php _e( 'Năm sinh', 'simple_plugin' ); ?></label>
                                        </th>
                                        <td>
                                            <select id="namsinh" name="namsinh" class="code" style="width: 15%;">
                                                <?php 
                                                    $selected_year = 1990;
                                                    for( $i = 1930; $i <= date('Y'); $i++ ){ 
                                                ?>
                                                    <option value="<?php echo $i; ?>" <?php if( empty( $item['namsinh'] ) ){ if( $i == $selected_year ){ echo 'selected="selected"'; } }else{ TT_Teamwork::tt_selected( $item['namsinh'], $i ); } ?>><?php echo $i; ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="gioitinh"><?php _e( 'Giới tính', 'simple_plugin' ); ?></label>
                                        </th>
                                        <td>
                                            <input type="radio" name="gioitinh" id="nam" value="Nam" <?php if( empty( $item['gioitinh'] ) ){ echo 'checked="checked"'; }else{  TT_Teamwork::tt_selected( $item['gioitinh'], "Nam" ); } ?> />Nam
                                            <input type="radio" name="gioitinh" id="nu" value="Nữ" accept="<?php if( !empty( $item['gioitinh']) ){ TT_Teamwork::tt_selected( $item['gioitinh'], "Nữ" ); } ?>" />Nữ
                                        </td>
                                    </tr>
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="quequan"><?php _e( 'Quê quán', 'simple_plugin' ); ?></label>
                                        </th>
                                        <td>
                                            <input id="quequan" name="quequan" type="text" style="width: 95%" value="<?php if( !empty( $item['quequan'] ) ) echo esc_attr( $item['quequan'] );?>" class="code"  required />
                                        </td>
                                    </tr>
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="cac_duan"><?php _e( 'Các dự án', 'simple_plugin' ); ?></label>
                                        </th>
                                        <td>
                                            <select id="cac_duan" name="cac_duan" class="code">
                                                <option value="Đã hoàn thành" <?php if( !empty( $item['trangthai']) ){ TT_Teamwork::tt_selected( $item['trangthai'], 'Đã hoàn thành' ); }?> >Đã hoàn thành</option>
                                                <option value="Đang triển khai" <?php if( empty( $item['trangthai'] ) ){ echo 'selected="selected"'; }else{ TT_Teamwork::tt_selected( $item['trangthai'], 'Đang triển khai' ); } ?> >Đang triển khai</option>
                                                <option value="Chưa hoàn thành" <?php if( !empty( $item['trangthai']) ){ TT_Teamwork::tt_selected( $item['trangthai'], 'Chưa hoàn thành' ); } ?> >Chưa hoàn thành</option>
                                                <option value="Đã hủy" <?php if( !empty( $item['trangthai']) ){ TT_Teamwork::tt_selected( $item['trangthai'], 'Đã hủy' ); } ?> >Đã hủy</option>
                                            </select>
                                        </td>
                                    </tr> 
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="cac_kynang"><?php _e( 'Các kỹ năng', 'simple_plugin' ); ?></label>
                                        </th>
                                        <td>
                                            <select id="cac_kynang" name="cac_kynang" class="code">
                                                <option value="Đã hoàn thành" <?php if( !empty( $item['trangthai']) ){ TT_Teamwork::tt_selected( $item['trangthai'], 'Đã hoàn thành' ); }?> >Đã hoàn thành</option>
                                                <option value="Đang triển khai" <?php if( empty( $item['trangthai'] ) ){ echo 'selected="selected"'; }else{ TT_Teamwork::tt_selected( $item['trangthai'], 'Đang triển khai' ); } ?> >Đang triển khai</option>
                                                <option value="Chưa hoàn thành" <?php if( !empty( $item['trangthai']) ){ TT_Teamwork::tt_selected( $item['trangthai'], 'Chưa hoàn thành' ); } ?> >Chưa hoàn thành</option>
                                                <option value="Đã hủy" <?php if( !empty( $item['trangthai']) ){ TT_Teamwork::tt_selected( $item['trangthai'], 'Đã hủy' ); } ?> >Đã hủy</option>
                                            </select>
                                        </td>
                                    </tr>
                                    
                                </tbody>
                            </table>
                            <input type="submit" value="<?php _e( 'Gửi', 'simple_plugin' ); ?>" id="submit" class="button-primary" name="submit">
                        </div>
                    </div>
                </div>
            </form>
        </div>
<?php        
    }//End function tt_new_nhanvien_page_callback()
    
    
    public static function tt_validate_data_nhanvien( $item ){
        $messages = array();
        if ( empty( $item['hoten'] ) ){ 
            $messages[] = __( 'Vui lòng nhập vào họ tên', 'simple_plugin' );
        }
        
        if ( empty( $item['namsinh'] ) ){ 
            $messages[] = __( 'Vui chọn năm sinh', 'simple_plugin' );
        }
        
        if ( empty( $item['gioitinh'] ) ){ 
            $messages[] = __( 'Vui lòng chọn giới tính', 'simple_plugin' );
        }
        
        if( empty( $item['quequan'] ) ){
            $messages[] = __( "Vui lòng nhập vào quê quán", "simple_plugin" );
        }
        
        if ( empty($messages ) ){
            return true; 
        }else{
            return implode( '<br />', $messages );
        }
        
    }
    
}

new TT_Nhanvien();