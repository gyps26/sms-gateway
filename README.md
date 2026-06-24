# SMS Gateway

This application is built using Laravel, Inertia.js, and Vue.js so make sure you are familiar with these technologies. You can check out the following video tutorial to get started if you are new to them.

https://youtu.be/QyqrYdhSku0

## Custom Development

To customize the application for your needs, follow these steps:

1. **Extract the zip file**
   Extract the zip file to your local machine:
   ```bash
   unzip sms-gateway.zip
   ```

2. **Install Dependencies**
   Install PHP and JavaScript dependencies:
   ```bash
   composer install
   npm install
   ```

3. **Database Setup**
   Set up your database in the `.env` file and run migrations:
   ```bash
   php artisan migrate
   ```

4. **Development Server**
   Start the development servers:
   ```bash
   php artisan serve
   npm run dev
   ```

5. **Frontend Customization**
   Modify Vue components located in the `resources/js` directory for frontend changes.

6. **Backend Customization**
   Update Laravel controllers, models, and routes in the `app` and `routes` directories for backend changes.

7. **Build for Production**
   When ready for production, build the frontend assets:
   ```bash
   npm run build
   ```
   If you just want to deploy changes in the frontend then you can upload the `public/build` directory to your server. Make sure to remove the old directory from your server before uploading the new one.

