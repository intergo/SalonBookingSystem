<?php

class SLNSMSTOSMS_Provider extends SLN_Action_Sms_Abstract
{
    public function send($to, $message, $sms_prefix = '')
    {
        $url = "https://api.sms.to/sms/send/";

        $args = array(
            'headers' => array(
                'authorization' => 'Bearer ' . $this->getPassword(),
                'content-type'  => 'application/json',
            ),
            'body' => json_encode(array(
                'to'    => $this->processTo($to, $sms_prefix),
                'message'    => $message,
                'sender_id'    => $this->getFrom(),
            )),
        );

        $response = wp_remote_post($url, $args);
        $body     = json_decode(wp_remote_retrieve_body($response), true);

        if ($body['success'] !== true) {
            $this->createException('smsto: ' . $body['message'], 1001);
        }
    }
}
