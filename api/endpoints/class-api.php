class WP_PRM_API {
    public function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes() {
        register_rest_route('prm/v1', '/assets', [
            'methods' => 'POST',
            'callback' => [$this, 'save_asset'],
            'permission_callback' => [$this, 'check_permissions'],
        ]);

        register_rest_route('prm/v1', '/assets', [
            'methods' => 'GET',
            'callback' => [$this, 'get_assets'],
            'permission_callback' => [$this, 'check_permissions'],
        ]);
    }
}

new WP_PRM_API();


    public function check_permissions() {
        return current_user_can('manage_options');
    }

    public function save_asset($request) {
        $data = $request->get_json_params();

        // Validate and sanitize input data
        $asset_url = isset($data['asset_url']) ? esc_url_raw($data['asset_url']) : '';
        $asset_tags = isset($data['asset_tags']) ? sanitize_text_field($data['asset_tags']) : '';

        if (empty($asset_url)) {
            return new WP_Error('invalid_data', 'Asset URL is required.', ['status' => 400]);
        }

        // Create or update the asset post
        $post_id = wp_insert_post([
            'post_title' => 'Asset - ' . time(),
            'post_content' => '',
            'post_type' => 'assets',
            'post_status' => 'publish',
        ]);

        if ($post_id && !is_wp_error($post_id)) {
            update_post_meta($post_id, 'asset_url', $asset_url);
            update_post_meta($post_id, 'asset_tags', $asset_tags);
            return new WP_REST_Response(['message' => 'Asset saved'], 200);
        }

        return new WP_Error('insert_failed', 'Failed to save asset.', ['status' => 500]);
    }

    public function get_assets($request) {
        $paged = isset($request['page']) ? intval($request['page']) : 1;
        $posts_per_page = isset($request['posts_per_page']) ? intval($request['posts_per_page']) : 10;

        if ($paged < 1) {
            $paged = 1;
        }

        if ($posts_per_page < 1 || $posts_per_page > 50) {
            $posts_per_page = 10;
        }

        $query_args = [
            'post_type' => 'assets',
            'posts_per_page' => $posts_per_page,
            'paged' => $paged,
            'post_status' => 'publish',
        ];

        $assets_query = new WP_Query($query_args);

        if (is_wp_error($assets_query)) {
            return new WP_Error('query_failed', 'Failed to retrieve assets.', ['status' => 500]);
        }

        if (!$assets_query->have_posts()) {
            return new WP_REST_Response([
                'message' => 'No assets found',
                'assets' => [],
                