<?php

class SLNSMSTOSMS_Update_Page
{
    /** @var SLNSHSMS_Update_Manager */
    private $updater;
    private $pageSlug;
    private $pageName;

    public function __construct(SLNSMSTOSMS_Update_Manager $updater)
    {
        $this->updater  = $updater;
        $this->pageName = $this->updater->get('name').' License';
        $this->pageSlug = $this->updater->get('slug').'-license';
        add_plugins_page($this->pageName, $this->pageName, 'manage_options', $this->pageSlug, array($this, 'render'));
        add_action('admin_notices', array($this, 'hook_admin_notices'));
    }

    public function hook_admin_notices()
    {
        if (!$this->updater->isValid() && (empty($_GET['page']) || $_GET['page'] != $this->pageSlug)) {
            $licenseUrl = admin_url('/plugins.php?page='.$this->pageSlug);
            ?>
            <div id="sln-setting-error" class="updated error">
                <h3><?php echo esc_html($this->updater->get('name').__(' needs a valid license', 'slnsmstosms')) ?></h3>
                <p><a href="<?php echo esc_url($licenseUrl) ?>"><?php _e('<p>Please insert your license key', 'slnsmstosms'); ?></a>
                </p>
            </div>
            <?php
        }
    }

    public function render()
    {

        if (isset($_POST['submit']) && isset($_POST['license_key'])) {
            $response = $this->updater->activateLicense($_POST['license_key']);
            if (is_wp_error($response)) {
                ?>
                <div id="sln-setting-error" class="updated error">
                    <p><?php echo esc_attr('ERROR: '.$response->get_error_code().' - '.$response->get_error_message()) ?></p>
                </div>
                <?php
            } else {
                ?>
                <div id="sln-setting-error" class="updated success">
                    <p><?php echo esc_attr(__('License updated with success', 'slnsmstosms')) ?></p>
                </div>
                <?php
            }
        }
        if (isset($_POST['license_deactivate'])) {
            $response = $this->updater->deactivateLicense();
            if (is_wp_error($response)) {
                ?>
                <div id="sln-setting-error" class="updated error">
                    <p><?php echo esc_attr($response->get_error_code().' - '.$response->get_error_message()) ?></p>
                </div>
                <?php
            } else {

                ?>
                <div id="sln-setting-error" class="updated success">
                    <p><?php echo esc_attr(__('License deactivated with success', 'slnsmstosms')) ?></p>
                </div>
                <?php
            }
        }
        $license = $this->updater->get('license_key');
        $status  = $this->updater->get('license_status');
        $data    = $this->updater->get('license_data');
        ?>
        <div class="wrap">
        <h2><?php echo esc_attr($this->pageName) ?></h2>
        <form method="post" action="?page=<?php echo esc_url($this->pageSlug) ?>">
            <table class="form-table">
                <tbody>
                <tr valign="top">
                    <th scope="row" valign="top">
                        <?php _e('License Key', 'slnsmstosms'); ?>
                    </th>
                    <td>
                        <input id="license_key" name="license_key" type="text" class="regular-text"
                               required="required"
                               value="<?php esc_attr_e($license); ?>"/>
                        <?php if (empty($license)): ?>
                            <label class="description" for="license_key"><?php _e(
                                    'Enter your license key',
                                    'slnsmstosms'
                                ); ?></label>
                        <?php endif ?>
                    </td>
                </tr>
                <?php if ($license) { ?>
                    <tr valign="top">
                        <th scope="row" valign="top">
                            <?php _e('License State', 'slnsmstosms'); ?>
                        </th>
                        <td>
                            <?php if ($status == 'valid') { ?>
                                <span style="color:green;"><?php _e('active', 'slnsmstosms'); ?></span>
                                <?php wp_nonce_field('nonce', 'nonce'); ?>&nbsp;
                                <input type="submit" class="button-secondary" name="license_deactivate"
                                       value="<?php _e('Deactivate License', 'slnsmstosms'); ?>"/>
                            <?php } elseif ($status == 'invalid') { ?>
                                <span style="color:red;"><?php _e('invalid', 'slnsmstosms'); ?></span>
                                <?php
                            } else { ?>
                                <span style="color:orange;">
                                    <?php _e('error', 'slnsmstosms'); ?>
                                    <?php echo esc_attr(' '.$status) ?>
                                </span>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" valign="top">
                            <?php _e('Payment id', 'slnsmstosms'); ?>
                        </th>
                        <td>
                            <?php echo esc_attr($data->payment_id) ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" valign="top">
                            <?php _e('Customer name', 'slnsmstosms'); ?>
                        </th>
                        <td>
                            <?php echo esc_attr($data->customer_name) ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" valign="top">
                            <?php _e('Customer email', 'slnsmstosms'); ?>
                        </th>
                        <td>
                            <?php echo esc_attr($data->customer_email) ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" valign="top">
                            <?php _e('Expires', 'slnsmstosms'); ?>
                        </th>
                        <td>
                            <?php echo esc_attr($data->expires) ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php if ($status != 'valid') { ?>
                <?php submit_button(); ?>
            <?php } ?>
        </form>
        <?php
    }
}