For further assistance, refer to the [Laravel](https://laravel.com/docs/12.x) and [Inertia](https://inertiajs.com) documentation.

## Adding a New Custom Message Gateway

The `MessageGateway` contract defines the structure and behavior for integrating different message-sending services. Developers can implement this contract to add support for new message gateways.

---

### **Steps to Implement the MessageGateway Contract**

1. **Create a New Class**
   - Create a new class in the `App\MessageGateways` namespace.
   - The class must implement the `App\Contracts\MessageGateway` interface.

2. **Constructor**
   - Accept an instance of the `SendingServer` model in the constructor.
   - Store the `SendingServer` instance for use in the implementation.

3. **Required Methods**

   Implement the following methods as defined in the `MessageGateway` contract:

   - **`public static function name(): string`**
     - Return the name of the gateway (e.g., "Twilio", "Textlocal").
     - It must be unique and user-friendly.

   - **`public static function fields(): array`**
     - Define the configuration fields required for the gateway.
     - Each field should include:
       - `label`: Display name for the field.
       - `type`: Field type (`text`, `number`, `boolean`, `list`, or `dictionary`).
       - `options`: (Optional) Required for `list` type fields.
       - `default`: (Optional) Default value for the field.

   - **`public static function supportedTypes(): array`**
     - Return an array of supported message types (e.g., `['SMS', 'MMS', 'WhatsApp']`).

   - **`public function send(Message $message): bool`**
     - Implement the logic to send a message using the gateway.
     - You can use the configuration fields defined in the `fields()` method from `$this->sendingServer->config` object.
     - Return `true` if the message is sent successfully, otherwise `false`.

   - **`public function handleWebhook(Request $request): Response`**
     - Handle incoming webhooks from the gateway to process delivery reports for sent messages.
     - Return an appropriate HTTP response.

---

### **Example Implementation**

Below is an example of implementing the `MessageGateway` contract for a fictional gateway:

```php
<?php

namespace App\MessageGateways;

use App\Contracts\MessageGateway;
use App\Models\Message;
use App\Models\SendingServer;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExampleGateway implements MessageGateway
{
    private SendingServer $sendingServer;

    public function __construct(SendingServer $sendingServer)
    {
        $this->sendingServer = $sendingServer;
    }

    public static function name(): string
    {
        return 'ExampleGateway';
    }

    public static function fields(): array
    {
        return [
            'api_key' => [
                'label' => 'API Key',
                'type' => 'text',
            ],
            'max_retries' => [
                'label' => 'Max Retries',
                'type' => 'number',
                'default' => 3,
            ],
            'use_sandbox' => [
                'label' => 'Use Sandbox',
                'type' => 'boolean',
                'default' => false,
            ],
            'priority' => [
                'label' => 'Priority',
                'type' => 'list',
                'options' => ['Low', 'Normal', 'High'],
                'default' => 'Normal',
            ],
            'headers' => [
                'label' => 'Custom Headers',
                'type' => 'dictionary',
                'default' => [
                    ['key' => 'Content-Type', 'value' => 'application/json'],
                ],
            ],
        ];
    }

    public static function supportedTypes(): array
    {
        return ['SMS'];
    }

    public function send(Message $message): bool
    {
        // Implement the logic to send a message using the ExampleGateway API
        // Use $this->sendingServer->config->api_key, $this->sendingServer->config->url etc. for configuration values.
        return true;
    }

    public function handleWebhook(Request $request): Response
    {
        // Handle incoming webhook requests
        return response()->json(null, 204);
    }
}
```

## Adding a New Custom Payment Gateway

The `PaymentGateway` contract defines the structure and behavior for integrating different payment services. Developers can implement this contract to add support for new payment gateways.

---

### **Steps to Implement the PaymentGateway Contract**

1. **Create a New Class**
   - Create a new class in the `App\PaymentGateways` namespace.
   - The class must implement the `App\Contracts\PaymentGateway` interface.

2. **Constructor**
   - Initialize any required configuration or dependencies for the payment gateway.

3. **Required Methods**

   Implement the following methods as defined in the `PaymentGateway` contract:

   - **`public static function label(): string`**
     - Return the display name of the payment gateway.
     - It must be unique and user-friendly.

   - **`public static function fields(): array`**
     - Define the configuration fields required for the payment gateway.
     - Each field should include:
       - `label`: The display name of the field.
       - `type`: The type of the field (`text`, `boolean` or `textarea`).

   - **`public function initPayment(Plan $plan, Collection $taxes, ?Coupon $coupon, array $billingInfo, int $quantity = 1): RedirectResponse|Response`**
     - Implement the logic to initialize a payment for a subscription plan.
     - Redirect the user to the payment gateway's checkout page or return a response.

   - **`public function handleWebhook(Request $request): Response`**
     - Handle incoming webhook requests from the payment gateway.
     - Process the new payments and update the subscription status accordingly.

   - **`public function callback(Request $request): RedirectResponse`**
     - Handle the callback after a payment is completed or canceled.

   - **`public function cancelSubscription(string $id): void`**
     - Implement the logic to cancel a subscription.

---

### **Example Implementation**

Below is an example of implementing the `PaymentGateway` contract for a fictional payment gateway:

```php
<?php

namespace App\PaymentGateways;

use App\Contracts\PaymentGateway;
use App\Models\Coupon;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExamplePaymentGateway implements PaymentGateway
{

    private string $apiKey;
    private string $apiSecret;
    private bool $sandbox;

    public function __construct()
    {
        // The gateway's name in the config file must be a slug derived from its label using the Str::slug method.
        $this->apiKey = config("payment-gateways.example-payment-gateway.api_key");
        $this->apiSecret = config("payment-gateways.example-payment-gateway.api_secret");
        $this->sandbox = config("payment-gateways.example-payment-gateway.sandbox");
    }

    public static function label(): string
    {
        return 'Example Payment Gateway';
    }

    public static function fields(): array
    {
        return [
            'api_key' => [
                'label' => 'API Key',
                'type' => 'text',
            ],
            'api_secret' => [
                'label' => 'API Secret',
                'type' => 'text',
            ],
            'test_mode' => [
                'label' => 'Sandbox',
                'type' => 'boolean'
            ],
        ];
    }

    public function initPayment(
        Plan $plan,
        Collection $taxes,
        ?Coupon $coupon,
        array $billingInfo,
        int $quantity = 1
    ): RedirectResponse|Response {
        // Implement the logic to initialize a payment
        return redirect('https://example-payment-gateway.com/checkout');
    }

    public function handleWebhook(Request $request): Response
    {
        // Handle incoming webhook requests
        return response()->json(['status' => 'success']);
    }

    public function callback(Request $request): RedirectResponse
    {
        // Handle the callback after payment
        return redirect()->route('subscribe')->with('success', 'Payment completed.');
    }

    public function cancelSubscription(string $id): void
    {
        // Implement the logic to cancel a subscription
    }
}
```