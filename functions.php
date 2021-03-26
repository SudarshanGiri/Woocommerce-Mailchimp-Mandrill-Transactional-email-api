
//woocommerce on order created
function action_woocommerce_order_created($order_id)
{
    $order = new WC_Order($order_id);
    $email = $order->get_billing_email();

    require_once('vendor/autoload.php');
    $mailchimp = new MailchimpTransactional\ApiClient();
    $mailchimp->setApiKey('XXXXXXXXXXXXX'); //transactional mail api key
    /******send order confirmation mail **********/
    $emailTemplateConfirmation="donation-confirmation";
    $emailTemplateReminder="donation-reminder-24hrs";
    foreach ($order->get_items() as $item_id => $item) {
        $product_id = $item->get_product_id();
    }
    if (!empty(get_field('confirmation_email', $product_id))) {
        $emailTemplateConfirmation = get_field('confirmation_email', $product_id);
    }

    if (!empty(get_field('reminder_email', $product_id))) {
        $emailTemplateReminder = get_field('reminder_email', $product_id);
    }

    // echo $emailTemplateConfirmation;
    // echo $emailTemplateReminder;
    // exit;
    $pickup_date=$order->get_meta('_billing_pickup_date');
    $order_cancel_link="https://donatestuffdev.wpengine.com/cancel-order/".$order_id;
    $response = $mailchimp->messages->sendTemplate([
        "template_name" => $emailTemplateConfirmation,
        "template_content" => [['sadasd']],
        "message" => [
            "text"=> "Donation Confirmation",
            "subject"=> "Your Donation is successfully scheduled",
            "from_email"=> "",
            "from_name"=> "The DonateStuff.com Team",
            "to"=> [[
                "email"=> $email,
                "name" => "test email",
                "type" => "to"
            ]],
            "merge" => true,
            "global_merge_vars" =>[[
                'name' => 'SCHEDULED_DATE',
                'content' => $pickup_date,
                'name'=> 'ORDER_CANCEL_LINK',
                'content' => $order_cancel_link,


            ]]
        ]
    ]);
    //**************schedule reminder mail**************
    $pickup_date=$order->get_meta('_billing_pickup_date');
    //remainder date is 1 day before pickup date
    $reminder_date=date('Y-m-d', strtotime('-1 day', strtotime($pickup_date)));
    //send order completed mail
    $response = $mailchimp->messages->sendTemplate([
        "template_name" => $emailTemplateReminder,
        "template_content" => [['sadasd']],
        "message" => [
            "text"=> "Donation Reminder",
            "subject"=> "Donation Remainder: Your donation will be picked up tomorrow",
            "from_email"=> "",
            "from_name"=> "",
            "to"=> [[
                "email"=> $email,
                "name" => "test email",
                "type" => "to"
            ]],
            'global_merge_vars' =>[[
                'name' => 'SCHEDULED_DATE',
                'content' => $pickup_date
            ]],
        ],
        "send_at" => $reminder_date."T17:34:22Z",
    ]);
}
add_action('woocommerce_thankyou', 'action_woocommerce_order_created', 111, 1);

//woocommerce on order status completed (Pickup Successfull)
function action_woocommerce_order_status_completed($order_id)
{
    $emailTemplatePickupSuccessful="pickup-successful";
    $order = new WC_Order($order_id);
    foreach ($order->get_items() as $item_id => $item) {
        $product_id = $item->get_product_id();
    }
    if (!empty(get_field('pickup_successful', $product_id))) {
        $emailTemplatePickupSuccessful = get_field('pickup_successful', $product_id);
    }
  
    $email = $order->get_billing_email();
    require_once('vendor/autoload.php');
    $mailchimp = new MailchimpTransactional\ApiClient();
    $mailchimp->setApiKey('XXXXXXXXXXXXXXXXXXXXXX'); //transactional mail api key
    $response = $mailchimp->messages->sendTemplate([
        "template_name" => $emailTemplatePickupSuccessful,
        "template_content" => [['sadasd']],
        "message" => [
            "text"=> "Donation Picked up",
            "subject"=> "Your donation is successufully picked up",
            "from_email"=> "",
            "from_name"=> "",
            "to"=> [[
                "email"=> $email,
                // "email"=>"binodlamsalny@gmail.com",
                "name" => "test email",
                "type" => "to"
            ]]
        ],
    ]);
};
add_action('woocommerce_order_status_completed', 'action_woocommerce_order_status_completed', 10, 1);

