<?php

class SLNSMSTOSMS_Update_Manager
{
    private $data;
    private $processor;

    public function __construct($data)
    {
        $this->data = $data;
        add_action('admin_init', array($this, 'hook_admin_init'), 0);
        add_action('admin_menu', array($this, 'hook_admin_menu'));
    }


    public function hook_admin_menu()
    {
        $this->page = new SLNSMSTOSMS_Update_Page($this);
    }


    public function hook_admin_init()
    {
        global $pagenow;
    }

    public function get($k)
    {
        if ($k == 'license_key') {
            return get_option($this->data['slug'].'_license_key');
        }
        if ($k == 'license_status') {
            return get_option($this->data['slug'].'_license_status');
        }
        if ($k == 'license_data') {
            return get_option($this->data['slug'].'_license_data');
        }

        return $this->data[$k];
    }

    /**
     * @param $license
     * @return null|WP_Error
     */
    public function activateLicense($key)
    {
        update_option($this->get('slug').'_license_key', $key);
        $response = $this->doCall('activate_license');
        if (is_wp_error($response)) {
            update_option($this->get('slug').'_license_status', $response->get_error_message());
        } else {
            update_option($this->get('slug').'_license_status', $response->license, true);
            update_option($this->get('slug').'_license_data', $response, true);
        }

        return $response;
    }

    /**
     * @return null|WP_Error
     * @throws Exception
     */
    public function deactivateLicense()
    {
        $response = $this->doCall('deactivate_license');
        if (is_wp_error($response)) {
            return $response;
        } elseif ($response->license == 'deactivated') {
            delete_option($this->get('slug').'_license_key');
            delete_option($this->get('slug').'_license_status');
            delete_option($this->get('slug').'_license_data');
        } else {
            throw new Exception('bad license '.$response->license);
        }
    }//93c76d62fa6817a31e2bd7493e9d1797

    /**
     * @param $action
     * @param $license
     * @return string|WP_Error
     */
    public function doCall($action)
    {
        $license  = $this->get('license_key');
        $request  = array(
            'edd_action' => $action,
            'license'    => $license,
            'item_name'  => $this->get('name'),
            'url'        => home_url(),
        );
        $response = wp_remote_get(
            add_query_arg($request, $this->get('store')),
            array('timeout' => 15, 'sslverify' => false)
        );
//        var_dump(array($this->get('store'), $request, $response));

        if (is_wp_error($response)) {
            return $response;
        } else {
            $license_data = json_decode(wp_remote_retrieve_body($response));

            return $license_data;
        }
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->get('license_status') == 'valid';
    }
}