// woocommerce on order cancelled (Pickup Cancelled)
function action_woocommerce_order_status_cancelled($order_id)
{
    $emailTemplatePickupCancelled="donation-pickup-unsuccessful";
    $order = new WC_Order($order_id);
    foreach ($order->get_items() as $item_id => $item) {
        $product_id = $item->get_product_id();
    }
    if (!empty(get_field('pickup_cancelled', $product_id))) {
        $emailTemplatePickupCancelled = get_field('pickup_cancelled', $product_id);
    }
    $email = $order->get_billing_email();
    //send order cancelled mail
    require_once('vendor/autoload.php');
    $mailchimp = new MailchimpTransactional\ApiClient();
    $mailchimp->setApiKey('XXXXXXXXXXXXXXxXXXXXXXX');
    $response = $mailchimp->messages->sendTemplate([
        "template_name" => $emailTemplatePickupCancelled,
        "template_content" => [['sadasd']],
        "message" => [
            "text"=> "Donation Cancelled",
            "subject"=> "Pickup Cancelled, Your donation is Cancelled",
            "from_email"=> "info@example.com",
            "from_name"=> "The Example.com Team",
            "to"=> [[
                "email"=> $email,
                "name" => "test email",
                "type" => "to"
            ]]
        ],
    ]);
};
add_action('woocommerce_order_status_cancelled', 'action_woocommerce_order_status_cancelled', 10, 1);

//woocommerce order failed (Pickup  Unsuccessful)
function action_woocommerce_order_status_failed($order_id)
{
    $emailTemplatePickupUnsuccessful="donation-pickup-unsuccessful";
    $order = new WC_Order($order_id);

    foreach ($order->get_items() as $item_id => $item) {
        $product_id = $item->get_product_id();
    }
    if (!empty(get_field('pickup_unsuccessful', $product_id))) {
        $emailTemplatePickupUnsuccessful = get_field('pickup_unsuccessful', $product_id);
    }

    $email = $order->get_billing_email();
    //send order failed mail
    require_once('vendor/autoload.php');
    $mailchimp = new MailchimpTransactional\ApiClient();
    $mailchimp->setApiKey('XXXXXXXXXXXXXXXXXXXXx');
    $response = $mailchimp->messages->sendTemplate([
        "template_name" => $emailTemplatePickupUnsuccessful,
        "template_content" => [['sadasd']],
        "message" => [
            "text"=> "Order Failed",
            "subject"=> "Pickup Unsuccessful, Your donation couldnt be collected",
            "from_email"=> "info@example.com",
            "from_name"=> "The Example.com Team",
            "to"=> [[
                "email"=> $email,
                "name" => "test email",
                "type" => "to"
            ]]
        ],
    ]);
};
add_action('woocommerce_order_status_failed', 'action_woocommerce_order_status_failed', 10, 1);

// for cancelling order on clicking the button in the template 

$request_cancel_order=$_SERVER['REQUEST_URI'];
$request_values = explode("/", $request_cancel_order);
if($request_values[1]=='cancel-order'){
    function action_woocommerce_before_main_content() { 
        $request_cancel_order=$_SERVER['REQUEST_URI'];
        $request_values = explode("/", $request_cancel_order);
        $order_id_cancel=$request_values[2];
        $request_values = explode("/", $request_cancel_order);
        $order = new WC_Order($order_id_cancel);
        $order->update_status('cancelled');
        echo "<script>window.location.href='/';</script>";

    }; 
    add_action( 'init', 'action_woocommerce_before_main_content', 10, 2 ); 
    
}
  
?>
