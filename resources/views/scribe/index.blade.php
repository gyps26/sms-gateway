<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>SMS Gateway API Documentation</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.style.css") }}" media="screen">
    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.print.css") }}" media="print">

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>

    <link rel="stylesheet"
          href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/obsidian.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jets/0.14.1/jets.min.js"></script>

    <style id="language-style">
        /* starts out as display none and is replaced with js later  */
                    body .content .bash-example code { display: none; }
                    body .content .javascript-example code { display: none; }
                    body .content .php-example code { display: none; }
                    body .content .python-example code { display: none; }
            </style>

    <script>
        var tryItOutBaseUrl = "{{ config("app.url") }}";
        var useCsrf = Boolean();
        var csrfUrl = "/sanctum/csrf-cookie";
    </script>
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.3.0.js") }}"></script>

    <script src="{{ asset("/vendor/scribe/js/theme-default-5.3.0.js") }}"></script>

</head>

<body data-languages="[&quot;bash&quot;,&quot;javascript&quot;,&quot;php&quot;,&quot;python&quot;]">

<a href="#" id="nav-button">
    <span>
        MENU
        <img src="{{ asset("/vendor/scribe/images/navbar.png") }}" alt="navbar-image"/>
    </span>
</a>
<div class="tocify-wrapper">
    
            <div class="lang-selector">
                                            <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                            <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                                            <button type="button" class="lang-button" data-language-name="php">php</button>
                                            <button type="button" class="lang-button" data-language-name="python">python</button>
                    </div>
    
    <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Search">
    </div>

    <div id="toc">
                    <ul id="tocify-header-introduction" class="tocify-header">
                <li class="tocify-item level-1" data-unique="introduction">
                    <a href="#introduction">Introduction</a>
                </li>
                            </ul>
                    <ul id="tocify-header-authenticating-requests" class="tocify-header">
                <li class="tocify-item level-1" data-unique="authenticating-requests">
                    <a href="#authenticating-requests">Authenticating requests</a>
                </li>
                            </ul>
                    <ul id="tocify-header-call-log" class="tocify-header">
                <li class="tocify-item level-1" data-unique="call-log">
                    <a href="#call-log">Call Log</a>
                </li>
                                    <ul id="tocify-subheader-call-log" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="call-log-GETapi-v1-calls">
                                <a href="#call-log-GETapi-v1-calls">List all calls</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="call-log-DELETEapi-v1-calls--id-">
                                <a href="#call-log-DELETEapi-v1-calls--id-">Delete a call</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-campaigns" class="tocify-header">
                <li class="tocify-item level-1" data-unique="campaigns">
                    <a href="#campaigns">Campaigns</a>
                </li>
                                    <ul id="tocify-subheader-campaigns" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="campaigns-GETapi-v1-campaigns">
                                <a href="#campaigns-GETapi-v1-campaigns">List all campaigns</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="campaigns-GETapi-v1-campaigns--id-">
                                <a href="#campaigns-GETapi-v1-campaigns--id-">Retrieve a campaign</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="campaigns-GETapi-v1-campaigns--campaign_id--devices">
                                <a href="#campaigns-GETapi-v1-campaigns--campaign_id--devices">List all devices used in a campaign</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="campaigns-GETapi-v1-campaigns--campaign_id--sending-servers">
                                <a href="#campaigns-GETapi-v1-campaigns--campaign_id--sending-servers">List all sending servers used in a campaign</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="campaigns-PUTapi-v1-campaigns--id-">
                                <a href="#campaigns-PUTapi-v1-campaigns--id-">Update a campaign</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="campaigns-DELETEapi-v1-campaigns--id-">
                                <a href="#campaigns-DELETEapi-v1-campaigns--id-">Delete a campaign</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-contact-lists" class="tocify-header">
                <li class="tocify-item level-1" data-unique="contact-lists">
                    <a href="#contact-lists">Contact Lists</a>
                </li>
                                    <ul id="tocify-subheader-contact-lists" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="contact-lists-GETapi-v1-contact-lists">
                                <a href="#contact-lists-GETapi-v1-contact-lists">List all contact lists</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="contact-lists-GETapi-v1-contact-lists--id-">
                                <a href="#contact-lists-GETapi-v1-contact-lists--id-">Retrieve a contact list</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="contact-lists-POSTapi-v1-contact-lists">
                                <a href="#contact-lists-POSTapi-v1-contact-lists">Create a contact list</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="contact-lists-PUTapi-v1-contact-lists--id-">
                                <a href="#contact-lists-PUTapi-v1-contact-lists--id-">Update a contact list</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="contact-lists-DELETEapi-v1-contact-lists--id-">
                                <a href="#contact-lists-DELETEapi-v1-contact-lists--id-">Delete a contact list</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="contact-lists-GETapi-v1-contact-lists--contact_list_id--fields">
                                <a href="#contact-lists-GETapi-v1-contact-lists--contact_list_id--fields">List all fields for a contact list</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="contact-lists-GETapi-v1-fields--id-">
                                <a href="#contact-lists-GETapi-v1-fields--id-">Retrieve a field</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="contact-lists-POSTapi-v1-contact-lists--contact_list_id--fields">
                                <a href="#contact-lists-POSTapi-v1-contact-lists--contact_list_id--fields">Create a field for a contact list</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="contact-lists-PUTapi-v1-fields--id-">
                                <a href="#contact-lists-PUTapi-v1-fields--id-">Update a field</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="contact-lists-DELETEapi-v1-fields--id-">
                                <a href="#contact-lists-DELETEapi-v1-fields--id-">Delete a field</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-contacts" class="tocify-header">
                <li class="tocify-item level-1" data-unique="contacts">
                    <a href="#contacts">Contacts</a>
                </li>
                                    <ul id="tocify-subheader-contacts" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="contacts-GETapi-v1-contact-lists--contact_list_id--contacts">
                                <a href="#contacts-GETapi-v1-contact-lists--contact_list_id--contacts">List all contacts for a contact list</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="contacts-GETapi-v1-contacts--id-">
                                <a href="#contacts-GETapi-v1-contacts--id-">Retrieve a contact</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="contacts-POSTapi-v1-contact-lists--contact_list_id--contacts">
                                <a href="#contacts-POSTapi-v1-contact-lists--contact_list_id--contacts">Create a contact in a contact list</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="contacts-PUTapi-v1-contacts--id-">
                                <a href="#contacts-PUTapi-v1-contacts--id-">Update a contact</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="contacts-DELETEapi-v1-contacts--id-">
                                <a href="#contacts-DELETEapi-v1-contacts--id-">Delete a contact</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-devices" class="tocify-header">
                <li class="tocify-item level-1" data-unique="devices">
                    <a href="#devices">Devices</a>
                </li>
                                    <ul id="tocify-subheader-devices" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="devices-GETapi-v1-devices">
                                <a href="#devices-GETapi-v1-devices">List all devices</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="devices-GETapi-v1-devices--id-">
                                <a href="#devices-GETapi-v1-devices--id-">Retrieve a device</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="devices-PUTapi-v1-devices--id-">
                                <a href="#devices-PUTapi-v1-devices--id-">Update a device</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="devices-DELETEapi-v1-devices--id-">
                                <a href="#devices-DELETEapi-v1-devices--id-">Delete a device</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-messages" class="tocify-header">
                <li class="tocify-item level-1" data-unique="messages">
                    <a href="#messages">Messages</a>
                </li>
                                    <ul id="tocify-subheader-messages" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="messages-GETapi-v1-messages-send">
                                <a href="#messages-GETapi-v1-messages-send">Send messages</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="messages-GETapi-v1-messages">
                                <a href="#messages-GETapi-v1-messages">List all messages</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="messages-GETapi-v1-messages--id-">
                                <a href="#messages-GETapi-v1-messages--id-">Retrieve a message</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="messages-DELETEapi-v1-messages--id-">
                                <a href="#messages-DELETEapi-v1-messages--id-">Delete a message</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-senderids" class="tocify-header">
                <li class="tocify-item level-1" data-unique="senderids">
                    <a href="#senderids">SenderIds</a>
                </li>
                                    <ul id="tocify-subheader-senderids" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="senderids-GETapi-v1-sender-ids">
                                <a href="#senderids-GETapi-v1-sender-ids">List all sender ids</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="senderids-GETapi-v1-sender-ids--id-">
                                <a href="#senderids-GETapi-v1-sender-ids--id-">Retrieve a sender id</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="senderids-POSTapi-v1-sending-servers--sending_server_id--sender-ids">
                                <a href="#senderids-POSTapi-v1-sending-servers--sending_server_id--sender-ids">Create a sender id for a sending server</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="senderids-DELETEapi-v1-sender-ids--id-">
                                <a href="#senderids-DELETEapi-v1-sender-ids--id-">Delete a sender id</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-sending-servers" class="tocify-header">
                <li class="tocify-item level-1" data-unique="sending-servers">
                    <a href="#sending-servers">Sending Servers</a>
                </li>
                                    <ul id="tocify-subheader-sending-servers" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="sending-servers-GETapi-v1-sending-servers">
                                <a href="#sending-servers-GETapi-v1-sending-servers">List all sending servers</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="sending-servers-GETapi-v1-sending-servers--id-">
                                <a href="#sending-servers-GETapi-v1-sending-servers--id-">Retrieve a sending server</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="sending-servers-POSTapi-v1-sending-servers">
                                <a href="#sending-servers-POSTapi-v1-sending-servers">Create a sending server</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="sending-servers-PUTapi-v1-sending-servers--id-">
                                <a href="#sending-servers-PUTapi-v1-sending-servers--id-">Update a sending server</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="sending-servers-DELETEapi-v1-sending-servers--id-">
                                <a href="#sending-servers-DELETEapi-v1-sending-servers--id-">Delete a sending server</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-ussd-pulls" class="tocify-header">
                <li class="tocify-item level-1" data-unique="ussd-pulls">
                    <a href="#ussd-pulls">USSD Pulls</a>
                </li>
                                    <ul id="tocify-subheader-ussd-pulls" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="ussd-pulls-GETapi-v1-ussd-pulls-send">
                                <a href="#ussd-pulls-GETapi-v1-ussd-pulls-send">Send USSD pulls</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="ussd-pulls-GETapi-v1-ussd-pulls">
                                <a href="#ussd-pulls-GETapi-v1-ussd-pulls">List all USSD pulls</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="ussd-pulls-GETapi-v1-ussd-pulls--id-">
                                <a href="#ussd-pulls-GETapi-v1-ussd-pulls--id-">Retrieve a USSD pull</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="ussd-pulls-DELETEapi-v1-ussd-pulls--id-">
                                <a href="#ussd-pulls-DELETEapi-v1-ussd-pulls--id-">Delete a USSD pull</a>
                            </li>
                                                                        </ul>
                            </ul>
            </div>

    <ul class="toc-footer" id="toc-footer">
                    <li style="padding-bottom: 5px;"><a href="{{ route("scribe.postman") }}">View Postman collection</a></li>
                            <li style="padding-bottom: 5px;"><a href="{{ route("scribe.openapi") }}">View OpenAPI spec</a></li>
                <li><a href="http://github.com/knuckleswtf/scribe">Documentation powered by Scribe ✍</a></li>
    </ul>

    <ul class="toc-footer" id="last-updated">
        <li>Last updated: September 8, 2025</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="introduction">Introduction</h1>
<aside>
    <strong>Base URL</strong>: <code>{{ config("app.url") }}</code>
</aside>
<pre><code>This documentation aims to provide all the information you need to work with our API.

&lt;aside&gt;As you scroll, you'll see code examples for working with the API in different programming languages in the dark area to the right (or as part of the content on mobile).
You can switch the language used with the tabs at the top right (or from the nav menu at the top left on mobile).&lt;/aside&gt;</code></pre>

        <h1 id="authenticating-requests">Authenticating requests</h1>
<p>To authenticate requests, include an <strong><code>Authorization</code></strong> header with the value <strong><code>"Bearer {YOUR_AUTH_KEY}"</code></strong>.</p>
<p>All authenticated endpoints are marked with a <code>requires authentication</code> badge in the documentation below.</p>
<p>You can retrieve your token by visiting your dashboard and clicking <b>Generate API token</b>.</p>

        <h1 id="call-log">Call Log</h1>

    <p>Call Log</p>

                                <h2 id="call-log-GETapi-v1-calls">List all calls</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Returns a list of calls. The calls are returned sorted by creation date, with the most recently created call appearing first.</p>

<span id="example-requests-GETapi-v1-calls">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "{{ config("app.url") }}/api/v1/calls?type=Incoming&amp;sim=1&amp;answered=" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/calls"
);

const params = {
    "type": "Incoming",
    "sim": "1",
    "answered": "0",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/calls';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'query' =&gt; [
            'type' =&gt; 'Incoming',
            'sim' =&gt; '1',
            'answered' =&gt; '0',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/calls'
params = {
  'type': 'Incoming',
  'sim': '1',
  'answered': '0',
}
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers, params=params)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-calls">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 141,
            &quot;number&quot;: &quot;973-450-3511&quot;,
            &quot;type&quot;: &quot;Missed&quot;,
            &quot;sim_id&quot;: 1,
            &quot;started_at&quot;: &quot;1991-01-07T14:12:01.000000Z&quot;,
            &quot;duration&quot;: 1912
        },
        {
            &quot;id&quot;: 142,
            &quot;number&quot;: &quot;724-602-7973&quot;,
            &quot;type&quot;: &quot;Voicemail&quot;,
            &quot;sim_id&quot;: 1,
            &quot;started_at&quot;: &quot;1988-04-26T00:07:31.000000Z&quot;,
            &quot;duration&quot;: 1669
        }
    ],
    &quot;links&quot;: {
        &quot;first&quot;: &quot;/?page=1&quot;,
        &quot;last&quot;: &quot;/?page=1&quot;,
        &quot;prev&quot;: null,
        &quot;next&quot;: null
    },
    &quot;meta&quot;: {
        &quot;current_page&quot;: 1,
        &quot;from&quot;: 1,
        &quot;last_page&quot;: 1,
        &quot;links&quot;: [
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;&amp;laquo; Previous&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            },
            {
                &quot;url&quot;: &quot;/?page=1&quot;,
                &quot;label&quot;: &quot;1&quot;,
                &quot;page&quot;: 1,
                &quot;active&quot;: true
            },
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;Next &amp;raquo;&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            }
        ],
        &quot;path&quot;: &quot;/&quot;,
        &quot;per_page&quot;: 100,
        &quot;to&quot;: 2,
        &quot;total&quot;: 2
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-calls" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-calls"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-calls"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-calls" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-calls">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-calls" data-method="GET"
      data-path="api/v1/calls"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-calls', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-calls"
                    onclick="tryItOut('GETapi-v1-calls');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-calls"
                    onclick="cancelTryOut('GETapi-v1-calls');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-calls"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/calls</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-calls"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-calls"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-calls"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="type"                data-endpoint="GETapi-v1-calls"
               value="Incoming"
               data-component="query">
    <br>
<p>Filter by call type. Example: <code>Incoming</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>Incoming</code></li> <li><code>Outgoing</code></li> <li><code>Missed</code></li> <li><code>Voicemail</code></li> <li><code>Rejected</code></li> <li><code>Blocked</code></li> <li><code>Answered Externally</code></li></ul>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>sim</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="sim"                data-endpoint="GETapi-v1-calls"
               value="1"
               data-component="query">
    <br>
<p>Filter by SIM ID. Must be at least 1. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>answered</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
<i>optional</i> &nbsp;
                <label data-endpoint="GETapi-v1-calls" style="display: none">
            <input type="radio" name="answered"
                   value="1"
                   data-endpoint="GETapi-v1-calls"
                   data-component="query"             >
            <code>true</code>
        </label>
        <label data-endpoint="GETapi-v1-calls" style="display: none">
            <input type="radio" name="answered"
                   value="0"
                   data-endpoint="GETapi-v1-calls"
                   data-component="query"             >
            <code>false</code>
        </label>
    <br>
<p>Example: <code>false</code></p>
            </div>
                </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the call.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The phone number of the call.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The type of the call.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>sim_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the SIM used for the call.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>started_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The time when the call was started.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>duration</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The duration of the call in seconds.</p>
        </div>
                        <h2 id="call-log-DELETEapi-v1-calls--id-">Delete a call</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Permanently deletes a call.</p>

<span id="example-requests-DELETEapi-v1-calls--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "{{ config("app.url") }}/api/v1/calls/1" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/calls/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/calls/1';
$response = $client-&gt;delete(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/calls/1'
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('DELETE', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-DELETEapi-v1-calls--id-">
            <blockquote>
            <p>Example response (204, No content):</p>
        </blockquote>
                <pre>
<code>Empty response</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-v1-calls--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-v1-calls--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-v1-calls--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-v1-calls--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-v1-calls--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-v1-calls--id-" data-method="DELETE"
      data-path="api/v1/calls/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-v1-calls--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-v1-calls--id-"
                    onclick="tryItOut('DELETEapi-v1-calls--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-v1-calls--id-"
                    onclick="cancelTryOut('DELETEapi-v1-calls--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-v1-calls--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/v1/calls/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="DELETEapi-v1-calls--id-"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-v1-calls--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-v1-calls--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="DELETEapi-v1-calls--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the call. Example: <code>1</code></p>
            </div>
                    </form>

                <h1 id="campaigns">Campaigns</h1>

    <p>Campaigns</p>

                                <h2 id="campaigns-GETapi-v1-campaigns">List all campaigns</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Returns a list of campaigns. The campaigns are returned sorted by creation date, with the most recently created campaign appearing first.</p>

<span id="example-requests-GETapi-v1-campaigns">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "{{ config("app.url") }}/api/v1/campaigns?status=Sent&amp;type=MMS&amp;recurring=1&amp;after=2021-01-01&amp;before=2025-01-01" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/campaigns"
);

const params = {
    "status": "Sent",
    "type": "MMS",
    "recurring": "1",
    "after": "2021-01-01",
    "before": "2025-01-01",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/campaigns';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'query' =&gt; [
            'status' =&gt; 'Sent',
            'type' =&gt; 'MMS',
            'recurring' =&gt; '1',
            'after' =&gt; '2021-01-01',
            'before' =&gt; '2025-01-01',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/campaigns'
params = {
  'status': 'Sent',
  'type': 'MMS',
  'recurring': '1',
  'after': '2021-01-01',
  'before': '2025-01-01',
}
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers, params=params)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-campaigns">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 7,
            &quot;name&quot;: &quot;Leo Gusikowski&quot;,
            &quot;scheduled_at&quot;: null,
            &quot;timezone&quot;: &quot;Atlantic/Faroe&quot;,
            &quot;recurring&quot;: false,
            &quot;frequency&quot;: null,
            &quot;frequency_unit&quot;: null,
            &quot;ends_at&quot;: null,
            &quot;repeat_at&quot;: null,
            &quot;active_hours&quot;: &quot;09:00-17:00&quot;,
            &quot;days_of_week&quot;: [
                1,
                3,
                5,
                6,
                7
            ],
            &quot;status&quot;: &quot;Processed&quot;,
            &quot;type&quot;: &quot;USSD Pull&quot;,
            &quot;options&quot;: {
                &quot;delay&quot;: &quot;39&quot;,
                &quot;prioritize&quot;: false
            },
            &quot;created_at&quot;: &quot;2025-09-08T05:42:25.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2025-09-08T05:42:25.000000Z&quot;
        },
        {
            &quot;id&quot;: 8,
            &quot;name&quot;: &quot;Mr. Orland Hermiston II&quot;,
            &quot;scheduled_at&quot;: &quot;2026-05-01T21:31:16.000000Z&quot;,
            &quot;timezone&quot;: &quot;America/Boise&quot;,
            &quot;recurring&quot;: false,
            &quot;frequency&quot;: null,
            &quot;frequency_unit&quot;: null,
            &quot;ends_at&quot;: null,
            &quot;repeat_at&quot;: null,
            &quot;active_hours&quot;: &quot;09:00-17:00&quot;,
            &quot;days_of_week&quot;: [
                1,
                2,
                4,
                5
            ],
            &quot;status&quot;: &quot;Processed&quot;,
            &quot;type&quot;: &quot;SMS&quot;,
            &quot;options&quot;: {
                &quot;delay&quot;: &quot;60&quot;,
                &quot;prioritize&quot;: false,
                &quot;delivery_report&quot;: false
            },
            &quot;created_at&quot;: &quot;2025-09-08T05:42:25.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2025-09-08T05:42:25.000000Z&quot;
        }
    ],
    &quot;links&quot;: {
        &quot;first&quot;: &quot;/?page=1&quot;,
        &quot;last&quot;: &quot;/?page=1&quot;,
        &quot;prev&quot;: null,
        &quot;next&quot;: null
    },
    &quot;meta&quot;: {
        &quot;current_page&quot;: 1,
        &quot;from&quot;: 1,
        &quot;last_page&quot;: 1,
        &quot;links&quot;: [
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;&amp;laquo; Previous&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            },
            {
                &quot;url&quot;: &quot;/?page=1&quot;,
                &quot;label&quot;: &quot;1&quot;,
                &quot;page&quot;: 1,
                &quot;active&quot;: true
            },
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;Next &amp;raquo;&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            }
        ],
        &quot;path&quot;: &quot;/&quot;,
        &quot;per_page&quot;: 100,
        &quot;to&quot;: 2,
        &quot;total&quot;: 2
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-campaigns" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-campaigns"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-campaigns"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-campaigns" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-campaigns">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-campaigns" data-method="GET"
      data-path="api/v1/campaigns"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-campaigns', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-campaigns"
                    onclick="tryItOut('GETapi-v1-campaigns');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-campaigns"
                    onclick="cancelTryOut('GETapi-v1-campaigns');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-campaigns"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/campaigns</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-campaigns"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-campaigns"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-campaigns"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="GETapi-v1-campaigns"
               value="Sent"
               data-component="query">
    <br>
<p>Filter by campaign status. Example: <code>Sent</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>Queued</code></li> <li><code>Processing</code></li> <li><code>Processed</code></li> <li><code>Scheduled</code></li> <li><code>Completed</code></li></ul>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="type"                data-endpoint="GETapi-v1-campaigns"
               value="MMS"
               data-component="query">
    <br>
<p>Filter by campaign type. Example: <code>MMS</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>SMS</code></li> <li><code>MMS</code></li> <li><code>WhatsApp</code></li> <li><code>USSD Pull</code></li></ul>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>recurring</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
<i>optional</i> &nbsp;
                <label data-endpoint="GETapi-v1-campaigns" style="display: none">
            <input type="radio" name="recurring"
                   value="1"
                   data-endpoint="GETapi-v1-campaigns"
                   data-component="query"             >
            <code>true</code>
        </label>
        <label data-endpoint="GETapi-v1-campaigns" style="display: none">
            <input type="radio" name="recurring"
                   value="0"
                   data-endpoint="GETapi-v1-campaigns"
                   data-component="query"             >
            <code>false</code>
        </label>
    <br>
<p>Filter by recurring campaigns. Example: <code>true</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>after</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="after"                data-endpoint="GETapi-v1-campaigns"
               value="2021-01-01"
               data-component="query">
    <br>
<p>Filter by campaigns after a certain date. Must be a valid date in the format <code>Y-m-d</code>. Must be a date before or equal to <code>before</code>. Example: <code>2021-01-01</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>before</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="before"                data-endpoint="GETapi-v1-campaigns"
               value="2025-01-01"
               data-component="query">
    <br>
<p>Filter by campaigns before a certain date. Must be a valid date in the format <code>Y-m-d</code>. Must be a date after or equal to <code>after</code>. Example: <code>2025-01-01</code></p>
            </div>
                </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the campaign.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The name of the campaign.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>scheduled_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time when the campaign is scheduled to run.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>timezone</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The timezone of the campaign.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>recurring</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether the campaign is recurring.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>frequency</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The frequency of the campaign.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>frequency_unit</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The unit of the frequency.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>ends_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time when the recurring campaign ends.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>repeat_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time when the campaign is scheduled to repeat.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>active_hours</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The timespan of a day when the campaign is active.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>days_of_week</code></b>&nbsp;&nbsp;
<small>integer[]</small>&nbsp;
 &nbsp;
<br>
<p>The days of the week when the campaign is active.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The status of the campaign.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The type of the campaign.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>options</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>
<p>The options of the campaign.</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>delay</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The delay between each item before sending the next one.</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>prioritize</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether to prioritize the campaign.</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>delivery_report</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether to request delivery report for SMS.</p>
                    </div>
                                    </details>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>created_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time the campaign was created.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>updated_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time the campaign was last updated.</p>
        </div>
                        <h2 id="campaigns-GETapi-v1-campaigns--id-">Retrieve a campaign</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retrieves a Campaign object.</p>

<span id="example-requests-GETapi-v1-campaigns--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "{{ config("app.url") }}/api/v1/campaigns/1" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/campaigns/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/campaigns/1';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/campaigns/1'
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-campaigns--id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;id&quot;: 9,
    &quot;name&quot;: &quot;Stefanie Lebsack&quot;,
    &quot;scheduled_at&quot;: &quot;2025-10-23T01:36:18.000000Z&quot;,
    &quot;timezone&quot;: &quot;Asia/Atyrau&quot;,
    &quot;recurring&quot;: false,
    &quot;frequency&quot;: null,
    &quot;frequency_unit&quot;: null,
    &quot;ends_at&quot;: null,
    &quot;repeat_at&quot;: null,
    &quot;active_hours&quot;: &quot;00:00-23:59&quot;,
    &quot;days_of_week&quot;: [
        1,
        2,
        5,
        6
    ],
    &quot;status&quot;: &quot;Processed&quot;,
    &quot;type&quot;: &quot;SMS&quot;,
    &quot;options&quot;: {
        &quot;delay&quot;: &quot;57&quot;,
        &quot;prioritize&quot;: false,
        &quot;delivery_report&quot;: true
    },
    &quot;created_at&quot;: &quot;2025-09-08T05:42:25.000000Z&quot;,
    &quot;updated_at&quot;: &quot;2025-09-08T05:42:25.000000Z&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-campaigns--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-campaigns--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-campaigns--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-campaigns--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-campaigns--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-campaigns--id-" data-method="GET"
      data-path="api/v1/campaigns/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-campaigns--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-campaigns--id-"
                    onclick="tryItOut('GETapi-v1-campaigns--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-campaigns--id-"
                    onclick="cancelTryOut('GETapi-v1-campaigns--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-campaigns--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/campaigns/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-campaigns--id-"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-campaigns--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-campaigns--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-v1-campaigns--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the campaign. Example: <code>1</code></p>
            </div>
                    </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the campaign.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The name of the campaign.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>scheduled_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time when the campaign is scheduled to run.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>timezone</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The timezone of the campaign.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>recurring</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether the campaign is recurring.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>frequency</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The frequency of the campaign.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>frequency_unit</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The unit of the frequency.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>ends_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time when the recurring campaign ends.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>repeat_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time when the campaign is scheduled to repeat.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>active_hours</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The timespan of a day when the campaign is active.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>days_of_week</code></b>&nbsp;&nbsp;
<small>integer[]</small>&nbsp;
 &nbsp;
<br>
<p>The days of the week when the campaign is active.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The status of the campaign.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The type of the campaign.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>options</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>
<p>The options of the campaign.</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>delay</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The delay between each item before sending the next one.</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>prioritize</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether to prioritize the campaign.</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>delivery_report</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether to request delivery report for SMS.</p>
                    </div>
                                    </details>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>created_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time the campaign was created.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>updated_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time the campaign was last updated.</p>
        </div>
                        <h2 id="campaigns-GETapi-v1-campaigns--campaign_id--devices">List all devices used in a campaign</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Returns a list of devices used in a campaign. The devices are returned with sims used in the campaign and status of the campaign on the device.</p>

<span id="example-requests-GETapi-v1-campaigns--campaign_id--devices">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "{{ config("app.url") }}/api/v1/campaigns/1/devices" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/campaigns/1/devices"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/campaigns/1/devices';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/campaigns/1/devices'
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-campaigns--campaign_id--devices">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 11,
            &quot;name&quot;: null,
            &quot;model&quot;: &quot;Nokia X20&quot;,
            &quot;android_version&quot;: &quot;7&quot;,
            &quot;app_version&quot;: &quot;0.20.34&quot;,
            &quot;battery&quot;: 46,
            &quot;is_charging&quot;: false,
            &quot;enabled&quot;: true,
            &quot;sims&quot;: [
                {
                    &quot;id&quot;: 15,
                    &quot;name&quot;: &quot;Koelpin, Nolan and Bailey&quot;,
                    &quot;label&quot;: null,
                    &quot;number&quot;: &quot;+15202822732&quot;,
                    &quot;country&quot;: &quot;SZ&quot;,
                    &quot;carrier&quot;: &quot;Miller Ltd&quot;,
                    &quot;slot&quot;: 0,
                    &quot;data_roaming&quot;: false,
                    &quot;signal_strength&quot;: 4,
                    &quot;active&quot;: true
                }
            ]
        },
        {
            &quot;id&quot;: 12,
            &quot;name&quot;: null,
            &quot;model&quot;: &quot;Xiaomi Mi 11&quot;,
            &quot;android_version&quot;: &quot;9&quot;,
            &quot;app_version&quot;: &quot;3.73.98&quot;,
            &quot;battery&quot;: 71,
            &quot;is_charging&quot;: false,
            &quot;enabled&quot;: true,
            &quot;sims&quot;: [
                {
                    &quot;id&quot;: 16,
                    &quot;name&quot;: &quot;Hermiston, Parker and Marvin&quot;,
                    &quot;label&quot;: null,
                    &quot;number&quot;: &quot;+13648507362&quot;,
                    &quot;country&quot;: &quot;GE&quot;,
                    &quot;carrier&quot;: &quot;Littel Ltd&quot;,
                    &quot;slot&quot;: 0,
                    &quot;data_roaming&quot;: false,
                    &quot;signal_strength&quot;: 4,
                    &quot;active&quot;: true
                }
            ]
        }
    ],
    &quot;links&quot;: {
        &quot;first&quot;: &quot;/?page=1&quot;,
        &quot;last&quot;: &quot;/?page=1&quot;,
        &quot;prev&quot;: null,
        &quot;next&quot;: null
    },
    &quot;meta&quot;: {
        &quot;current_page&quot;: 1,
        &quot;from&quot;: 1,
        &quot;last_page&quot;: 1,
        &quot;links&quot;: [
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;&amp;laquo; Previous&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            },
            {
                &quot;url&quot;: &quot;/?page=1&quot;,
                &quot;label&quot;: &quot;1&quot;,
                &quot;page&quot;: 1,
                &quot;active&quot;: true
            },
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;Next &amp;raquo;&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            }
        ],
        &quot;path&quot;: &quot;/&quot;,
        &quot;per_page&quot;: 10,
        &quot;to&quot;: 2,
        &quot;total&quot;: 2
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-campaigns--campaign_id--devices" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-campaigns--campaign_id--devices"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-campaigns--campaign_id--devices"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-campaigns--campaign_id--devices" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-campaigns--campaign_id--devices">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-campaigns--campaign_id--devices" data-method="GET"
      data-path="api/v1/campaigns/{campaign_id}/devices"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-campaigns--campaign_id--devices', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-campaigns--campaign_id--devices"
                    onclick="tryItOut('GETapi-v1-campaigns--campaign_id--devices');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-campaigns--campaign_id--devices"
                    onclick="cancelTryOut('GETapi-v1-campaigns--campaign_id--devices');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-campaigns--campaign_id--devices"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/campaigns/{campaign_id}/devices</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-campaigns--campaign_id--devices"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-campaigns--campaign_id--devices"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-campaigns--campaign_id--devices"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>campaign_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="campaign_id"                data-endpoint="GETapi-v1-campaigns--campaign_id--devices"
               value="1"
               data-component="url">
    <br>
<p>The ID of the campaign. Example: <code>1</code></p>
            </div>
                    </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the device.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The name of the device.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>model</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The model of the device.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>enabled</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether the user has logged in to the app on the device.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>android_version</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The android version of the device.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>app_version</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The app version installed on the device.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>battery</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The percentage of battery remaining on the device.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>is_charging</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether the device is charging or not.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The status of the campaign on the device.</p>
Must be one of:
<ul style="list-style-type: square;"><li><code>Pending</code></li> <li><code>Queued</code></li> <li><code>Stalled</code></li> <li><code>Succeeded</code></li> <li><code>Failed</code></li> <li><code>Cancelling</code></li> <li><code>Cancelled</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>sims</code></b>&nbsp;&nbsp;
<small>object[]</small>&nbsp;
 &nbsp;
<br>
<p>The sims of the device that are used in the campaign.</p>
            </summary>
                                                <div style=" margin-left: 14px; clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>*</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>

            </summary>
                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the sim.</p>
                    </div>
                                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The name of the sim.</p>
                    </div>
                                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>label</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The label of the sim.</p>
                    </div>
                                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The number of the sim.</p>
                    </div>
                                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>country</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The country of the sim.</p>
                    </div>
                                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>carrier</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The carrier of the sim.</p>
                    </div>
                                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>slot</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The slot of the sim.</p>
                    </div>
                                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>active</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether the sim is active or not.</p>
                    </div>
                                    </details>
        </div>
                                        </details>
        </div>
                        <h2 id="campaigns-GETapi-v1-campaigns--campaign_id--sending-servers">List all sending servers used in a campaign</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Returns a list of sending servers used in a campaign. The sending servers are returned with sender ids used in the campaign and status of the campaign on the sending server.</p>

<span id="example-requests-GETapi-v1-campaigns--campaign_id--sending-servers">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "{{ config("app.url") }}/api/v1/campaigns/1/sending-servers" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/campaigns/1/sending-servers"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/campaigns/1/sending-servers';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/campaigns/1/sending-servers'
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-campaigns--campaign_id--sending-servers">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 3,
            &quot;name&quot;: &quot;Gideon Boyer&quot;,
            &quot;enabled&quot;: true,
            &quot;sender_ids&quot;: [
                {
                    &quot;id&quot;: 21,
                    &quot;value&quot;: &quot;942561&quot;
                }
            ]
        },
        {
            &quot;id&quot;: 4,
            &quot;name&quot;: &quot;Bridgette D&#039;Amore&quot;,
            &quot;enabled&quot;: true,
            &quot;sender_ids&quot;: [
                {
                    &quot;id&quot;: 22,
                    &quot;value&quot;: &quot;597151&quot;
                }
            ]
        }
    ],
    &quot;links&quot;: {
        &quot;first&quot;: &quot;/?page=1&quot;,
        &quot;last&quot;: &quot;/?page=1&quot;,
        &quot;prev&quot;: null,
        &quot;next&quot;: null
    },
    &quot;meta&quot;: {
        &quot;current_page&quot;: 1,
        &quot;from&quot;: 1,
        &quot;last_page&quot;: 1,
        &quot;links&quot;: [
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;&amp;laquo; Previous&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            },
            {
                &quot;url&quot;: &quot;/?page=1&quot;,
                &quot;label&quot;: &quot;1&quot;,
                &quot;page&quot;: 1,
                &quot;active&quot;: true
            },
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;Next &amp;raquo;&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            }
        ],
        &quot;path&quot;: &quot;/&quot;,
        &quot;per_page&quot;: 10,
        &quot;to&quot;: 2,
        &quot;total&quot;: 2
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-campaigns--campaign_id--sending-servers" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-campaigns--campaign_id--sending-servers"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-campaigns--campaign_id--sending-servers"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-campaigns--campaign_id--sending-servers" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-campaigns--campaign_id--sending-servers">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-campaigns--campaign_id--sending-servers" data-method="GET"
      data-path="api/v1/campaigns/{campaign_id}/sending-servers"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-campaigns--campaign_id--sending-servers', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-campaigns--campaign_id--sending-servers"
                    onclick="tryItOut('GETapi-v1-campaigns--campaign_id--sending-servers');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-campaigns--campaign_id--sending-servers"
                    onclick="cancelTryOut('GETapi-v1-campaigns--campaign_id--sending-servers');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-campaigns--campaign_id--sending-servers"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/campaigns/{campaign_id}/sending-servers</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-campaigns--campaign_id--sending-servers"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-campaigns--campaign_id--sending-servers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-campaigns--campaign_id--sending-servers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>campaign_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="campaign_id"                data-endpoint="GETapi-v1-campaigns--campaign_id--sending-servers"
               value="1"
               data-component="url">
    <br>
<p>The ID of the campaign. Example: <code>1</code></p>
            </div>
                    </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the sending server.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The name of the sending server.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The status of the campaign on this sending server.</p>
Must be one of:
<ul style="list-style-type: square;"><li><code>Pending</code></li> <li><code>Queued</code></li> <li><code>Stalled</code></li> <li><code>Succeeded</code></li> <li><code>Failed</code></li> <li><code>Cancelling</code></li> <li><code>Cancelled</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>sender_ids</code></b>&nbsp;&nbsp;
<small>object[]</small>&nbsp;
 &nbsp;
<br>
<p>The sender ids of the sending server that are used in the campaign.</p>
            </summary>
                                                <div style=" margin-left: 14px; clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>*</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>

            </summary>
                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the sender id.</p>
                    </div>
                                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>value</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The value of the sender id.</p>
                    </div>
                                    </details>
        </div>
                                        </details>
        </div>
                        <h2 id="campaigns-PUTapi-v1-campaigns--id-">Update a campaign</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Updates the specific campaign by setting the values of the parameters passed.</p>

<span id="example-requests-PUTapi-v1-campaigns--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "{{ config("app.url") }}/api/v1/campaigns/1" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"New Campaign\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/campaigns/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "New Campaign"
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/campaigns/1';
$response = $client-&gt;put(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'name' =&gt; 'New Campaign',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/campaigns/1'
payload = {
    "name": "New Campaign"
}
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('PUT', url, headers=headers, json=payload)
response.json()</code></pre></div>

</span>

<span id="example-responses-PUTapi-v1-campaigns--id-">
            <blockquote>
            <p>Example response (204, No content):</p>
        </blockquote>
                <pre>
<code>Empty response</code>
 </pre>
    </span>
<span id="execution-results-PUTapi-v1-campaigns--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-v1-campaigns--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-v1-campaigns--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-v1-campaigns--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-v1-campaigns--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-v1-campaigns--id-" data-method="PUT"
      data-path="api/v1/campaigns/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-v1-campaigns--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-v1-campaigns--id-"
                    onclick="tryItOut('PUTapi-v1-campaigns--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-v1-campaigns--id-"
                    onclick="cancelTryOut('PUTapi-v1-campaigns--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-v1-campaigns--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/v1/campaigns/{id}</code></b>
        </p>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/v1/campaigns/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="PUTapi-v1-campaigns--id-"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-v1-campaigns--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-v1-campaigns--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="PUTapi-v1-campaigns--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the campaign. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="PUTapi-v1-campaigns--id-"
               value="New Campaign"
               data-component="body">
    <br>
<p>The name of the campaign. Must be at least 3 characters. Example: <code>New Campaign</code></p>
        </div>
        </form>

                    <h2 id="campaigns-DELETEapi-v1-campaigns--id-">Delete a campaign</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Permanently deletes a campaign. Also deletes all messages or USSD pulls associated with it. It cannot be undone.</p>

<span id="example-requests-DELETEapi-v1-campaigns--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "{{ config("app.url") }}/api/v1/campaigns/1" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/campaigns/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/campaigns/1';
$response = $client-&gt;delete(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/campaigns/1'
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('DELETE', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-DELETEapi-v1-campaigns--id-">
            <blockquote>
            <p>Example response (204, No content):</p>
        </blockquote>
                <pre>
<code>Empty response</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-v1-campaigns--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-v1-campaigns--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-v1-campaigns--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-v1-campaigns--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-v1-campaigns--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-v1-campaigns--id-" data-method="DELETE"
      data-path="api/v1/campaigns/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-v1-campaigns--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-v1-campaigns--id-"
                    onclick="tryItOut('DELETEapi-v1-campaigns--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-v1-campaigns--id-"
                    onclick="cancelTryOut('DELETEapi-v1-campaigns--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-v1-campaigns--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/v1/campaigns/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="DELETEapi-v1-campaigns--id-"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-v1-campaigns--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-v1-campaigns--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="DELETEapi-v1-campaigns--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the campaign. Example: <code>1</code></p>
            </div>
                    </form>

                <h1 id="contact-lists">Contact Lists</h1>

    <p>Contact Lists</p>

                                <h2 id="contact-lists-GETapi-v1-contact-lists">List all contact lists</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Returns a list of contact lists. The contact lists are returned sorted by creation date, with the most recently created contact list appearing first.</p>

<span id="example-requests-GETapi-v1-contact-lists">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "{{ config("app.url") }}/api/v1/contact-lists" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/contact-lists"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/contact-lists';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/contact-lists'
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-contact-lists">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 11,
            &quot;name&quot;: &quot;deborah.mayert&quot;
        },
        {
            &quot;id&quot;: 12,
            &quot;name&quot;: &quot;kuhn.jaclyn&quot;
        }
    ],
    &quot;links&quot;: {
        &quot;first&quot;: &quot;/?page=1&quot;,
        &quot;last&quot;: &quot;/?page=1&quot;,
        &quot;prev&quot;: null,
        &quot;next&quot;: null
    },
    &quot;meta&quot;: {
        &quot;current_page&quot;: 1,
        &quot;from&quot;: 1,
        &quot;last_page&quot;: 1,
        &quot;links&quot;: [
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;&amp;laquo; Previous&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            },
            {
                &quot;url&quot;: &quot;/?page=1&quot;,
                &quot;label&quot;: &quot;1&quot;,
                &quot;page&quot;: 1,
                &quot;active&quot;: true
            },
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;Next &amp;raquo;&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            }
        ],
        &quot;path&quot;: &quot;/&quot;,
        &quot;per_page&quot;: 10,
        &quot;to&quot;: 2,
        &quot;total&quot;: 2
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-contact-lists" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-contact-lists"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-contact-lists"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-contact-lists" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-contact-lists">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-contact-lists" data-method="GET"
      data-path="api/v1/contact-lists"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-contact-lists', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-contact-lists"
                    onclick="tryItOut('GETapi-v1-contact-lists');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-contact-lists"
                    onclick="cancelTryOut('GETapi-v1-contact-lists');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-contact-lists"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/contact-lists</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-contact-lists"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-contact-lists"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-contact-lists"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the contact list.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The name of the contact list.</p>
        </div>
                        <h2 id="contact-lists-GETapi-v1-contact-lists--id-">Retrieve a contact list</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retrieves a Contact List object.</p>

<span id="example-requests-GETapi-v1-contact-lists--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "{{ config("app.url") }}/api/v1/contact-lists/1" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/contact-lists/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/contact-lists/1';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/contact-lists/1'
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-contact-lists--id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;id&quot;: 13,
    &quot;name&quot;: &quot;deborah.mayert&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-contact-lists--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-contact-lists--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-contact-lists--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-contact-lists--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-contact-lists--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-contact-lists--id-" data-method="GET"
      data-path="api/v1/contact-lists/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-contact-lists--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-contact-lists--id-"
                    onclick="tryItOut('GETapi-v1-contact-lists--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-contact-lists--id-"
                    onclick="cancelTryOut('GETapi-v1-contact-lists--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-contact-lists--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/contact-lists/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-contact-lists--id-"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-contact-lists--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-contact-lists--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-v1-contact-lists--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the contact list. Example: <code>1</code></p>
            </div>
                    </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the contact list.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The name of the contact list.</p>
        </div>
                        <h2 id="contact-lists-POSTapi-v1-contact-lists">Create a contact list</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTapi-v1-contact-lists">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "{{ config("app.url") }}/api/v1/contact-lists" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"My List\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/contact-lists"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "My List"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/contact-lists';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'name' =&gt; 'My List',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/contact-lists'
payload = {
    "name": "My List"
}
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('POST', url, headers=headers, json=payload)
response.json()</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-contact-lists">
            <blockquote>
            <p>Example response (201, Created):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;id&quot;: 14,
    &quot;name&quot;: &quot;brown.maggie&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-v1-contact-lists" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-contact-lists"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-contact-lists"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-contact-lists" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-contact-lists">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-contact-lists" data-method="POST"
      data-path="api/v1/contact-lists"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-contact-lists', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-contact-lists"
                    onclick="tryItOut('POSTapi-v1-contact-lists');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-contact-lists"
                    onclick="cancelTryOut('POSTapi-v1-contact-lists');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-contact-lists"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/contact-lists</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-v1-contact-lists"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-contact-lists"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-contact-lists"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTapi-v1-contact-lists"
               value="My List"
               data-component="body">
    <br>
<p>The name of the contact list. Must be unique. Must be at least 3 characters. Example: <code>My List</code></p>
        </div>
        </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the contact list.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The name of the contact list.</p>
        </div>
                        <h2 id="contact-lists-PUTapi-v1-contact-lists--id-">Update a contact list</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Updates the specific contact list by setting the values of the parameters passed.</p>

<span id="example-requests-PUTapi-v1-contact-lists--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "{{ config("app.url") }}/api/v1/contact-lists/1" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"My List\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/contact-lists/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "My List"
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/contact-lists/1';
$response = $client-&gt;put(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'name' =&gt; 'My List',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/contact-lists/1'
payload = {
    "name": "My List"
}
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('PUT', url, headers=headers, json=payload)
response.json()</code></pre></div>

</span>

<span id="example-responses-PUTapi-v1-contact-lists--id-">
            <blockquote>
            <p>Example response (204, No content):</p>
        </blockquote>
                <pre>
<code>Empty response</code>
 </pre>
    </span>
<span id="execution-results-PUTapi-v1-contact-lists--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-v1-contact-lists--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-v1-contact-lists--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-v1-contact-lists--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-v1-contact-lists--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-v1-contact-lists--id-" data-method="PUT"
      data-path="api/v1/contact-lists/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-v1-contact-lists--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-v1-contact-lists--id-"
                    onclick="tryItOut('PUTapi-v1-contact-lists--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-v1-contact-lists--id-"
                    onclick="cancelTryOut('PUTapi-v1-contact-lists--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-v1-contact-lists--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/v1/contact-lists/{id}</code></b>
        </p>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/v1/contact-lists/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="PUTapi-v1-contact-lists--id-"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-v1-contact-lists--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-v1-contact-lists--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="PUTapi-v1-contact-lists--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the contact list. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="PUTapi-v1-contact-lists--id-"
               value="My List"
               data-component="body">
    <br>
<p>The name of the contact list. Must be unique. Must be at least 3 characters. Example: <code>My List</code></p>
        </div>
        </form>

                    <h2 id="contact-lists-DELETEapi-v1-contact-lists--id-">Delete a contact list</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Permanently deletes the specific contact list. Also deletes all contacts and fields associated with it. It cannot be undone.</p>

<span id="example-requests-DELETEapi-v1-contact-lists--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "{{ config("app.url") }}/api/v1/contact-lists/1" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/contact-lists/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/contact-lists/1';
$response = $client-&gt;delete(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/contact-lists/1'
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('DELETE', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-DELETEapi-v1-contact-lists--id-">
            <blockquote>
            <p>Example response (204, No content):</p>
        </blockquote>
                <pre>
<code>Empty response</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-v1-contact-lists--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-v1-contact-lists--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-v1-contact-lists--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-v1-contact-lists--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-v1-contact-lists--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-v1-contact-lists--id-" data-method="DELETE"
      data-path="api/v1/contact-lists/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-v1-contact-lists--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-v1-contact-lists--id-"
                    onclick="tryItOut('DELETEapi-v1-contact-lists--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-v1-contact-lists--id-"
                    onclick="cancelTryOut('DELETEapi-v1-contact-lists--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-v1-contact-lists--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/v1/contact-lists/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="DELETEapi-v1-contact-lists--id-"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-v1-contact-lists--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-v1-contact-lists--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="DELETEapi-v1-contact-lists--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the contact list. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="contact-lists-GETapi-v1-contact-lists--contact_list_id--fields">List all fields for a contact list</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Returns a list of fields for a contact list. The fields are returned sorted by creation date, with the most recently created field appearing first.</p>

<span id="example-requests-GETapi-v1-contact-lists--contact_list_id--fields">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "{{ config("app.url") }}/api/v1/contact-lists/1/fields" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/contact-lists/1/fields"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/contact-lists/1/fields';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/contact-lists/1/fields'
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-contact-lists--contact_list_id--fields">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 21,
            &quot;label&quot;: &quot;Itaque&quot;,
            &quot;tag&quot;: &quot;itaque&quot;,
            &quot;type&quot;: &quot;email&quot;,
            &quot;options&quot;: null,
            &quot;default_value&quot;: &quot;non&quot;,
            &quot;required&quot;: true
        },
        {
            &quot;id&quot;: 22,
            &quot;label&quot;: &quot;Sed&quot;,
            &quot;tag&quot;: &quot;sed&quot;,
            &quot;type&quot;: &quot;multiselect&quot;,
            &quot;options&quot;: [
                {
                    &quot;label&quot;: &quot;quasi&quot;,
                    &quot;value&quot;: &quot;commodi&quot;
                },
                {
                    &quot;label&quot;: &quot;tempore&quot;,
                    &quot;value&quot;: &quot;in&quot;
                },
                {
                    &quot;label&quot;: &quot;qui&quot;,
                    &quot;value&quot;: &quot;hic&quot;
                },
                {
                    &quot;label&quot;: &quot;sequi&quot;,
                    &quot;value&quot;: &quot;corrupti&quot;
                },
                {
                    &quot;label&quot;: &quot;et&quot;,
                    &quot;value&quot;: &quot;enim&quot;
                }
            ],
            &quot;default_value&quot;: [
                &quot;in&quot;
            ],
            &quot;required&quot;: true
        }
    ],
    &quot;links&quot;: {
        &quot;first&quot;: &quot;/?page=1&quot;,
        &quot;last&quot;: &quot;/?page=1&quot;,
        &quot;prev&quot;: null,
        &quot;next&quot;: null
    },
    &quot;meta&quot;: {
        &quot;current_page&quot;: 1,
        &quot;from&quot;: 1,
        &quot;last_page&quot;: 1,
        &quot;links&quot;: [
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;&amp;laquo; Previous&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            },
            {
                &quot;url&quot;: &quot;/?page=1&quot;,
                &quot;label&quot;: &quot;1&quot;,
                &quot;page&quot;: 1,
                &quot;active&quot;: true
            },
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;Next &amp;raquo;&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            }
        ],
        &quot;path&quot;: &quot;/&quot;,
        &quot;per_page&quot;: 50,
        &quot;to&quot;: 2,
        &quot;total&quot;: 2
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-contact-lists--contact_list_id--fields" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-contact-lists--contact_list_id--fields"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-contact-lists--contact_list_id--fields"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-contact-lists--contact_list_id--fields" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-contact-lists--contact_list_id--fields">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-contact-lists--contact_list_id--fields" data-method="GET"
      data-path="api/v1/contact-lists/{contact_list_id}/fields"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-contact-lists--contact_list_id--fields', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-contact-lists--contact_list_id--fields"
                    onclick="tryItOut('GETapi-v1-contact-lists--contact_list_id--fields');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-contact-lists--contact_list_id--fields"
                    onclick="cancelTryOut('GETapi-v1-contact-lists--contact_list_id--fields');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-contact-lists--contact_list_id--fields"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/contact-lists/{contact_list_id}/fields</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-contact-lists--contact_list_id--fields"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-contact-lists--contact_list_id--fields"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-contact-lists--contact_list_id--fields"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>contact_list_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="contact_list_id"                data-endpoint="GETapi-v1-contact-lists--contact_list_id--fields"
               value="1"
               data-component="url">
    <br>
<p>The ID of the contact list. Example: <code>1</code></p>
            </div>
                    </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the field.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>label</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The label of the field.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>tag</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The tag of the field.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The type of the field.</p>
Must be one of:
<ul style="list-style-type: square;"><li><code>text</code></li> <li><code>number</code></li> <li><code>email</code></li> <li><code>dropdown</code></li> <li><code>multiselect</code></li> <li><code>checkbox</code></li> <li><code>radio</code></li> <li><code>date</code></li> <li><code>datetime-local</code></li> <li><code>time</code></li> <li><code>textarea</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>options</code></b>&nbsp;&nbsp;
<small>object[]</small>&nbsp;
 &nbsp;
<br>
<p>List of all possible options for the field.</p>
            </summary>
                                                <div style=" margin-left: 14px; clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>*</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>

            </summary>
                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>label</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The label of the option.</p>
                    </div>
                                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>value</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The value of the option.</p>
                    </div>
                                    </details>
        </div>
                                        </details>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>default_value</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The default value of the field.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>required</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether the field is required or not.</p>
        </div>
                        <h2 id="contact-lists-GETapi-v1-fields--id-">Retrieve a field</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retrieves a Field object.</p>

<span id="example-requests-GETapi-v1-fields--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "{{ config("app.url") }}/api/v1/fields/1" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/fields/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/fields/1';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/fields/1'
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-fields--id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;id&quot;: 23,
    &quot;label&quot;: &quot;Non&quot;,
    &quot;tag&quot;: &quot;non&quot;,
    &quot;type&quot;: &quot;email&quot;,
    &quot;options&quot;: null,
    &quot;default_value&quot;: &quot;aspernatur&quot;,
    &quot;required&quot;: true
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-fields--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-fields--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-fields--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-fields--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-fields--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-fields--id-" data-method="GET"
      data-path="api/v1/fields/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-fields--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-fields--id-"
                    onclick="tryItOut('GETapi-v1-fields--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-fields--id-"
                    onclick="cancelTryOut('GETapi-v1-fields--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-fields--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/fields/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-fields--id-"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-fields--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-fields--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-v1-fields--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the field. Example: <code>1</code></p>
            </div>
                    </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the field.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>label</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The label of the field.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>tag</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The tag of the field.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The type of the field.</p>
Must be one of:
<ul style="list-style-type: square;"><li><code>text</code></li> <li><code>number</code></li> <li><code>email</code></li> <li><code>dropdown</code></li> <li><code>multiselect</code></li> <li><code>checkbox</code></li> <li><code>radio</code></li> <li><code>date</code></li> <li><code>datetime-local</code></li> <li><code>time</code></li> <li><code>textarea</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>options</code></b>&nbsp;&nbsp;
<small>object[]</small>&nbsp;
 &nbsp;
<br>
<p>List of all possible options for the field.</p>
            </summary>
                                                <div style=" margin-left: 14px; clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>*</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>

            </summary>
                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>label</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The label of the option.</p>
                    </div>
                                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>value</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The value of the option.</p>
                    </div>
                                    </details>
        </div>
                                        </details>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>default_value</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The default value of the field.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>required</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether the field is required or not.</p>
        </div>
                        <h2 id="contact-lists-POSTapi-v1-contact-lists--contact_list_id--fields">Create a field for a contact list</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTapi-v1-contact-lists--contact_list_id--fields">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "{{ config("app.url") }}/api/v1/contact-lists/1/fields" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"label\": \"First Name\",
    \"tag\": \"first_name\",
    \"type\": \"text\",
    \"default_value\": \"John\",
    \"required\": false,
    \"options\": [
        {
            \"label\": \"Option 1\",
            \"value\": \"option_1\"
        }
    ]
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/contact-lists/1/fields"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "label": "First Name",
    "tag": "first_name",
    "type": "text",
    "default_value": "John",
    "required": false,
    "options": [
        {
            "label": "Option 1",
            "value": "option_1"
        }
    ]
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/contact-lists/1/fields';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'label' =&gt; 'First Name',
            'tag' =&gt; 'first_name',
            'type' =&gt; 'text',
            'default_value' =&gt; 'John',
            'required' =&gt; false,
            'options' =&gt; [
                [
                    'label' =&gt; 'Option 1',
                    'value' =&gt; 'option_1',
                ],
            ],
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/contact-lists/1/fields'
payload = {
    "label": "First Name",
    "tag": "first_name",
    "type": "text",
    "default_value": "John",
    "required": false,
    "options": [
        {
            "label": "Option 1",
            "value": "option_1"
        }
    ]
}
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('POST', url, headers=headers, json=payload)
response.json()</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-contact-lists--contact_list_id--fields">
            <blockquote>
            <p>Example response (201, Created):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;id&quot;: 24,
    &quot;label&quot;: &quot;Est&quot;,
    &quot;tag&quot;: &quot;est&quot;,
    &quot;type&quot;: &quot;dropdown&quot;,
    &quot;options&quot;: [
        {
            &quot;label&quot;: &quot;repellat&quot;,
            &quot;value&quot;: &quot;ut&quot;
        },
        {
            &quot;label&quot;: &quot;est&quot;,
            &quot;value&quot;: &quot;accusantium&quot;
        },
        {
            &quot;label&quot;: &quot;eaque&quot;,
            &quot;value&quot;: &quot;unde&quot;
        },
        {
            &quot;label&quot;: &quot;enim&quot;,
            &quot;value&quot;: &quot;id&quot;
        },
        {
            &quot;label&quot;: &quot;id&quot;,
            &quot;value&quot;: &quot;dolorem&quot;
        }
    ],
    &quot;default_value&quot;: null,
    &quot;required&quot;: false
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-v1-contact-lists--contact_list_id--fields" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-contact-lists--contact_list_id--fields"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-contact-lists--contact_list_id--fields"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-contact-lists--contact_list_id--fields" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-contact-lists--contact_list_id--fields">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-contact-lists--contact_list_id--fields" data-method="POST"
      data-path="api/v1/contact-lists/{contact_list_id}/fields"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-contact-lists--contact_list_id--fields', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-contact-lists--contact_list_id--fields"
                    onclick="tryItOut('POSTapi-v1-contact-lists--contact_list_id--fields');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-contact-lists--contact_list_id--fields"
                    onclick="cancelTryOut('POSTapi-v1-contact-lists--contact_list_id--fields');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-contact-lists--contact_list_id--fields"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/contact-lists/{contact_list_id}/fields</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-v1-contact-lists--contact_list_id--fields"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-contact-lists--contact_list_id--fields"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-contact-lists--contact_list_id--fields"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>contact_list_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="contact_list_id"                data-endpoint="POSTapi-v1-contact-lists--contact_list_id--fields"
               value="1"
               data-component="url">
    <br>
<p>The ID of the contact list. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>label</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="label"                data-endpoint="POSTapi-v1-contact-lists--contact_list_id--fields"
               value="First Name"
               data-component="body">
    <br>
<p>The label of the field. Must be at least 3 characters. Must not be greater than 50 characters. Example: <code>First Name</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>tag</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="tag"                data-endpoint="POSTapi-v1-contact-lists--contact_list_id--fields"
               value="first_name"
               data-component="body">
    <br>
<p>The tag of the field. Must contain only letters, numbers, dashes and underscores. Must not be one of <code>mobile_number</code> or <code>subscribed</code> Must be at least 3 characters. Must not be greater than 50 characters. Example: <code>first_name</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="type"                data-endpoint="POSTapi-v1-contact-lists--contact_list_id--fields"
               value="text"
               data-component="body">
    <br>
<p>The type of the field. Example: <code>text</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>text</code></li> <li><code>number</code></li> <li><code>email</code></li> <li><code>dropdown</code></li> <li><code>multiselect</code></li> <li><code>checkbox</code></li> <li><code>radio</code></li> <li><code>date</code></li> <li><code>datetime-local</code></li> <li><code>time</code></li> <li><code>textarea</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>options</code></b>&nbsp;&nbsp;
<small>object[]</small>&nbsp;
<i>optional</i> &nbsp;
<br>
<p>List of all possible options for the field. This field is required when <code>type</code> is <code>checkbox</code>, <code>dropdown</code>, <code>multiselect</code>, or <code>radio</code>.</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>label</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="options.0.label"                data-endpoint="POSTapi-v1-contact-lists--contact_list_id--fields"
               value="Option 1"
               data-component="body">
    <br>
<p>The label of the option. Must be at least 3 characters. Example: <code>Option 1</code></p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>value</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="options.0.value"                data-endpoint="POSTapi-v1-contact-lists--contact_list_id--fields"
               value="option_1"
               data-component="body">
    <br>
<p>The value of the option. Must contain only letters, numbers, dashes and underscores. Example: <code>option_1</code></p>
                    </div>
                                    </details>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>default_value</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="default_value"                data-endpoint="POSTapi-v1-contact-lists--contact_list_id--fields"
               value="John"
               data-component="body">
    <br>
<p>The default value of the field. Example: <code>John</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>required</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
                <label data-endpoint="POSTapi-v1-contact-lists--contact_list_id--fields" style="display: none">
            <input type="radio" name="required"
                   value="true"
                   data-endpoint="POSTapi-v1-contact-lists--contact_list_id--fields"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="POSTapi-v1-contact-lists--contact_list_id--fields" style="display: none">
            <input type="radio" name="required"
                   value="false"
                   data-endpoint="POSTapi-v1-contact-lists--contact_list_id--fields"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>Whether the field is required or not. Example: <code>false</code></p>
        </div>
        </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the field.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>label</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The label of the field.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>tag</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The tag of the field.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The type of the field.</p>
Must be one of:
<ul style="list-style-type: square;"><li><code>text</code></li> <li><code>number</code></li> <li><code>email</code></li> <li><code>dropdown</code></li> <li><code>multiselect</code></li> <li><code>checkbox</code></li> <li><code>radio</code></li> <li><code>date</code></li> <li><code>datetime-local</code></li> <li><code>time</code></li> <li><code>textarea</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>options</code></b>&nbsp;&nbsp;
<small>object[]</small>&nbsp;
 &nbsp;
<br>
<p>List of all possible options for the field.</p>
            </summary>
                                                <div style=" margin-left: 14px; clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>*</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>

            </summary>
                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>label</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The label of the option.</p>
                    </div>
                                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>value</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The value of the option.</p>
                    </div>
                                    </details>
        </div>
                                        </details>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>default_value</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The default value of the field.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>required</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether the field is required or not.</p>
        </div>
                        <h2 id="contact-lists-PUTapi-v1-fields--id-">Update a field</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Updates the specific field by setting the values of the parameters passed. It is recommended to retrieve the field first to get the field's current state because partial updates are not supported.</p>

<span id="example-requests-PUTapi-v1-fields--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "{{ config("app.url") }}/api/v1/fields/1" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"label\": \"First Name\",
    \"tag\": \"first_name\",
    \"type\": \"text\",
    \"default_value\": \"John\",
    \"required\": false,
    \"options\": [
        {
            \"label\": \"Option 1\",
            \"value\": \"option_1\"
        }
    ]
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/fields/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "label": "First Name",
    "tag": "first_name",
    "type": "text",
    "default_value": "John",
    "required": false,
    "options": [
        {
            "label": "Option 1",
            "value": "option_1"
        }
    ]
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/fields/1';
$response = $client-&gt;put(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'label' =&gt; 'First Name',
            'tag' =&gt; 'first_name',
            'type' =&gt; 'text',
            'default_value' =&gt; 'John',
            'required' =&gt; false,
            'options' =&gt; [
                [
                    'label' =&gt; 'Option 1',
                    'value' =&gt; 'option_1',
                ],
            ],
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/fields/1'
payload = {
    "label": "First Name",
    "tag": "first_name",
    "type": "text",
    "default_value": "John",
    "required": false,
    "options": [
        {
            "label": "Option 1",
            "value": "option_1"
        }
    ]
}
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('PUT', url, headers=headers, json=payload)
response.json()</code></pre></div>

</span>

<span id="example-responses-PUTapi-v1-fields--id-">
            <blockquote>
            <p>Example response (204, No content):</p>
        </blockquote>
                <pre>
<code>Empty response</code>
 </pre>
    </span>
<span id="execution-results-PUTapi-v1-fields--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-v1-fields--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-v1-fields--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-v1-fields--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-v1-fields--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-v1-fields--id-" data-method="PUT"
      data-path="api/v1/fields/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-v1-fields--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-v1-fields--id-"
                    onclick="tryItOut('PUTapi-v1-fields--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-v1-fields--id-"
                    onclick="cancelTryOut('PUTapi-v1-fields--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-v1-fields--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/v1/fields/{id}</code></b>
        </p>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/v1/fields/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="PUTapi-v1-fields--id-"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-v1-fields--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-v1-fields--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="PUTapi-v1-fields--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the field. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>label</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="label"                data-endpoint="PUTapi-v1-fields--id-"
               value="First Name"
               data-component="body">
    <br>
<p>The label of the field. Must be at least 3 characters. Must not be greater than 50 characters. Example: <code>First Name</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>tag</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="tag"                data-endpoint="PUTapi-v1-fields--id-"
               value="first_name"
               data-component="body">
    <br>
<p>The tag of the field. Must contain only letters, numbers, dashes and underscores. Must not be one of <code>mobile_number</code> or <code>subscribed</code> Must be at least 3 characters. Must not be greater than 50 characters. Example: <code>first_name</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="type"                data-endpoint="PUTapi-v1-fields--id-"
               value="text"
               data-component="body">
    <br>
<p>The type of the field. Example: <code>text</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>text</code></li> <li><code>number</code></li> <li><code>email</code></li> <li><code>dropdown</code></li> <li><code>multiselect</code></li> <li><code>checkbox</code></li> <li><code>radio</code></li> <li><code>date</code></li> <li><code>datetime-local</code></li> <li><code>time</code></li> <li><code>textarea</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>options</code></b>&nbsp;&nbsp;
<small>object[]</small>&nbsp;
<i>optional</i> &nbsp;
<br>
<p>List of all possible options for the field. This field is required when <code>type</code> is <code>checkbox</code>, <code>dropdown</code>, <code>multiselect</code>, or <code>radio</code>.</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>label</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="options.0.label"                data-endpoint="PUTapi-v1-fields--id-"
               value="Option 1"
               data-component="body">
    <br>
<p>The label of the option. Must be at least 3 characters. Example: <code>Option 1</code></p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>value</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="options.0.value"                data-endpoint="PUTapi-v1-fields--id-"
               value="option_1"
               data-component="body">
    <br>
<p>The value of the option. Must contain only letters, numbers, dashes and underscores. Example: <code>option_1</code></p>
                    </div>
                                    </details>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>default_value</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="default_value"                data-endpoint="PUTapi-v1-fields--id-"
               value="John"
               data-component="body">
    <br>
<p>The default value of the field. Example: <code>John</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>required</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
                <label data-endpoint="PUTapi-v1-fields--id-" style="display: none">
            <input type="radio" name="required"
                   value="true"
                   data-endpoint="PUTapi-v1-fields--id-"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="PUTapi-v1-fields--id-" style="display: none">
            <input type="radio" name="required"
                   value="false"
                   data-endpoint="PUTapi-v1-fields--id-"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>Whether the field is required or not. Example: <code>false</code></p>
        </div>
        </form>

                    <h2 id="contact-lists-DELETEapi-v1-fields--id-">Delete a field</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Permanently deletes a field. Also deletes all associated field values of contacts. It cannot be undone.</p>

<span id="example-requests-DELETEapi-v1-fields--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "{{ config("app.url") }}/api/v1/fields/1" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/fields/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/fields/1';
$response = $client-&gt;delete(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/fields/1'
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('DELETE', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-DELETEapi-v1-fields--id-">
            <blockquote>
            <p>Example response (204, No content):</p>
        </blockquote>
                <pre>
<code>Empty response</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-v1-fields--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-v1-fields--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-v1-fields--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-v1-fields--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-v1-fields--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-v1-fields--id-" data-method="DELETE"
      data-path="api/v1/fields/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-v1-fields--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-v1-fields--id-"
                    onclick="tryItOut('DELETEapi-v1-fields--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-v1-fields--id-"
                    onclick="cancelTryOut('DELETEapi-v1-fields--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-v1-fields--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/v1/fields/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="DELETEapi-v1-fields--id-"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-v1-fields--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-v1-fields--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="DELETEapi-v1-fields--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the field. Example: <code>1</code></p>
            </div>
                    </form>

                <h1 id="contacts">Contacts</h1>

    <p>Contacts</p>

                                <h2 id="contacts-GETapi-v1-contact-lists--contact_list_id--contacts">List all contacts for a contact list</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Returns a list of contacts for a contact list. The contacts are returned sorted by creation date, with the most recently created contact appearing first.</p>

<span id="example-requests-GETapi-v1-contact-lists--contact_list_id--contacts">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "{{ config("app.url") }}/api/v1/contact-lists/1/contacts" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/contact-lists/1/contacts"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/contact-lists/1/contacts';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/contact-lists/1/contacts'
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-contact-lists--contact_list_id--contacts">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 2001,
            &quot;mobile_number&quot;: &quot;+14329282957&quot;,
            &quot;subscribed&quot;: true,
            &quot;created_at&quot;: &quot;2025-09-08T05:42:25.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2025-09-08T05:42:25.000000Z&quot;
        },
        {
            &quot;id&quot;: 2002,
            &quot;mobile_number&quot;: &quot;+18058165575&quot;,
            &quot;subscribed&quot;: false,
            &quot;created_at&quot;: &quot;2025-09-08T05:42:25.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2025-09-08T05:42:25.000000Z&quot;
        }
    ],
    &quot;links&quot;: {
        &quot;first&quot;: &quot;/?page=1&quot;,
        &quot;last&quot;: &quot;/?page=1&quot;,
        &quot;prev&quot;: null,
        &quot;next&quot;: null
    },
    &quot;meta&quot;: {
        &quot;current_page&quot;: 1,
        &quot;from&quot;: 1,
        &quot;last_page&quot;: 1,
        &quot;links&quot;: [
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;&amp;laquo; Previous&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            },
            {
                &quot;url&quot;: &quot;/?page=1&quot;,
                &quot;label&quot;: &quot;1&quot;,
                &quot;page&quot;: 1,
                &quot;active&quot;: true
            },
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;Next &amp;raquo;&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            }
        ],
        &quot;path&quot;: &quot;/&quot;,
        &quot;per_page&quot;: 100,
        &quot;to&quot;: 2,
        &quot;total&quot;: 2
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-contact-lists--contact_list_id--contacts" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-contact-lists--contact_list_id--contacts"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-contact-lists--contact_list_id--contacts"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-contact-lists--contact_list_id--contacts" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-contact-lists--contact_list_id--contacts">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-contact-lists--contact_list_id--contacts" data-method="GET"
      data-path="api/v1/contact-lists/{contact_list_id}/contacts"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-contact-lists--contact_list_id--contacts', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-contact-lists--contact_list_id--contacts"
                    onclick="tryItOut('GETapi-v1-contact-lists--contact_list_id--contacts');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-contact-lists--contact_list_id--contacts"
                    onclick="cancelTryOut('GETapi-v1-contact-lists--contact_list_id--contacts');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-contact-lists--contact_list_id--contacts"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/contact-lists/{contact_list_id}/contacts</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-contact-lists--contact_list_id--contacts"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-contact-lists--contact_list_id--contacts"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-contact-lists--contact_list_id--contacts"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>contact_list_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="contact_list_id"                data-endpoint="GETapi-v1-contact-lists--contact_list_id--contacts"
               value="1"
               data-component="url">
    <br>
<p>The ID of the contact list. Example: <code>1</code></p>
            </div>
                    </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the contact.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>mobile_number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The mobile number of the contact.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>first_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The first name of the contact. This is a custom field, so if you didn't set it up, then it won't be available.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>last_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The last name of the contact.  This is a custom field, so if you didn't set it up, then it won't be available.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>subscribed</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether the contact is subscribed or not.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>created_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time when the contact was created.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>updated_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time when the contact was last updated.</p>
        </div>
                        <h2 id="contacts-GETapi-v1-contacts--id-">Retrieve a contact</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retrieves a Contact object.</p>

<span id="example-requests-GETapi-v1-contacts--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "{{ config("app.url") }}/api/v1/contacts/1" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/contacts/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/contacts/1';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/contacts/1'
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-contacts--id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;id&quot;: 2003,
    &quot;mobile_number&quot;: &quot;+19475350806&quot;,
    &quot;subscribed&quot;: true,
    &quot;created_at&quot;: &quot;2025-09-08T05:42:25.000000Z&quot;,
    &quot;updated_at&quot;: &quot;2025-09-08T05:42:25.000000Z&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-contacts--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-contacts--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-contacts--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-contacts--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-contacts--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-contacts--id-" data-method="GET"
      data-path="api/v1/contacts/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-contacts--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-contacts--id-"
                    onclick="tryItOut('GETapi-v1-contacts--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-contacts--id-"
                    onclick="cancelTryOut('GETapi-v1-contacts--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-contacts--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/contacts/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-contacts--id-"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-contacts--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-contacts--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-v1-contacts--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the contact. Example: <code>1</code></p>
            </div>
                    </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the contact.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>mobile_number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The mobile number of the contact.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>first_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The first name of the contact. This is a custom field, so if you didn't set it up, then it won't be available.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>last_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The last name of the contact.  This is a custom field, so if you didn't set it up, then it won't be available.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>subscribed</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether the contact is subscribed or not.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>created_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time when the contact was created.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>updated_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time when the contact was last updated.</p>
        </div>
                        <h2 id="contacts-POSTapi-v1-contact-lists--contact_list_id--contacts">Create a contact in a contact list</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTapi-v1-contact-lists--contact_list_id--contacts">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "{{ config("app.url") }}/api/v1/contact-lists/1/contacts" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"mobile_number\": \"+12025886500\",
    \"subscribed\": false,
    \"first_name\": \"John\",
    \"last_name\": \"Doe\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/contact-lists/1/contacts"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "mobile_number": "+12025886500",
    "subscribed": false,
    "first_name": "John",
    "last_name": "Doe"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/contact-lists/1/contacts';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'mobile_number' =&gt; '+12025886500',
            'subscribed' =&gt; false,
            'first_name' =&gt; 'John',
            'last_name' =&gt; 'Doe',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/contact-lists/1/contacts'
payload = {
    "mobile_number": "+12025886500",
    "subscribed": false,
    "first_name": "John",
    "last_name": "Doe"
}
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('POST', url, headers=headers, json=payload)
response.json()</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-contact-lists--contact_list_id--contacts">
            <blockquote>
            <p>Example response (201, Created):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;id&quot;: 2004,
    &quot;mobile_number&quot;: &quot;+19475350806&quot;,
    &quot;subscribed&quot;: true,
    &quot;created_at&quot;: &quot;2025-09-08T05:42:25.000000Z&quot;,
    &quot;updated_at&quot;: &quot;2025-09-08T05:42:25.000000Z&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-v1-contact-lists--contact_list_id--contacts" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-contact-lists--contact_list_id--contacts"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-contact-lists--contact_list_id--contacts"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-contact-lists--contact_list_id--contacts" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-contact-lists--contact_list_id--contacts">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-contact-lists--contact_list_id--contacts" data-method="POST"
      data-path="api/v1/contact-lists/{contact_list_id}/contacts"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-contact-lists--contact_list_id--contacts', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-contact-lists--contact_list_id--contacts"
                    onclick="tryItOut('POSTapi-v1-contact-lists--contact_list_id--contacts');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-contact-lists--contact_list_id--contacts"
                    onclick="cancelTryOut('POSTapi-v1-contact-lists--contact_list_id--contacts');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-contact-lists--contact_list_id--contacts"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/contact-lists/{contact_list_id}/contacts</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-v1-contact-lists--contact_list_id--contacts"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-contact-lists--contact_list_id--contacts"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-contact-lists--contact_list_id--contacts"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>contact_list_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="contact_list_id"                data-endpoint="POSTapi-v1-contact-lists--contact_list_id--contacts"
               value="1"
               data-component="url">
    <br>
<p>The ID of the contact list. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>mobile_number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="mobile_number"                data-endpoint="POSTapi-v1-contact-lists--contact_list_id--contacts"
               value="+12025886500"
               data-component="body">
    <br>
<p>The mobile number of the contact. Example: <code>+12025886500</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>subscribed</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
                <label data-endpoint="POSTapi-v1-contact-lists--contact_list_id--contacts" style="display: none">
            <input type="radio" name="subscribed"
                   value="true"
                   data-endpoint="POSTapi-v1-contact-lists--contact_list_id--contacts"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="POSTapi-v1-contact-lists--contact_list_id--contacts" style="display: none">
            <input type="radio" name="subscribed"
                   value="false"
                   data-endpoint="POSTapi-v1-contact-lists--contact_list_id--contacts"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>Whether the contact is subscribed to the contact list. Example: <code>false</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>first_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="first_name"                data-endpoint="POSTapi-v1-contact-lists--contact_list_id--contacts"
               value="John"
               data-component="body">
    <br>
<p>The first name of the contact. This is a custom field so if you didn't set it up, it won't be available. Example: <code>John</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>last_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="last_name"                data-endpoint="POSTapi-v1-contact-lists--contact_list_id--contacts"
               value="Doe"
               data-component="body">
    <br>
<p>The last name of the contact. This is a custom field so if you didn't set it up, it won't be available. Example: <code>Doe</code></p>
        </div>
        </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the contact.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>mobile_number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The mobile number of the contact.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>first_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The first name of the contact. This is a custom field, so if you didn't set it up, then it won't be available.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>last_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The last name of the contact.  This is a custom field, so if you didn't set it up, then it won't be available.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>subscribed</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether the contact is subscribed or not.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>created_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time when the contact was created.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>updated_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time when the contact was last updated.</p>
        </div>
                        <h2 id="contacts-PUTapi-v1-contacts--id-">Update a contact</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Update a contact in a contact list with the given data. It is recommended to retrieve the contact first to get the contact's current state because partial updates are not supported.</p>

<span id="example-requests-PUTapi-v1-contacts--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "{{ config("app.url") }}/api/v1/contacts/1" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"mobile_number\": \"+12025886500\",
    \"subscribed\": false,
    \"first_name\": \"John\",
    \"last_name\": \"Doe\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/contacts/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "mobile_number": "+12025886500",
    "subscribed": false,
    "first_name": "John",
    "last_name": "Doe"
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/contacts/1';
$response = $client-&gt;put(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'mobile_number' =&gt; '+12025886500',
            'subscribed' =&gt; false,
            'first_name' =&gt; 'John',
            'last_name' =&gt; 'Doe',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/contacts/1'
payload = {
    "mobile_number": "+12025886500",
    "subscribed": false,
    "first_name": "John",
    "last_name": "Doe"
}
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('PUT', url, headers=headers, json=payload)
response.json()</code></pre></div>

</span>

<span id="example-responses-PUTapi-v1-contacts--id-">
            <blockquote>
            <p>Example response (204, No content):</p>
        </blockquote>
                <pre>
<code>Empty response</code>
 </pre>
    </span>
<span id="execution-results-PUTapi-v1-contacts--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-v1-contacts--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-v1-contacts--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-v1-contacts--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-v1-contacts--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-v1-contacts--id-" data-method="PUT"
      data-path="api/v1/contacts/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-v1-contacts--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-v1-contacts--id-"
                    onclick="tryItOut('PUTapi-v1-contacts--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-v1-contacts--id-"
                    onclick="cancelTryOut('PUTapi-v1-contacts--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-v1-contacts--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/v1/contacts/{id}</code></b>
        </p>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/v1/contacts/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="PUTapi-v1-contacts--id-"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-v1-contacts--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-v1-contacts--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="PUTapi-v1-contacts--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the contact. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>mobile_number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="mobile_number"                data-endpoint="PUTapi-v1-contacts--id-"
               value="+12025886500"
               data-component="body">
    <br>
<p>The mobile number of the contact. Example: <code>+12025886500</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>subscribed</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
                <label data-endpoint="PUTapi-v1-contacts--id-" style="display: none">
            <input type="radio" name="subscribed"
                   value="true"
                   data-endpoint="PUTapi-v1-contacts--id-"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="PUTapi-v1-contacts--id-" style="display: none">
            <input type="radio" name="subscribed"
                   value="false"
                   data-endpoint="PUTapi-v1-contacts--id-"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>Whether the contact is subscribed to the contact list. Example: <code>false</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>first_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="first_name"                data-endpoint="PUTapi-v1-contacts--id-"
               value="John"
               data-component="body">
    <br>
<p>The first name of the contact. This is a custom field so if you didn't set it up, it won't be available. Example: <code>John</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>last_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="last_name"                data-endpoint="PUTapi-v1-contacts--id-"
               value="Doe"
               data-component="body">
    <br>
<p>The last name of the contact. This is a custom field so if you didn't set it up, it won't be available. Example: <code>Doe</code></p>
        </div>
        </form>

                    <h2 id="contacts-DELETEapi-v1-contacts--id-">Delete a contact</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Permanently deletes a contact. It cannot be undone.</p>

<span id="example-requests-DELETEapi-v1-contacts--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "{{ config("app.url") }}/api/v1/contacts/1" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/contacts/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/contacts/1';
$response = $client-&gt;delete(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/contacts/1'
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('DELETE', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-DELETEapi-v1-contacts--id-">
            <blockquote>
            <p>Example response (204, No content):</p>
        </blockquote>
                <pre>
<code>Empty response</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-v1-contacts--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-v1-contacts--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-v1-contacts--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-v1-contacts--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-v1-contacts--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-v1-contacts--id-" data-method="DELETE"
      data-path="api/v1/contacts/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-v1-contacts--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-v1-contacts--id-"
                    onclick="tryItOut('DELETEapi-v1-contacts--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-v1-contacts--id-"
                    onclick="cancelTryOut('DELETEapi-v1-contacts--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-v1-contacts--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/v1/contacts/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="DELETEapi-v1-contacts--id-"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-v1-contacts--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-v1-contacts--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="DELETEapi-v1-contacts--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the contact. Example: <code>1</code></p>
            </div>
                    </form>

                <h1 id="devices">Devices</h1>

    <p>Devices</p>

                                <h2 id="devices-GETapi-v1-devices">List all devices</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Returns a list of devices. The devices are returned sorted by creation date, with the most recently created device appearing first.</p>

<span id="example-requests-GETapi-v1-devices">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "{{ config("app.url") }}/api/v1/devices" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/devices"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/devices';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/devices'
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-devices">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 13,
            &quot;name&quot;: null,
            &quot;model&quot;: &quot;Nokia X20&quot;,
            &quot;enabled&quot;: true,
            &quot;android_version&quot;: &quot;7&quot;,
            &quot;app_version&quot;: &quot;0.20.34&quot;,
            &quot;battery&quot;: 46,
            &quot;is_charging&quot;: false,
            &quot;updated_at&quot;: &quot;2025-09-08T05:42:25.000000Z&quot;,
            &quot;sims&quot;: [
                {
                    &quot;id&quot;: 17,
                    &quot;name&quot;: &quot;Nitzsche, Rogahn and Jakubowski&quot;,
                    &quot;label&quot;: null,
                    &quot;number&quot;: &quot;+16284319281&quot;,
                    &quot;country&quot;: &quot;IL&quot;,
                    &quot;carrier&quot;: &quot;Gerhold, Walter and Schinner&quot;,
                    &quot;slot&quot;: 0,
                    &quot;data_roaming&quot;: false,
                    &quot;signal_strength&quot;: 4,
                    &quot;active&quot;: true
                }
            ]
        },
        {
            &quot;id&quot;: 14,
            &quot;name&quot;: null,
            &quot;model&quot;: &quot;OnePlus Nord 2 5G&quot;,
            &quot;enabled&quot;: true,
            &quot;android_version&quot;: &quot;11&quot;,
            &quot;app_version&quot;: &quot;3.40.82&quot;,
            &quot;battery&quot;: 0,
            &quot;is_charging&quot;: true,
            &quot;updated_at&quot;: &quot;2025-09-08T05:42:25.000000Z&quot;,
            &quot;sims&quot;: [
                {
                    &quot;id&quot;: 18,
                    &quot;name&quot;: &quot;Langosh, Waters and Wolf&quot;,
                    &quot;label&quot;: null,
                    &quot;number&quot;: &quot;+13108318149&quot;,
                    &quot;country&quot;: &quot;BW&quot;,
                    &quot;carrier&quot;: &quot;Boehm LLC&quot;,
                    &quot;slot&quot;: 0,
                    &quot;data_roaming&quot;: true,
                    &quot;signal_strength&quot;: 2,
                    &quot;active&quot;: true
                }
            ]
        }
    ],
    &quot;links&quot;: {
        &quot;first&quot;: &quot;/?page=1&quot;,
        &quot;last&quot;: &quot;/?page=1&quot;,
        &quot;prev&quot;: null,
        &quot;next&quot;: null
    },
    &quot;meta&quot;: {
        &quot;current_page&quot;: 1,
        &quot;from&quot;: 1,
        &quot;last_page&quot;: 1,
        &quot;links&quot;: [
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;&amp;laquo; Previous&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            },
            {
                &quot;url&quot;: &quot;/?page=1&quot;,
                &quot;label&quot;: &quot;1&quot;,
                &quot;page&quot;: 1,
                &quot;active&quot;: true
            },
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;Next &amp;raquo;&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            }
        ],
        &quot;path&quot;: &quot;/&quot;,
        &quot;per_page&quot;: 100,
        &quot;to&quot;: 2,
        &quot;total&quot;: 2
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-devices" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-devices"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-devices"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-devices" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-devices">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-devices" data-method="GET"
      data-path="api/v1/devices"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-devices', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-devices"
                    onclick="tryItOut('GETapi-v1-devices');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-devices"
                    onclick="cancelTryOut('GETapi-v1-devices');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-devices"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/devices</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-devices"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-devices"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-devices"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the device.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The name of the device.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>model</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The model of the device.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>enabled</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether the user has logged in to the app on the device.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>android_version</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The android version of the device.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>app_version</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The app version installed on the device.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>battery</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The percentage of battery remaining on the device.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>is_charging</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether the device is charging or not.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>updated_at</code></b>&nbsp;&nbsp;
<small>datetime</small>&nbsp;
 &nbsp;
<br>
<p>The last time the device was updated.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>sims</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>
<p>List of all sims on the device.</p>
            </summary>
                                                <div style=" margin-left: 14px; clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>*</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>

            </summary>
                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the sim.</p>
                    </div>
                                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The name of the sim.</p>
                    </div>
                                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>label</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The label of the sim.</p>
                    </div>
                                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The number of the sim.</p>
                    </div>
                                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>country</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The country of the sim.</p>
                    </div>
                                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>carrier</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The carrier of the sim.</p>
                    </div>
                                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>slot</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The slot of the sim.</p>
                    </div>
                                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>active</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether the sim is active or not.</p>
                    </div>
                                    </details>
        </div>
                                        </details>
        </div>
                        <h2 id="devices-GETapi-v1-devices--id-">Retrieve a device</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retrieves a Device object.</p>

<span id="example-requests-GETapi-v1-devices--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "{{ config("app.url") }}/api/v1/devices/1" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/devices/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/devices/1';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/devices/1'
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-devices--id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;id&quot;: 15,
    &quot;name&quot;: null,
    &quot;model&quot;: &quot;Nokia X20&quot;,
    &quot;enabled&quot;: true,
    &quot;android_version&quot;: &quot;7&quot;,
    &quot;app_version&quot;: &quot;0.20.34&quot;,
    &quot;battery&quot;: 46,
    &quot;is_charging&quot;: false,
    &quot;updated_at&quot;: &quot;2025-09-08T05:42:25.000000Z&quot;,
    &quot;sims&quot;: [
        {
            &quot;id&quot;: 19,
            &quot;name&quot;: &quot;Carter, Zieme and Oberbrunner&quot;,
            &quot;label&quot;: null,
            &quot;number&quot;: &quot;+16819707512&quot;,
            &quot;country&quot;: &quot;KM&quot;,
            &quot;carrier&quot;: &quot;Kassulke-Ankunding&quot;,
            &quot;slot&quot;: 0,
            &quot;data_roaming&quot;: true,
            &quot;signal_strength&quot;: 3,
            &quot;active&quot;: true
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-devices--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-devices--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-devices--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-devices--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-devices--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-devices--id-" data-method="GET"
      data-path="api/v1/devices/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-devices--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-devices--id-"
                    onclick="tryItOut('GETapi-v1-devices--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-devices--id-"
                    onclick="cancelTryOut('GETapi-v1-devices--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-devices--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/devices/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-devices--id-"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-devices--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-devices--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-v1-devices--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the device. Example: <code>1</code></p>
            </div>
                    </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the device.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The name of the device.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>model</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The model of the device.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>enabled</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether the user has logged in to the app on the device.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>android_version</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The android version of the device.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>app_version</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The app version installed on the device.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>battery</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The percentage of battery remaining on the device.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>is_charging</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether the device is charging or not.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>updated_at</code></b>&nbsp;&nbsp;
<small>datetime</small>&nbsp;
 &nbsp;
<br>
<p>The last time the device was updated.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>sims</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>
<p>List of all sims on the device.</p>
            </summary>
                                                <div style=" margin-left: 14px; clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>*</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>

            </summary>
                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the sim.</p>
                    </div>
                                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The name of the sim.</p>
                    </div>
                                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>label</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The label of the sim.</p>
                    </div>
                                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The number of the sim.</p>
                    </div>
                                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>country</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The country of the sim.</p>
                    </div>
                                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>carrier</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The carrier of the sim.</p>
                    </div>
                                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>slot</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The slot of the sim.</p>
                    </div>
                                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>active</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether the sim is active or not.</p>
                    </div>
                                    </details>
        </div>
                                        </details>
        </div>
                        <h2 id="devices-PUTapi-v1-devices--id-">Update a device</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Update the specific device by setting the values of the parameters passed.</p>

<span id="example-requests-PUTapi-v1-devices--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "{{ config("app.url") }}/api/v1/devices/1" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"My Device\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/devices/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "My Device"
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/devices/1';
$response = $client-&gt;put(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'name' =&gt; 'My Device',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/devices/1'
payload = {
    "name": "My Device"
}
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('PUT', url, headers=headers, json=payload)
response.json()</code></pre></div>

</span>

<span id="example-responses-PUTapi-v1-devices--id-">
            <blockquote>
            <p>Example response (204, No content):</p>
        </blockquote>
                <pre>
<code>Empty response</code>
 </pre>
    </span>
<span id="execution-results-PUTapi-v1-devices--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-v1-devices--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-v1-devices--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-v1-devices--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-v1-devices--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-v1-devices--id-" data-method="PUT"
      data-path="api/v1/devices/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-v1-devices--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-v1-devices--id-"
                    onclick="tryItOut('PUTapi-v1-devices--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-v1-devices--id-"
                    onclick="cancelTryOut('PUTapi-v1-devices--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-v1-devices--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/v1/devices/{id}</code></b>
        </p>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/v1/devices/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="PUTapi-v1-devices--id-"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-v1-devices--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-v1-devices--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="PUTapi-v1-devices--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the device. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="PUTapi-v1-devices--id-"
               value="My Device"
               data-component="body">
    <br>
<p>The name of the device. Must be at least 3 characters. Must not be greater than 255 characters. Example: <code>My Device</code></p>
        </div>
        </form>

                    <h2 id="devices-DELETEapi-v1-devices--id-">Delete a device</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Permanently deletes a device. Also removes the SIMs associated with the device and any messages, USSD Pulls and call logs associated with those SIMs. It cannot be undone.</p>

<span id="example-requests-DELETEapi-v1-devices--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "{{ config("app.url") }}/api/v1/devices/1" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/devices/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/devices/1';
$response = $client-&gt;delete(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/devices/1'
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('DELETE', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-DELETEapi-v1-devices--id-">
            <blockquote>
            <p>Example response (204, No content):</p>
        </blockquote>
                <pre>
<code>Empty response</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-v1-devices--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-v1-devices--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-v1-devices--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-v1-devices--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-v1-devices--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-v1-devices--id-" data-method="DELETE"
      data-path="api/v1/devices/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-v1-devices--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-v1-devices--id-"
                    onclick="tryItOut('DELETEapi-v1-devices--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-v1-devices--id-"
                    onclick="cancelTryOut('DELETEapi-v1-devices--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-v1-devices--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/v1/devices/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="DELETEapi-v1-devices--id-"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-v1-devices--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-v1-devices--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="DELETEapi-v1-devices--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the device. Example: <code>1</code></p>
            </div>
                    </form>

                <h1 id="messages">Messages</h1>

    <p>Messages</p>

                                <h2 id="messages-GETapi-v1-messages-send">Send messages</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Send an SMS or MMS campaign.</p>

<span id="example-requests-GETapi-v1-messages-send">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "{{ config("app.url") }}/api/v1/messages/send" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"sims\": [
        1
    ],
    \"sender_ids\": [
        1
    ],
    \"random_sender\": false,
    \"contact_lists\": [
        1
    ],
    \"mobile_numbers\": [
        \"+12345678901\"
    ],
    \"type\": \"SMS\",
    \"message\": \"Hello, World!\",
    \"attachments\": [
        \"https:\\/\\/example.com\\/image.jpg\"
    ],
    \"delivery_report\": false,
    \"delay\": 60,
    \"prioritize\": false,
    \"name\": \"My Campaign\",
    \"scheduled_at\": \"2021-01-01T12:00:00Z\",
    \"recurring\": false,
    \"frequency\": 1,
    \"frequency_unit\": \"day\",
    \"ends_at\": \"2021-01-01T12:00:00Z\",
    \"timezone\": \"UTC\",
    \"days_of_week\": [
        1
    ],
    \"active_hours\": {
        \"start\": \"08:00\",
        \"end\": \"17:00\"
    }
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/messages/send"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "sims": [
        1
    ],
    "sender_ids": [
        1
    ],
    "random_sender": false,
    "contact_lists": [
        1
    ],
    "mobile_numbers": [
        "+12345678901"
    ],
    "type": "SMS",
    "message": "Hello, World!",
    "attachments": [
        "https:\/\/example.com\/image.jpg"
    ],
    "delivery_report": false,
    "delay": 60,
    "prioritize": false,
    "name": "My Campaign",
    "scheduled_at": "2021-01-01T12:00:00Z",
    "recurring": false,
    "frequency": 1,
    "frequency_unit": "day",
    "ends_at": "2021-01-01T12:00:00Z",
    "timezone": "UTC",
    "days_of_week": [
        1
    ],
    "active_hours": {
        "start": "08:00",
        "end": "17:00"
    }
};

fetch(url, {
    method: "GET",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/messages/send';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'sims' =&gt; [
                1,
            ],
            'sender_ids' =&gt; [
                1,
            ],
            'random_sender' =&gt; false,
            'contact_lists' =&gt; [
                1,
            ],
            'mobile_numbers' =&gt; [
                '+12345678901',
            ],
            'type' =&gt; 'SMS',
            'message' =&gt; 'Hello, World!',
            'attachments' =&gt; [
                'https://example.com/image.jpg',
            ],
            'delivery_report' =&gt; false,
            'delay' =&gt; 60,
            'prioritize' =&gt; false,
            'name' =&gt; 'My Campaign',
            'scheduled_at' =&gt; '2021-01-01T12:00:00Z',
            'recurring' =&gt; false,
            'frequency' =&gt; 1,
            'frequency_unit' =&gt; 'day',
            'ends_at' =&gt; '2021-01-01T12:00:00Z',
            'timezone' =&gt; 'UTC',
            'days_of_week' =&gt; [
                1,
            ],
            'active_hours' =&gt; [
                'start' =&gt; '08:00',
                'end' =&gt; '17:00',
            ],
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/messages/send'
payload = {
    "sims": [
        1
    ],
    "sender_ids": [
        1
    ],
    "random_sender": false,
    "contact_lists": [
        1
    ],
    "mobile_numbers": [
        "+12345678901"
    ],
    "type": "SMS",
    "message": "Hello, World!",
    "attachments": [
        "https:\/\/example.com\/image.jpg"
    ],
    "delivery_report": false,
    "delay": 60,
    "prioritize": false,
    "name": "My Campaign",
    "scheduled_at": "2021-01-01T12:00:00Z",
    "recurring": false,
    "frequency": 1,
    "frequency_unit": "day",
    "ends_at": "2021-01-01T12:00:00Z",
    "timezone": "UTC",
    "days_of_week": [
        1
    ],
    "active_hours": {
        "start": "08:00",
        "end": "17:00"
    }
}
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers, json=payload)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-messages-send">
            <blockquote>
            <p>Example response (201, Created):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;id&quot;: 10,
    &quot;name&quot;: &quot;Blaise Pouros&quot;,
    &quot;scheduled_at&quot;: &quot;2025-12-16T20:37:14.000000Z&quot;,
    &quot;timezone&quot;: &quot;America/Indiana/Knox&quot;,
    &quot;recurring&quot;: true,
    &quot;frequency&quot;: 5,
    &quot;frequency_unit&quot;: &quot;Minute&quot;,
    &quot;ends_at&quot;: &quot;2026-09-05T09:37:44.000000Z&quot;,
    &quot;repeat_at&quot;: null,
    &quot;active_hours&quot;: &quot;09:00-17:00&quot;,
    &quot;days_of_week&quot;: [
        1,
        3,
        5,
        7
    ],
    &quot;status&quot;: &quot;Processed&quot;,
    &quot;type&quot;: &quot;MMS&quot;,
    &quot;options&quot;: {
        &quot;delay&quot;: &quot;34&quot;,
        &quot;prioritize&quot;: true
    },
    &quot;created_at&quot;: &quot;2025-09-08T05:42:25.000000Z&quot;,
    &quot;updated_at&quot;: &quot;2025-09-08T05:42:25.000000Z&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-messages-send" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-messages-send"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-messages-send"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-messages-send" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-messages-send">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-messages-send" data-method="GET"
      data-path="api/v1/messages/send"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-messages-send', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-messages-send"
                    onclick="tryItOut('GETapi-v1-messages-send');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-messages-send"
                    onclick="cancelTryOut('GETapi-v1-messages-send');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-messages-send"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/messages/send</code></b>
        </p>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/messages/send</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-messages-send"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-messages-send"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-messages-send"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>sims</code></b>&nbsp;&nbsp;
<small>integer[]</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="sims[0]"                data-endpoint="GETapi-v1-messages-send"
               data-component="body">
        <input type="number" style="display: none"
               name="sims[1]"                data-endpoint="GETapi-v1-messages-send"
               data-component="body">
    <br>
<p>The ids of the sims to use for sending the messages. You can also set the first element to * and it will use all available sims. This field is required when sender_ids is not present.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>sender_ids</code></b>&nbsp;&nbsp;
<small>integer[]</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="sender_ids[0]"                data-endpoint="GETapi-v1-messages-send"
               data-component="body">
        <input type="number" style="display: none"
               name="sender_ids[1]"                data-endpoint="GETapi-v1-messages-send"
               data-component="body">
    <br>
<p>The ids of the sender ids to use for sending the messages. You can also set the first element to * and it will use all available sender ids. This field is required when sims is not present.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>random_sender</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
<i>optional</i> &nbsp;
                <label data-endpoint="GETapi-v1-messages-send" style="display: none">
            <input type="radio" name="random_sender"
                   value="true"
                   data-endpoint="GETapi-v1-messages-send"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="GETapi-v1-messages-send" style="display: none">
            <input type="radio" name="random_sender"
                   value="false"
                   data-endpoint="GETapi-v1-messages-send"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>Whether to use a random sim or sender id from selected sims or sender_ids for entire campaign. Example: <code>false</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>contact_lists</code></b>&nbsp;&nbsp;
<small>integer[]</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="contact_lists[0]"                data-endpoint="GETapi-v1-messages-send"
               data-component="body">
        <input type="number" style="display: none"
               name="contact_lists[1]"                data-endpoint="GETapi-v1-messages-send"
               data-component="body">
    <br>
<p>The ids of the contact lists to send the messages. This field is required when mobile_numbers is not present. This field is prohibited when mobile_numbers is present.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>mobile_numbers</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="mobile_numbers[0]"                data-endpoint="GETapi-v1-messages-send"
               data-component="body">
        <input type="text" style="display: none"
               name="mobile_numbers[1]"                data-endpoint="GETapi-v1-messages-send"
               data-component="body">
    <br>
<p>The mobile numbers to send the messages. This field is required when contact_lists is not present. This field is prohibited when contact_lists is present.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="type"                data-endpoint="GETapi-v1-messages-send"
               value="SMS"
               data-component="body">
    <br>
<p>The type of the message. Example: <code>SMS</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>SMS</code></li> <li><code>MMS</code></li> <li><code>WhatsApp</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>message</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="message"                data-endpoint="GETapi-v1-messages-send"
               value="Hello, World!"
               data-component="body">
    <br>
<p>The message to send. This field is required when <code>type</code> is <code>SMS</code>. This field is required when <code>attachments</code> is not present. Must not be greater than 1600 characters. Example: <code>Hello, World!</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>attachments</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="attachments[0]"                data-endpoint="GETapi-v1-messages-send"
               data-component="body">
        <input type="text" style="display: none"
               name="attachments[1]"                data-endpoint="GETapi-v1-messages-send"
               data-component="body">
    <br>
<p>The array of file URLs you want to send as attachments with the message. This field is prohibited when type is not MMS.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>delivery_report</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
<i>optional</i> &nbsp;
                <label data-endpoint="GETapi-v1-messages-send" style="display: none">
            <input type="radio" name="delivery_report"
                   value="true"
                   data-endpoint="GETapi-v1-messages-send"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="GETapi-v1-messages-send" style="display: none">
            <input type="radio" name="delivery_report"
                   value="false"
                   data-endpoint="GETapi-v1-messages-send"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>Whether to request a delivery report for the message. This field is prohibited when type is not SMS. Example: <code>false</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>delay</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="delay"                data-endpoint="GETapi-v1-messages-send"
               value="60"
               data-component="body">
    <br>
<p>The delay between messages in seconds. This field is prohibited when sims is not present. Example: <code>60</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>prioritize</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
<i>optional</i> &nbsp;
                <label data-endpoint="GETapi-v1-messages-send" style="display: none">
            <input type="radio" name="prioritize"
                   value="true"
                   data-endpoint="GETapi-v1-messages-send"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="GETapi-v1-messages-send" style="display: none">
            <input type="radio" name="prioritize"
                   value="false"
                   data-endpoint="GETapi-v1-messages-send"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>Whether to prioritize the campaign. This field is prohibited when sims is not present. Example: <code>false</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="GETapi-v1-messages-send"
               value="My Campaign"
               data-component="body">
    <br>
<p>The name of the campaign. Must not be greater than 255 characters. Example: <code>My Campaign</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>scheduled_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="scheduled_at"                data-endpoint="GETapi-v1-messages-send"
               value="2021-01-01T12:00:00Z"
               data-component="body">
    <br>
<p>The date and time the campaign is scheduled to run. Must be a valid date. Example: <code>2021-01-01T12:00:00Z</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>recurring</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
<i>optional</i> &nbsp;
                <label data-endpoint="GETapi-v1-messages-send" style="display: none">
            <input type="radio" name="recurring"
                   value="true"
                   data-endpoint="GETapi-v1-messages-send"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="GETapi-v1-messages-send" style="display: none">
            <input type="radio" name="recurring"
                   value="false"
                   data-endpoint="GETapi-v1-messages-send"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>Whether the campaign is recurring. Example: <code>false</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>frequency</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="frequency"                data-endpoint="GETapi-v1-messages-send"
               value="1"
               data-component="body">
    <br>
<p>The frequency of the campaign. This field is required when <code>recurring</code> is <code>true</code>. Must be at least 1. Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>frequency_unit</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="frequency_unit"                data-endpoint="GETapi-v1-messages-send"
               value="day"
               data-component="body">
    <br>
<p>The unit of the frequency. This field is required when <code>recurring</code> is <code>true</code>. Example: <code>day</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>Minute</code></li> <li><code>Hour</code></li> <li><code>Day</code></li> <li><code>Week</code></li> <li><code>Month</code></li> <li><code>Year</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>ends_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="ends_at"                data-endpoint="GETapi-v1-messages-send"
               value="2021-01-01T12:00:00Z"
               data-component="body">
    <br>
<p>The date and time when the recurring campaign ends. Must be a valid date. Example: <code>2021-01-01T12:00:00Z</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>timezone</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="timezone"                data-endpoint="GETapi-v1-messages-send"
               value="UTC"
               data-component="body">
    <br>
<p>The timezone of the campaign. This is used for scheduled_at, ends_at, days_of_week, and active_hours. This field is required when <code>active_hours</code>, <code>days_of_week</code>, <code>scheduled_at</code>, or <code>ends_at</code> is present. Must be a valid time zone, such as <code>Africa/Accra</code>. Example: <code>UTC</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>days_of_week</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="days_of_week[0]"                data-endpoint="GETapi-v1-messages-send"
               data-component="body">
        <input type="text" style="display: none"
               name="days_of_week[1]"                data-endpoint="GETapi-v1-messages-send"
               data-component="body">
    <br>
<p>The days of the week when the campaign is active.</p>
Must be one of:
<ul style="list-style-type: square;"><li><code>1</code></li> <li><code>2</code></li> <li><code>3</code></li> <li><code>4</code></li> <li><code>5</code></li> <li><code>6</code></li> <li><code>7</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>active_hours</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
<i>optional</i> &nbsp;
<br>
<p>Must contain 2 items.</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>start</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="active_hours.start"                data-endpoint="GETapi-v1-messages-send"
               value="08:00"
               data-component="body">
    <br>
<p>The start time of the active hours. This field is required when <code>active_hours.end</code> is present. Must be a valid date in the format <code>H:i</code>. Must be a date before <code>active_hours.end</code>. Example: <code>08:00</code></p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>end</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="active_hours.end"                data-endpoint="GETapi-v1-messages-send"
               value="17:00"
               data-component="body">
    <br>
<p>The end time of the active hours. This field is required when <code>active_hours.start</code> is present. Must be a valid date in the format <code>H:i</code>. Must be a date after <code>active_hours.start</code>. Example: <code>17:00</code></p>
                    </div>
                                    </details>
        </div>
        </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the campaign.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The name of the campaign.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>scheduled_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time when the campaign is scheduled to run.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>timezone</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The timezone of the campaign.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>recurring</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether the campaign is recurring.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>frequency</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The frequency of the campaign.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>frequency_unit</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The unit of the frequency.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>ends_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time when the recurring campaign ends.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>repeat_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time when the campaign is scheduled to repeat.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>active_hours</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The timespan of a day when the campaign is active.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>days_of_week</code></b>&nbsp;&nbsp;
<small>integer[]</small>&nbsp;
 &nbsp;
<br>
<p>The days of the week when the campaign is active.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The status of the campaign.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The type of the campaign.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>options</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>
<p>The options of the campaign.</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>delay</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The delay between each item before sending the next one.</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>prioritize</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether to prioritize the campaign.</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>delivery_report</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether to request delivery report for SMS.</p>
                    </div>
                                    </details>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>created_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time the campaign was created.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>updated_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time the campaign was last updated.</p>
        </div>
                        <h2 id="messages-GETapi-v1-messages">List all messages</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Returns a list of messages. The messages are returned sorted by sent date, with the most recently sent message appearing first.</p>

<span id="example-requests-GETapi-v1-messages">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "{{ config("app.url") }}/api/v1/messages?user=1&amp;campaign=1&amp;sim=1&amp;message=Hello%2C+World%21&amp;type=SMS&amp;statuses[]=Queued&amp;after=2021-01-01&amp;before=2024-01-01" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/messages"
);

const params = {
    "user": "1",
    "campaign": "1",
    "sim": "1",
    "message": "Hello, World!",
    "type": "SMS",
    "statuses[0]": "Queued",
    "after": "2021-01-01",
    "before": "2024-01-01",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/messages';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'query' =&gt; [
            'user' =&gt; '1',
            'campaign' =&gt; '1',
            'sim' =&gt; '1',
            'message' =&gt; 'Hello, World!',
            'type' =&gt; 'SMS',
            'statuses[0]' =&gt; 'Queued',
            'after' =&gt; '2021-01-01',
            'before' =&gt; '2024-01-01',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/messages'
params = {
  'user': '1',
  'campaign': '1',
  'sim': '1',
  'message': 'Hello, World!',
  'type': 'SMS',
  'statuses[0]': 'Queued',
  'after': '2021-01-01',
  'before': '2024-01-01',
}
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers, params=params)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-messages">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 2061,
            &quot;from&quot;: &quot;+17345017250&quot;,
            &quot;to&quot;: &quot;+19899609500&quot;,
            &quot;content&quot;: &quot;Itaque non aspernatur odio nam incidunt. Distinctio quam sed quasi commodi tempore.&quot;,
            &quot;type&quot;: &quot;SMS&quot;,
            &quot;attachments&quot;: [],
            &quot;status&quot;: &quot;Sent&quot;,
            &quot;response&quot;: [],
            &quot;messenger&quot;: &quot;Sim #20&quot;,
            &quot;sent_at&quot;: &quot;2025-09-07T08:20:33.000000Z&quot;,
            &quot;delivered_at&quot;: &quot;2025-09-08T05:42:25.000000Z&quot;
        },
        {
            &quot;id&quot;: 2062,
            &quot;from&quot;: &quot;+13309437084&quot;,
            &quot;to&quot;: &quot;+14094483472&quot;,
            &quot;content&quot;: &quot;Porro aut tempora ut possimus odit. Sed rem ducimus est ut voluptatum porro repudiandae doloremque. In est id sed voluptatem.&quot;,
            &quot;type&quot;: &quot;WhatsApp&quot;,
            &quot;attachments&quot;: [],
            &quot;status&quot;: &quot;Queued&quot;,
            &quot;response&quot;: [],
            &quot;messenger&quot;: &quot;Sim #21&quot;,
            &quot;sent_at&quot;: &quot;2025-09-07T15:42:10.000000Z&quot;,
            &quot;delivered_at&quot;: &quot;2025-09-08T05:42:25.000000Z&quot;
        }
    ],
    &quot;links&quot;: {
        &quot;first&quot;: &quot;/?page=1&quot;,
        &quot;last&quot;: &quot;/?page=1&quot;,
        &quot;prev&quot;: null,
        &quot;next&quot;: null
    },
    &quot;meta&quot;: {
        &quot;current_page&quot;: 1,
        &quot;from&quot;: 1,
        &quot;last_page&quot;: 1,
        &quot;links&quot;: [
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;&amp;laquo; Previous&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            },
            {
                &quot;url&quot;: &quot;/?page=1&quot;,
                &quot;label&quot;: &quot;1&quot;,
                &quot;page&quot;: 1,
                &quot;active&quot;: true
            },
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;Next &amp;raquo;&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            }
        ],
        &quot;path&quot;: &quot;/&quot;,
        &quot;per_page&quot;: 100,
        &quot;to&quot;: 2,
        &quot;total&quot;: 2
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-messages" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-messages"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-messages"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-messages" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-messages">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-messages" data-method="GET"
      data-path="api/v1/messages"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-messages', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-messages"
                    onclick="tryItOut('GETapi-v1-messages');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-messages"
                    onclick="cancelTryOut('GETapi-v1-messages');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-messages"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/messages</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-messages"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-messages"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-messages"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>user</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="user"                data-endpoint="GETapi-v1-messages"
               value="1"
               data-component="query">
    <br>
<p>Filter by user id. Only available for admin. Must be at least 1. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>campaign</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="campaign"                data-endpoint="GETapi-v1-messages"
               value="1"
               data-component="query">
    <br>
<p>Filter by campaign id. Must be at least 1. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>sim</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="sim"                data-endpoint="GETapi-v1-messages"
               value="1"
               data-component="query">
    <br>
<p>Filter by sim id. This field is prohibited if sender_id is present. Must be at least 1. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>message</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="message"                data-endpoint="GETapi-v1-messages"
               value="Hello, World!"
               data-component="query">
    <br>
<p>Filter by message. Must not be greater than 1600 characters. Example: <code>Hello, World!</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="type"                data-endpoint="GETapi-v1-messages"
               value="SMS"
               data-component="query">
    <br>
<p>Filter by message type. Example: <code>SMS</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>SMS</code></li> <li><code>MMS</code></li> <li><code>WhatsApp</code></li></ul>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>statuses</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="statuses[0]"                data-endpoint="GETapi-v1-messages"
               data-component="query">
        <input type="text" style="display: none"
               name="statuses[1]"                data-endpoint="GETapi-v1-messages"
               data-component="query">
    <br>

Must be one of:
<ul style="list-style-type: square;"><li><code>Pending</code></li> <li><code>Queued</code></li> <li><code>Processed</code></li> <li><code>Sent</code></li> <li><code>Delivered</code></li> <li><code>Failed</code></li> <li><code>Received</code></li></ul>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>after</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="after"                data-endpoint="GETapi-v1-messages"
               value="2021-01-01"
               data-component="query">
    <br>
<p>Filter by messages that are sent after this date. Must be a valid date in the format <code>Y-m-d</code>. Must be a date before or equal to <code>before</code>. Example: <code>2021-01-01</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>before</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="before"                data-endpoint="GETapi-v1-messages"
               value="2024-01-01"
               data-component="query">
    <br>
<p>Filter by messages that are sent before this date. Must be a valid date in the format <code>Y-m-d</code>. Must be a date after or equal to <code>after</code>. Example: <code>2024-01-01</code></p>
            </div>
                </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the message.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>from</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The sender of the message.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>to</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The recipient of the message.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>content</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The content of the message.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The type of the message.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>attachments</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
 &nbsp;
<br>
<p>List of all attachments for the message.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The status of the message.</p>
Must be one of:
<ul style="list-style-type: square;"><li><code>Pending</code></li> <li><code>Queued</code></li> <li><code>Processed</code></li> <li><code>Sent</code></li> <li><code>Delivered</code></li> <li><code>Failed</code></li> <li><code>Received</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>response</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>
<p>The response received from the device or sending server.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>messenger</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The sim or sender id used for the message.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>sent_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time when the message was sent.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>delivered_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time when the message was delivered.</p>
        </div>
                        <h2 id="messages-GETapi-v1-messages--id-">Retrieve a message</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retrieves a Message object.</p>

<span id="example-requests-GETapi-v1-messages--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "{{ config("app.url") }}/api/v1/messages/1" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/messages/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/messages/1';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/messages/1'
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-messages--id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;id&quot;: 2063,
    &quot;from&quot;: &quot;+15045565946&quot;,
    &quot;to&quot;: &quot;+14322823398&quot;,
    &quot;content&quot;: &quot;Eveniet et quasi qui corporis quae sequi. Est rerum quam recusandae omnis dolore. Perferendis et et amet earum illum qui.&quot;,
    &quot;type&quot;: &quot;WhatsApp&quot;,
    &quot;attachments&quot;: [],
    &quot;status&quot;: &quot;Sent&quot;,
    &quot;response&quot;: [],
    &quot;messenger&quot;: &quot;Sim #22&quot;,
    &quot;sent_at&quot;: &quot;2025-09-08T00:09:04.000000Z&quot;,
    &quot;delivered_at&quot;: &quot;2025-09-08T05:42:25.000000Z&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-messages--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-messages--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-messages--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-messages--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-messages--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-messages--id-" data-method="GET"
      data-path="api/v1/messages/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-messages--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-messages--id-"
                    onclick="tryItOut('GETapi-v1-messages--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-messages--id-"
                    onclick="cancelTryOut('GETapi-v1-messages--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-messages--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/messages/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-messages--id-"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-messages--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-messages--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-v1-messages--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the message. Example: <code>1</code></p>
            </div>
                    </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the message.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>from</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The sender of the message.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>to</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The recipient of the message.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>content</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The content of the message.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The type of the message.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>attachments</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
 &nbsp;
<br>
<p>List of all attachments for the message.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The status of the message.</p>
Must be one of:
<ul style="list-style-type: square;"><li><code>Pending</code></li> <li><code>Queued</code></li> <li><code>Processed</code></li> <li><code>Sent</code></li> <li><code>Delivered</code></li> <li><code>Failed</code></li> <li><code>Received</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>response</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>
<p>The response received from the device or sending server.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>messenger</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The sim or sender id used for the message.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>sent_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time when the message was sent.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>delivered_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time when the message was delivered.</p>
        </div>
                        <h2 id="messages-DELETEapi-v1-messages--id-">Delete a message</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Permanently deletes a message. It cannot be undone.</p>

<span id="example-requests-DELETEapi-v1-messages--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "{{ config("app.url") }}/api/v1/messages/1" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/messages/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/messages/1';
$response = $client-&gt;delete(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/messages/1'
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('DELETE', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-DELETEapi-v1-messages--id-">
            <blockquote>
            <p>Example response (204, No content):</p>
        </blockquote>
                <pre>
<code>Empty response</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-v1-messages--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-v1-messages--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-v1-messages--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-v1-messages--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-v1-messages--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-v1-messages--id-" data-method="DELETE"
      data-path="api/v1/messages/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-v1-messages--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-v1-messages--id-"
                    onclick="tryItOut('DELETEapi-v1-messages--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-v1-messages--id-"
                    onclick="cancelTryOut('DELETEapi-v1-messages--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-v1-messages--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/v1/messages/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="DELETEapi-v1-messages--id-"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-v1-messages--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-v1-messages--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="DELETEapi-v1-messages--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the message. Example: <code>1</code></p>
            </div>
                    </form>

                <h1 id="senderids">SenderIds</h1>

    <p>SenderIds</p>

                                <h2 id="senderids-GETapi-v1-sender-ids">List all sender ids</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Returns a list of sender ids. The sender ids are returned sorted by creation date, with the most recently created sender id appearing first.</p>

<span id="example-requests-GETapi-v1-sender-ids">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "{{ config("app.url") }}/api/v1/sender-ids" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/sender-ids"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/sender-ids';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/sender-ids'
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-sender-ids">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 23,
            &quot;value&quot;: &quot;647973&quot;
        },
        {
            &quot;id&quot;: 24,
            &quot;value&quot;: &quot;731066&quot;
        }
    ],
    &quot;links&quot;: {
        &quot;first&quot;: &quot;/?page=1&quot;,
        &quot;last&quot;: &quot;/?page=1&quot;,
        &quot;prev&quot;: null,
        &quot;next&quot;: null
    },
    &quot;meta&quot;: {
        &quot;current_page&quot;: 1,
        &quot;from&quot;: 1,
        &quot;last_page&quot;: 1,
        &quot;links&quot;: [
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;&amp;laquo; Previous&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            },
            {
                &quot;url&quot;: &quot;/?page=1&quot;,
                &quot;label&quot;: &quot;1&quot;,
                &quot;page&quot;: 1,
                &quot;active&quot;: true
            },
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;Next &amp;raquo;&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            }
        ],
        &quot;path&quot;: &quot;/&quot;,
        &quot;per_page&quot;: 100,
        &quot;to&quot;: 2,
        &quot;total&quot;: 2
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-sender-ids" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-sender-ids"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-sender-ids"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-sender-ids" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-sender-ids">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-sender-ids" data-method="GET"
      data-path="api/v1/sender-ids"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-sender-ids', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-sender-ids"
                    onclick="tryItOut('GETapi-v1-sender-ids');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-sender-ids"
                    onclick="cancelTryOut('GETapi-v1-sender-ids');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-sender-ids"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/sender-ids</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-sender-ids"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-sender-ids"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-sender-ids"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the sender id.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>value</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The value of the sender id.</p>
        </div>
                        <h2 id="senderids-GETapi-v1-sender-ids--id-">Retrieve a sender id</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retrieves a SenderId object.</p>

<span id="example-requests-GETapi-v1-sender-ids--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "{{ config("app.url") }}/api/v1/sender-ids/1" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/sender-ids/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/sender-ids/1';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/sender-ids/1'
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-sender-ids--id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;id&quot;: 25,
    &quot;value&quot;: &quot;574311&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-sender-ids--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-sender-ids--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-sender-ids--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-sender-ids--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-sender-ids--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-sender-ids--id-" data-method="GET"
      data-path="api/v1/sender-ids/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-sender-ids--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-sender-ids--id-"
                    onclick="tryItOut('GETapi-v1-sender-ids--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-sender-ids--id-"
                    onclick="cancelTryOut('GETapi-v1-sender-ids--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-sender-ids--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/sender-ids/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-sender-ids--id-"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-sender-ids--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-sender-ids--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-v1-sender-ids--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the sender id. Example: <code>1</code></p>
            </div>
                    </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the sender id.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>value</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The value of the sender id.</p>
        </div>
                        <h2 id="senderids-POSTapi-v1-sending-servers--sending_server_id--sender-ids">Create a sender id for a sending server</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTapi-v1-sending-servers--sending_server_id--sender-ids">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "{{ config("app.url") }}/api/v1/sending-servers/1/sender-ids" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"value\": \"MySenderID\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/sending-servers/1/sender-ids"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "value": "MySenderID"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/sending-servers/1/sender-ids';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'value' =&gt; 'MySenderID',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/sending-servers/1/sender-ids'
payload = {
    "value": "MySenderID"
}
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('POST', url, headers=headers, json=payload)
response.json()</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-sending-servers--sending_server_id--sender-ids">
            <blockquote>
            <p>Example response (201, Created):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;id&quot;: 26,
    &quot;value&quot;: &quot;583848&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-v1-sending-servers--sending_server_id--sender-ids" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-sending-servers--sending_server_id--sender-ids"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-sending-servers--sending_server_id--sender-ids"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-sending-servers--sending_server_id--sender-ids" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-sending-servers--sending_server_id--sender-ids">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-sending-servers--sending_server_id--sender-ids" data-method="POST"
      data-path="api/v1/sending-servers/{sending_server_id}/sender-ids"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-sending-servers--sending_server_id--sender-ids', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-sending-servers--sending_server_id--sender-ids"
                    onclick="tryItOut('POSTapi-v1-sending-servers--sending_server_id--sender-ids');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-sending-servers--sending_server_id--sender-ids"
                    onclick="cancelTryOut('POSTapi-v1-sending-servers--sending_server_id--sender-ids');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-sending-servers--sending_server_id--sender-ids"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/sending-servers/{sending_server_id}/sender-ids</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-v1-sending-servers--sending_server_id--sender-ids"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-sending-servers--sending_server_id--sender-ids"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-sending-servers--sending_server_id--sender-ids"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>sending_server_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="sending_server_id"                data-endpoint="POSTapi-v1-sending-servers--sending_server_id--sender-ids"
               value="1"
               data-component="url">
    <br>
<p>The ID of the sending server. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>value</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="value"                data-endpoint="POSTapi-v1-sending-servers--sending_server_id--sender-ids"
               value="MySenderID"
               data-component="body">
    <br>
<p>The sender id value. Must not be greater than 11 characters. Example: <code>MySenderID</code></p>
        </div>
        </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the sender id.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>value</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The value of the sender id.</p>
        </div>
                        <h2 id="senderids-DELETEapi-v1-sender-ids--id-">Delete a sender id</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Permanently deletes a sender id. Also removes any messages sent using this sender id. It cannot be undone.</p>

<span id="example-requests-DELETEapi-v1-sender-ids--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "{{ config("app.url") }}/api/v1/sender-ids/1" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/sender-ids/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/sender-ids/1';
$response = $client-&gt;delete(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/sender-ids/1'
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('DELETE', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-DELETEapi-v1-sender-ids--id-">
            <blockquote>
            <p>Example response (204, No content):</p>
        </blockquote>
                <pre>
<code>Empty response</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-v1-sender-ids--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-v1-sender-ids--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-v1-sender-ids--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-v1-sender-ids--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-v1-sender-ids--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-v1-sender-ids--id-" data-method="DELETE"
      data-path="api/v1/sender-ids/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-v1-sender-ids--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-v1-sender-ids--id-"
                    onclick="tryItOut('DELETEapi-v1-sender-ids--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-v1-sender-ids--id-"
                    onclick="cancelTryOut('DELETEapi-v1-sender-ids--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-v1-sender-ids--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/v1/sender-ids/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="DELETEapi-v1-sender-ids--id-"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-v1-sender-ids--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-v1-sender-ids--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="DELETEapi-v1-sender-ids--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the sender id. Example: <code>1</code></p>
            </div>
                    </form>

                <h1 id="sending-servers">Sending Servers</h1>

    <p>Sending Servers</p>

                                <h2 id="sending-servers-GETapi-v1-sending-servers">List all sending servers</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Returns a list of sending servers. The sending servers are returned sorted by creation date, with the most recently created sending server appearing first.</p>

<span id="example-requests-GETapi-v1-sending-servers">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "{{ config("app.url") }}/api/v1/sending-servers" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/sending-servers"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/sending-servers';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/sending-servers'
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-sending-servers">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 5,
            &quot;name&quot;: &quot;Gideon Boyer&quot;,
            &quot;driver&quot;: &quot;Textlocal&quot;,
            &quot;supported_types&quot;: [
                &quot;SMS&quot;
            ],
            &quot;config&quot;: {
                &quot;url&quot;: &quot;https://api.textlocal.in&quot;,
                &quot;test&quot;: true,
                &quot;api_key&quot;: &quot;NDQ2NDYyNGQ0OTY2NGU3Njc5Nzc0ZTM0Njk2NjQzNjI=&quot;
            },
            &quot;enabled&quot;: true,
            &quot;created_at&quot;: &quot;2025-09-08T05:42:25.000000Z&quot;
        },
        {
            &quot;id&quot;: 6,
            &quot;name&quot;: &quot;Amelia Koelpin&quot;,
            &quot;driver&quot;: &quot;Textlocal&quot;,
            &quot;supported_types&quot;: [
                &quot;MMS&quot;
            ],
            &quot;config&quot;: {
                &quot;url&quot;: &quot;https://api.textlocal.in&quot;,
                &quot;test&quot;: true,
                &quot;api_key&quot;: &quot;NDQ2NDYyNGQ0OTY2NGU3Njc5Nzc0ZTM0Njk2NjQzNjI=&quot;
            },
            &quot;enabled&quot;: true,
            &quot;created_at&quot;: &quot;2025-09-08T05:42:25.000000Z&quot;
        }
    ],
    &quot;links&quot;: {
        &quot;first&quot;: &quot;/?page=1&quot;,
        &quot;last&quot;: &quot;/?page=1&quot;,
        &quot;prev&quot;: null,
        &quot;next&quot;: null
    },
    &quot;meta&quot;: {
        &quot;current_page&quot;: 1,
        &quot;from&quot;: 1,
        &quot;last_page&quot;: 1,
        &quot;links&quot;: [
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;&amp;laquo; Previous&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            },
            {
                &quot;url&quot;: &quot;/?page=1&quot;,
                &quot;label&quot;: &quot;1&quot;,
                &quot;page&quot;: 1,
                &quot;active&quot;: true
            },
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;Next &amp;raquo;&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            }
        ],
        &quot;path&quot;: &quot;/&quot;,
        &quot;per_page&quot;: 100,
        &quot;to&quot;: 2,
        &quot;total&quot;: 2
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-sending-servers" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-sending-servers"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-sending-servers"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-sending-servers" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-sending-servers">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-sending-servers" data-method="GET"
      data-path="api/v1/sending-servers"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-sending-servers', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-sending-servers"
                    onclick="tryItOut('GETapi-v1-sending-servers');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-sending-servers"
                    onclick="cancelTryOut('GETapi-v1-sending-servers');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-sending-servers"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/sending-servers</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-sending-servers"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-sending-servers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-sending-servers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the sending server.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The name of the sending server.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>driver</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The driver of the sending server.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>supported_types</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
 &nbsp;
<br>
<p>The types of messages supported by the sending server.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>config</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>
<p>The sending server configuration.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>enabled</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether the sending server is enabled or not.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>created_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time when the sending server was created.</p>
        </div>
                        <h2 id="sending-servers-GETapi-v1-sending-servers--id-">Retrieve a sending server</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retrieves a SendingServer object.</p>

<span id="example-requests-GETapi-v1-sending-servers--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "{{ config("app.url") }}/api/v1/sending-servers/1" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/sending-servers/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/sending-servers/1';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/sending-servers/1'
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-sending-servers--id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;id&quot;: 7,
    &quot;name&quot;: &quot;Gideon Boyer&quot;,
    &quot;driver&quot;: &quot;Textlocal&quot;,
    &quot;supported_types&quot;: [
        &quot;SMS&quot;
    ],
    &quot;config&quot;: {
        &quot;url&quot;: &quot;https://api.textlocal.in&quot;,
        &quot;test&quot;: true,
        &quot;api_key&quot;: &quot;NDQ2NDYyNGQ0OTY2NGU3Njc5Nzc0ZTM0Njk2NjQzNjI=&quot;
    },
    &quot;enabled&quot;: true,
    &quot;created_at&quot;: &quot;2025-09-08T05:42:25.000000Z&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-sending-servers--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-sending-servers--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-sending-servers--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-sending-servers--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-sending-servers--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-sending-servers--id-" data-method="GET"
      data-path="api/v1/sending-servers/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-sending-servers--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-sending-servers--id-"
                    onclick="tryItOut('GETapi-v1-sending-servers--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-sending-servers--id-"
                    onclick="cancelTryOut('GETapi-v1-sending-servers--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-sending-servers--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/sending-servers/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-sending-servers--id-"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-sending-servers--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-sending-servers--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-v1-sending-servers--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the sending server. Example: <code>1</code></p>
            </div>
                    </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the sending server.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The name of the sending server.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>driver</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The driver of the sending server.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>supported_types</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
 &nbsp;
<br>
<p>The types of messages supported by the sending server.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>config</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>
<p>The sending server configuration.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>enabled</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether the sending server is enabled or not.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>created_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time when the sending server was created.</p>
        </div>
                        <h2 id="sending-servers-POSTapi-v1-sending-servers">Create a sending server</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTapi-v1-sending-servers">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "{{ config("app.url") }}/api/v1/sending-servers" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"Twilio\",
    \"driver\": \"twilio\",
    \"supported_types\": [
        \"MMS\"
    ],
    \"config\": {
        \"account_sid\": \"ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX\",
        \"auth_token\": \"your_auth_token\",
        \"from\": \"+15017122661\"
    },
    \"enabled\": false
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/sending-servers"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "Twilio",
    "driver": "twilio",
    "supported_types": [
        "MMS"
    ],
    "config": {
        "account_sid": "ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX",
        "auth_token": "your_auth_token",
        "from": "+15017122661"
    },
    "enabled": false
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/sending-servers';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'name' =&gt; 'Twilio',
            'driver' =&gt; 'twilio',
            'supported_types' =&gt; [
                'MMS',
            ],
            'config' =&gt; [
                'account_sid' =&gt; 'ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
                'auth_token' =&gt; 'your_auth_token',
                'from' =&gt; '+15017122661',
            ],
            'enabled' =&gt; false,
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/sending-servers'
payload = {
    "name": "Twilio",
    "driver": "twilio",
    "supported_types": [
        "MMS"
    ],
    "config": {
        "account_sid": "ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX",
        "auth_token": "your_auth_token",
        "from": "+15017122661"
    },
    "enabled": false
}
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('POST', url, headers=headers, json=payload)
response.json()</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-sending-servers">
            <blockquote>
            <p>Example response (201, Created):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;id&quot;: 8,
    &quot;name&quot;: &quot;Gideon Boyer&quot;,
    &quot;driver&quot;: &quot;Textlocal&quot;,
    &quot;supported_types&quot;: [
        &quot;SMS&quot;
    ],
    &quot;config&quot;: {
        &quot;url&quot;: &quot;https://api.textlocal.in&quot;,
        &quot;test&quot;: true,
        &quot;api_key&quot;: &quot;NDQ2NDYyNGQ0OTY2NGU3Njc5Nzc0ZTM0Njk2NjQzNjI=&quot;
    },
    &quot;enabled&quot;: true,
    &quot;created_at&quot;: &quot;2025-09-08T05:42:25.000000Z&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-v1-sending-servers" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-sending-servers"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-sending-servers"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-sending-servers" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-sending-servers">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-sending-servers" data-method="POST"
      data-path="api/v1/sending-servers"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-sending-servers', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-sending-servers"
                    onclick="tryItOut('POSTapi-v1-sending-servers');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-sending-servers"
                    onclick="cancelTryOut('POSTapi-v1-sending-servers');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-sending-servers"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/sending-servers</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-v1-sending-servers"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-sending-servers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-sending-servers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTapi-v1-sending-servers"
               value="Twilio"
               data-component="body">
    <br>
<p>The name of the sending server. Example: <code>Twilio</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>driver</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="driver"                data-endpoint="POSTapi-v1-sending-servers"
               value="twilio"
               data-component="body">
    <br>
<p>The driver of the sending server. Example: <code>twilio</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>Custom</code></li> <li><code>Textlocal</code></li> <li><code>Twilio</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>supported_types</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="supported_types[0]"                data-endpoint="POSTapi-v1-sending-servers"
               data-component="body">
        <input type="text" style="display: none"
               name="supported_types[1]"                data-endpoint="POSTapi-v1-sending-servers"
               data-component="body">
    <br>

Must be one of:
<ul style="list-style-type: square;"><li><code>SMS</code></li> <li><code>MMS</code></li> <li><code>WhatsApp</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>config</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="config"                data-endpoint="POSTapi-v1-sending-servers"
               value=""
               data-component="body">
    <br>
<p>The configuration for the sending server.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>enabled</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
                <label data-endpoint="POSTapi-v1-sending-servers" style="display: none">
            <input type="radio" name="enabled"
                   value="true"
                   data-endpoint="POSTapi-v1-sending-servers"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="POSTapi-v1-sending-servers" style="display: none">
            <input type="radio" name="enabled"
                   value="false"
                   data-endpoint="POSTapi-v1-sending-servers"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>The status of the sending server. Example: <code>false</code></p>
        </div>
        </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the sending server.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The name of the sending server.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>driver</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The driver of the sending server.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>supported_types</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
 &nbsp;
<br>
<p>The types of messages supported by the sending server.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>config</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>
<p>The sending server configuration.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>enabled</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether the sending server is enabled or not.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>created_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time when the sending server was created.</p>
        </div>
                        <h2 id="sending-servers-PUTapi-v1-sending-servers--id-">Update a sending server</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Update the specific sending server by setting the values of the parameters passed. It is recommended to retrieve the sending server first to get the sending server's current state because partial updates are not supported.</p>

<span id="example-requests-PUTapi-v1-sending-servers--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "{{ config("app.url") }}/api/v1/sending-servers/1" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"Twilio\",
    \"driver\": \"twilio\",
    \"supported_types\": [
        \"MMS\"
    ],
    \"config\": {
        \"account_sid\": \"ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX\",
        \"auth_token\": \"your_auth_token\",
        \"from\": \"+15017122661\"
    },
    \"enabled\": false
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/sending-servers/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "Twilio",
    "driver": "twilio",
    "supported_types": [
        "MMS"
    ],
    "config": {
        "account_sid": "ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX",
        "auth_token": "your_auth_token",
        "from": "+15017122661"
    },
    "enabled": false
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/sending-servers/1';
$response = $client-&gt;put(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'name' =&gt; 'Twilio',
            'driver' =&gt; 'twilio',
            'supported_types' =&gt; [
                'MMS',
            ],
            'config' =&gt; [
                'account_sid' =&gt; 'ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
                'auth_token' =&gt; 'your_auth_token',
                'from' =&gt; '+15017122661',
            ],
            'enabled' =&gt; false,
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/sending-servers/1'
payload = {
    "name": "Twilio",
    "driver": "twilio",
    "supported_types": [
        "MMS"
    ],
    "config": {
        "account_sid": "ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX",
        "auth_token": "your_auth_token",
        "from": "+15017122661"
    },
    "enabled": false
}
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('PUT', url, headers=headers, json=payload)
response.json()</code></pre></div>

</span>

<span id="example-responses-PUTapi-v1-sending-servers--id-">
            <blockquote>
            <p>Example response (204, No content):</p>
        </blockquote>
                <pre>
<code>Empty response</code>
 </pre>
    </span>
<span id="execution-results-PUTapi-v1-sending-servers--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-v1-sending-servers--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-v1-sending-servers--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-v1-sending-servers--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-v1-sending-servers--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-v1-sending-servers--id-" data-method="PUT"
      data-path="api/v1/sending-servers/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-v1-sending-servers--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-v1-sending-servers--id-"
                    onclick="tryItOut('PUTapi-v1-sending-servers--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-v1-sending-servers--id-"
                    onclick="cancelTryOut('PUTapi-v1-sending-servers--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-v1-sending-servers--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/v1/sending-servers/{id}</code></b>
        </p>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/v1/sending-servers/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="PUTapi-v1-sending-servers--id-"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-v1-sending-servers--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-v1-sending-servers--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="PUTapi-v1-sending-servers--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the sending server. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="PUTapi-v1-sending-servers--id-"
               value="Twilio"
               data-component="body">
    <br>
<p>The name of the sending server. Example: <code>Twilio</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>driver</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="driver"                data-endpoint="PUTapi-v1-sending-servers--id-"
               value="twilio"
               data-component="body">
    <br>
<p>The driver of the sending server. Example: <code>twilio</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>Custom</code></li> <li><code>Textlocal</code></li> <li><code>Twilio</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>supported_types</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="supported_types[0]"                data-endpoint="PUTapi-v1-sending-servers--id-"
               data-component="body">
        <input type="text" style="display: none"
               name="supported_types[1]"                data-endpoint="PUTapi-v1-sending-servers--id-"
               data-component="body">
    <br>

Must be one of:
<ul style="list-style-type: square;"><li><code>SMS</code></li> <li><code>MMS</code></li> <li><code>WhatsApp</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>config</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="config"                data-endpoint="PUTapi-v1-sending-servers--id-"
               value=""
               data-component="body">
    <br>
<p>The configuration for the sending server.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>enabled</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
                <label data-endpoint="PUTapi-v1-sending-servers--id-" style="display: none">
            <input type="radio" name="enabled"
                   value="true"
                   data-endpoint="PUTapi-v1-sending-servers--id-"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="PUTapi-v1-sending-servers--id-" style="display: none">
            <input type="radio" name="enabled"
                   value="false"
                   data-endpoint="PUTapi-v1-sending-servers--id-"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>The status of the sending server. Example: <code>false</code></p>
        </div>
        </form>

                    <h2 id="sending-servers-DELETEapi-v1-sending-servers--id-">Delete a sending server</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Permanently deletes a sending server. Also removes sender ids associated with the sending server and any messages associated with those sender ids. It cannot be undone.</p>

<span id="example-requests-DELETEapi-v1-sending-servers--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "{{ config("app.url") }}/api/v1/sending-servers/1" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/sending-servers/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/sending-servers/1';
$response = $client-&gt;delete(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/sending-servers/1'
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('DELETE', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-DELETEapi-v1-sending-servers--id-">
            <blockquote>
            <p>Example response (204, No content):</p>
        </blockquote>
                <pre>
<code>Empty response</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-v1-sending-servers--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-v1-sending-servers--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-v1-sending-servers--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-v1-sending-servers--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-v1-sending-servers--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-v1-sending-servers--id-" data-method="DELETE"
      data-path="api/v1/sending-servers/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-v1-sending-servers--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-v1-sending-servers--id-"
                    onclick="tryItOut('DELETEapi-v1-sending-servers--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-v1-sending-servers--id-"
                    onclick="cancelTryOut('DELETEapi-v1-sending-servers--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-v1-sending-servers--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/v1/sending-servers/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="DELETEapi-v1-sending-servers--id-"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-v1-sending-servers--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-v1-sending-servers--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="DELETEapi-v1-sending-servers--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the sending server. Example: <code>1</code></p>
            </div>
                    </form>

                <h1 id="ussd-pulls">USSD Pulls</h1>

    <p>USSD Pulls</p>

                                <h2 id="ussd-pulls-GETapi-v1-ussd-pulls-send">Send USSD pulls</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Sends USSD Pull campaign.</p>

<span id="example-requests-GETapi-v1-ussd-pulls-send">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "{{ config("app.url") }}/api/v1/ussd-pulls/send" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"sims\": [
        1
    ],
    \"ussd_codes\": [
        \"*123#\"
    ],
    \"delay\": 60,
    \"prioritize\": false,
    \"name\": \"My Campaign\",
    \"scheduled_at\": \"2021-01-01T12:00:00Z\",
    \"recurring\": false,
    \"frequency\": 1,
    \"frequency_unit\": \"day\",
    \"ends_at\": \"2021-01-01T12:00:00Z\",
    \"timezone\": \"UTC\",
    \"days_of_week\": [
        1
    ],
    \"active_hours\": {
        \"start\": \"08:00\",
        \"end\": \"17:00\"
    },
    \"random_sender\": false
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/ussd-pulls/send"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "sims": [
        1
    ],
    "ussd_codes": [
        "*123#"
    ],
    "delay": 60,
    "prioritize": false,
    "name": "My Campaign",
    "scheduled_at": "2021-01-01T12:00:00Z",
    "recurring": false,
    "frequency": 1,
    "frequency_unit": "day",
    "ends_at": "2021-01-01T12:00:00Z",
    "timezone": "UTC",
    "days_of_week": [
        1
    ],
    "active_hours": {
        "start": "08:00",
        "end": "17:00"
    },
    "random_sender": false
};

fetch(url, {
    method: "GET",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/ussd-pulls/send';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'sims' =&gt; [
                1,
            ],
            'ussd_codes' =&gt; [
                '*123#',
            ],
            'delay' =&gt; 60,
            'prioritize' =&gt; false,
            'name' =&gt; 'My Campaign',
            'scheduled_at' =&gt; '2021-01-01T12:00:00Z',
            'recurring' =&gt; false,
            'frequency' =&gt; 1,
            'frequency_unit' =&gt; 'day',
            'ends_at' =&gt; '2021-01-01T12:00:00Z',
            'timezone' =&gt; 'UTC',
            'days_of_week' =&gt; [
                1,
            ],
            'active_hours' =&gt; [
                'start' =&gt; '08:00',
                'end' =&gt; '17:00',
            ],
            'random_sender' =&gt; false,
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/ussd-pulls/send'
payload = {
    "sims": [
        1
    ],
    "ussd_codes": [
        "*123#"
    ],
    "delay": 60,
    "prioritize": false,
    "name": "My Campaign",
    "scheduled_at": "2021-01-01T12:00:00Z",
    "recurring": false,
    "frequency": 1,
    "frequency_unit": "day",
    "ends_at": "2021-01-01T12:00:00Z",
    "timezone": "UTC",
    "days_of_week": [
        1
    ],
    "active_hours": {
        "start": "08:00",
        "end": "17:00"
    },
    "random_sender": false
}
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers, json=payload)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-ussd-pulls-send">
            <blockquote>
            <p>Example response (201, Created):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;id&quot;: 14,
    &quot;name&quot;: &quot;Blaise Pouros&quot;,
    &quot;scheduled_at&quot;: &quot;2025-12-16T20:37:15.000000Z&quot;,
    &quot;timezone&quot;: &quot;America/Indiana/Knox&quot;,
    &quot;recurring&quot;: true,
    &quot;frequency&quot;: 5,
    &quot;frequency_unit&quot;: &quot;Minute&quot;,
    &quot;ends_at&quot;: &quot;2026-09-05T09:37:45.000000Z&quot;,
    &quot;repeat_at&quot;: null,
    &quot;active_hours&quot;: &quot;09:00-17:00&quot;,
    &quot;days_of_week&quot;: [
        1,
        3,
        5,
        7
    ],
    &quot;status&quot;: &quot;Processed&quot;,
    &quot;type&quot;: &quot;MMS&quot;,
    &quot;options&quot;: {
        &quot;delay&quot;: &quot;34&quot;,
        &quot;prioritize&quot;: true
    },
    &quot;created_at&quot;: &quot;2025-09-08T05:42:26.000000Z&quot;,
    &quot;updated_at&quot;: &quot;2025-09-08T05:42:26.000000Z&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-ussd-pulls-send" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-ussd-pulls-send"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-ussd-pulls-send"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-ussd-pulls-send" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-ussd-pulls-send">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-ussd-pulls-send" data-method="GET"
      data-path="api/v1/ussd-pulls/send"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-ussd-pulls-send', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-ussd-pulls-send"
                    onclick="tryItOut('GETapi-v1-ussd-pulls-send');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-ussd-pulls-send"
                    onclick="cancelTryOut('GETapi-v1-ussd-pulls-send');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-ussd-pulls-send"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/ussd-pulls/send</code></b>
        </p>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/ussd-pulls/send</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-ussd-pulls-send"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-ussd-pulls-send"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-ussd-pulls-send"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>sims</code></b>&nbsp;&nbsp;
<small>integer[]</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="sims[0]"                data-endpoint="GETapi-v1-ussd-pulls-send"
               data-component="body">
        <input type="number" style="display: none"
               name="sims[1]"                data-endpoint="GETapi-v1-ussd-pulls-send"
               data-component="body">
    <br>
<p>The ids of the sims to use for sending the USSD pulls. You can also set the first element to * and it will use all available sims.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>ussd_codes</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="ussd_codes[0]"                data-endpoint="GETapi-v1-ussd-pulls-send"
               data-component="body">
        <input type="text" style="display: none"
               name="ussd_codes[1]"                data-endpoint="GETapi-v1-ussd-pulls-send"
               data-component="body">
    <br>
<p>The array of USSD codes to send. Must match the regex /(?:*\d+)+#/.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>delay</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="delay"                data-endpoint="GETapi-v1-ussd-pulls-send"
               value="60"
               data-component="body">
    <br>
<p>The delay in seconds between each USSD pull. Example: <code>60</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>prioritize</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
<i>optional</i> &nbsp;
                <label data-endpoint="GETapi-v1-ussd-pulls-send" style="display: none">
            <input type="radio" name="prioritize"
                   value="true"
                   data-endpoint="GETapi-v1-ussd-pulls-send"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="GETapi-v1-ussd-pulls-send" style="display: none">
            <input type="radio" name="prioritize"
                   value="false"
                   data-endpoint="GETapi-v1-ussd-pulls-send"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>Whether to prioritize the campaign. Example: <code>false</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="GETapi-v1-ussd-pulls-send"
               value="My Campaign"
               data-component="body">
    <br>
<p>The name of the campaign. Must not be greater than 255 characters. Example: <code>My Campaign</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>scheduled_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="scheduled_at"                data-endpoint="GETapi-v1-ussd-pulls-send"
               value="2021-01-01T12:00:00Z"
               data-component="body">
    <br>
<p>The date and time the campaign is scheduled to run. Must be a valid date. Example: <code>2021-01-01T12:00:00Z</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>recurring</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
<i>optional</i> &nbsp;
                <label data-endpoint="GETapi-v1-ussd-pulls-send" style="display: none">
            <input type="radio" name="recurring"
                   value="true"
                   data-endpoint="GETapi-v1-ussd-pulls-send"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="GETapi-v1-ussd-pulls-send" style="display: none">
            <input type="radio" name="recurring"
                   value="false"
                   data-endpoint="GETapi-v1-ussd-pulls-send"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>Whether the campaign is recurring. Example: <code>false</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>frequency</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="frequency"                data-endpoint="GETapi-v1-ussd-pulls-send"
               value="1"
               data-component="body">
    <br>
<p>The frequency of the campaign. This field is required when <code>recurring</code> is <code>true</code>. Must be at least 1. Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>frequency_unit</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="frequency_unit"                data-endpoint="GETapi-v1-ussd-pulls-send"
               value="day"
               data-component="body">
    <br>
<p>The unit of the frequency. This field is required when <code>recurring</code> is <code>true</code>. Example: <code>day</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>Minute</code></li> <li><code>Hour</code></li> <li><code>Day</code></li> <li><code>Week</code></li> <li><code>Month</code></li> <li><code>Year</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>ends_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="ends_at"                data-endpoint="GETapi-v1-ussd-pulls-send"
               value="2021-01-01T12:00:00Z"
               data-component="body">
    <br>
<p>The date and time when the recurring campaign ends. Must be a valid date. Example: <code>2021-01-01T12:00:00Z</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>timezone</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="timezone"                data-endpoint="GETapi-v1-ussd-pulls-send"
               value="UTC"
               data-component="body">
    <br>
<p>The timezone of the campaign. This is used for scheduled_at, ends_at, days_of_week, and active_hours. This field is required when <code>active_hours</code>, <code>days_of_week</code>, <code>scheduled_at</code>, or <code>ends_at</code> is present. Must be a valid time zone, such as <code>Africa/Accra</code>. Example: <code>UTC</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>days_of_week</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="days_of_week[0]"                data-endpoint="GETapi-v1-ussd-pulls-send"
               data-component="body">
        <input type="text" style="display: none"
               name="days_of_week[1]"                data-endpoint="GETapi-v1-ussd-pulls-send"
               data-component="body">
    <br>
<p>The days of the week when the campaign is active.</p>
Must be one of:
<ul style="list-style-type: square;"><li><code>1</code></li> <li><code>2</code></li> <li><code>3</code></li> <li><code>4</code></li> <li><code>5</code></li> <li><code>6</code></li> <li><code>7</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>active_hours</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
<i>optional</i> &nbsp;
<br>
<p>Must contain 2 items.</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>start</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="active_hours.start"                data-endpoint="GETapi-v1-ussd-pulls-send"
               value="08:00"
               data-component="body">
    <br>
<p>The start time of the active hours. This field is required when <code>active_hours.end</code> is present. Must be a valid date in the format <code>H:i</code>. Must be a date before <code>active_hours.end</code>. Example: <code>08:00</code></p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>end</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="active_hours.end"                data-endpoint="GETapi-v1-ussd-pulls-send"
               value="17:00"
               data-component="body">
    <br>
<p>The end time of the active hours. This field is required when <code>active_hours.start</code> is present. Must be a valid date in the format <code>H:i</code>. Must be a date after <code>active_hours.start</code>. Example: <code>17:00</code></p>
                    </div>
                                    </details>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>random_sender</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
<i>optional</i> &nbsp;
                <label data-endpoint="GETapi-v1-ussd-pulls-send" style="display: none">
            <input type="radio" name="random_sender"
                   value="true"
                   data-endpoint="GETapi-v1-ussd-pulls-send"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="GETapi-v1-ussd-pulls-send" style="display: none">
            <input type="radio" name="random_sender"
                   value="false"
                   data-endpoint="GETapi-v1-ussd-pulls-send"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>Whether to use a random sim from selected sims for entire campaign. Example: <code>false</code></p>
        </div>
        </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the campaign.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The name of the campaign.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>scheduled_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time when the campaign is scheduled to run.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>timezone</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The timezone of the campaign.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>recurring</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether the campaign is recurring.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>frequency</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The frequency of the campaign.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>frequency_unit</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The unit of the frequency.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>ends_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time when the recurring campaign ends.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>repeat_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time when the campaign is scheduled to repeat.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>active_hours</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The timespan of a day when the campaign is active.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>days_of_week</code></b>&nbsp;&nbsp;
<small>integer[]</small>&nbsp;
 &nbsp;
<br>
<p>The days of the week when the campaign is active.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The status of the campaign.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The type of the campaign.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>options</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>
<p>The options of the campaign.</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>delay</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The delay between each item before sending the next one.</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>prioritize</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether to prioritize the campaign.</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>delivery_report</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
<br>
<p>Whether to request delivery report for SMS.</p>
                    </div>
                                    </details>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>created_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time the campaign was created.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>updated_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time the campaign was last updated.</p>
        </div>
                        <h2 id="ussd-pulls-GETapi-v1-ussd-pulls">List all USSD pulls</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Returns a list of USSD pulls. The USSD pulls are returned sorted by sent date, with the most recently sent USSD pull appearing first.</p>

<span id="example-requests-GETapi-v1-ussd-pulls">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "{{ config("app.url") }}/api/v1/ussd-pulls?user=1&amp;campaign=1&amp;sim=1&amp;statuses[]=Queued&amp;after=2021-01-01&amp;before=2021-01-01" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/ussd-pulls"
);

const params = {
    "user": "1",
    "campaign": "1",
    "sim": "1",
    "statuses[0]": "Queued",
    "after": "2021-01-01",
    "before": "2021-01-01",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/ussd-pulls';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'query' =&gt; [
            'user' =&gt; '1',
            'campaign' =&gt; '1',
            'sim' =&gt; '1',
            'statuses[0]' =&gt; 'Queued',
            'after' =&gt; '2021-01-01',
            'before' =&gt; '2021-01-01',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/ussd-pulls'
params = {
  'user': '1',
  'campaign': '1',
  'sim': '1',
  'statuses[0]': 'Queued',
  'after': '2021-01-01',
  'before': '2021-01-01',
}
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers, params=params)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-ussd-pulls">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 281,
            &quot;code&quot;: &quot;*3434200#&quot;,
            &quot;response&quot;: null,
            &quot;status&quot;: &quot;Failed&quot;,
            &quot;sim_id&quot;: 23,
            &quot;sent_at&quot;: &quot;2025-09-08T11:12:26.000000Z&quot;,
            &quot;received_at&quot;: null
        },
        {
            &quot;id&quot;: 282,
            &quot;code&quot;: &quot;*32721213140#&quot;,
            &quot;response&quot;: &quot;Aut tempora ut possimus odit nisi sed rem ducimus. Ut voluptatum porro repudiandae doloremque qui in. Id sed voluptatem saepe magnam dicta tempore blanditiis quos.&quot;,
            &quot;status&quot;: &quot;Completed&quot;,
            &quot;sim_id&quot;: 24,
            &quot;sent_at&quot;: &quot;2025-09-08T11:12:26.000000Z&quot;,
            &quot;received_at&quot;: &quot;2025-09-08T12:31:12.000000Z&quot;
        }
    ],
    &quot;links&quot;: {
        &quot;first&quot;: &quot;/?page=1&quot;,
        &quot;last&quot;: &quot;/?page=1&quot;,
        &quot;prev&quot;: null,
        &quot;next&quot;: null
    },
    &quot;meta&quot;: {
        &quot;current_page&quot;: 1,
        &quot;from&quot;: 1,
        &quot;last_page&quot;: 1,
        &quot;links&quot;: [
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;&amp;laquo; Previous&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            },
            {
                &quot;url&quot;: &quot;/?page=1&quot;,
                &quot;label&quot;: &quot;1&quot;,
                &quot;page&quot;: 1,
                &quot;active&quot;: true
            },
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;Next &amp;raquo;&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            }
        ],
        &quot;path&quot;: &quot;/&quot;,
        &quot;per_page&quot;: 100,
        &quot;to&quot;: 2,
        &quot;total&quot;: 2
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-ussd-pulls" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-ussd-pulls"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-ussd-pulls"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-ussd-pulls" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-ussd-pulls">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-ussd-pulls" data-method="GET"
      data-path="api/v1/ussd-pulls"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-ussd-pulls', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-ussd-pulls"
                    onclick="tryItOut('GETapi-v1-ussd-pulls');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-ussd-pulls"
                    onclick="cancelTryOut('GETapi-v1-ussd-pulls');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-ussd-pulls"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/ussd-pulls</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-ussd-pulls"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-ussd-pulls"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-ussd-pulls"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>user</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="user"                data-endpoint="GETapi-v1-ussd-pulls"
               value="1"
               data-component="query">
    <br>
<p>Filter by user id. Only available for admin. Must be at least 1. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>campaign</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="campaign"                data-endpoint="GETapi-v1-ussd-pulls"
               value="1"
               data-component="query">
    <br>
<p>Filter by campaign id. Must be at least 1. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>sim</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="sim"                data-endpoint="GETapi-v1-ussd-pulls"
               value="1"
               data-component="query">
    <br>
<p>Filter by sim id. Must be at least 1. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>statuses</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="statuses[0]"                data-endpoint="GETapi-v1-ussd-pulls"
               data-component="query">
        <input type="text" style="display: none"
               name="statuses[1]"                data-endpoint="GETapi-v1-ussd-pulls"
               data-component="query">
    <br>

Must be one of:
<ul style="list-style-type: square;"><li><code>Pending</code></li> <li><code>Queued</code></li> <li><code>Completed</code></li> <li><code>Failed</code></li></ul>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>after</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="after"                data-endpoint="GETapi-v1-ussd-pulls"
               value="2021-01-01"
               data-component="query">
    <br>
<p>Show USSD pulls that are sent after this date. Must be a valid date in the format <code>Y-m-d</code>. Must be a date before or equal to <code>before</code>. Example: <code>2021-01-01</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>before</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="before"                data-endpoint="GETapi-v1-ussd-pulls"
               value="2021-01-01"
               data-component="query">
    <br>
<p>Show USSD pulls that are sent before this date. Must be a valid date in the format <code>Y-m-d</code>. Must be a date after or equal to <code>after</code>. Example: <code>2021-01-01</code></p>
            </div>
                </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the USSD pull.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>code</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The USSD code.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>response</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The response from the USSD pull.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The status of the USSD pull.</p>
Must be one of:
<ul style="list-style-type: square;"><li><code>Pending</code></li> <li><code>Queued</code></li> <li><code>Completed</code></li> <li><code>Failed</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>sim_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The id of the sim used to send the USSD pull.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>sent_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time when the USSD pull was sent.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>received_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time when the response from the USSD pull was received.</p>
        </div>
                        <h2 id="ussd-pulls-GETapi-v1-ussd-pulls--id-">Retrieve a USSD pull</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retrieves a USSD Pull object.</p>

<span id="example-requests-GETapi-v1-ussd-pulls--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "{{ config("app.url") }}/api/v1/ussd-pulls/1" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/ussd-pulls/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/ussd-pulls/1';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/ussd-pulls/1'
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-ussd-pulls--id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;id&quot;: 283,
    &quot;code&quot;: &quot;*00268*8#&quot;,
    &quot;response&quot;: null,
    &quot;status&quot;: &quot;Failed&quot;,
    &quot;sim_id&quot;: 25,
    &quot;sent_at&quot;: &quot;2025-09-08T11:12:26.000000Z&quot;,
    &quot;received_at&quot;: null
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-ussd-pulls--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-ussd-pulls--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-ussd-pulls--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-ussd-pulls--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-ussd-pulls--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-ussd-pulls--id-" data-method="GET"
      data-path="api/v1/ussd-pulls/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-ussd-pulls--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-ussd-pulls--id-"
                    onclick="tryItOut('GETapi-v1-ussd-pulls--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-ussd-pulls--id-"
                    onclick="cancelTryOut('GETapi-v1-ussd-pulls--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-ussd-pulls--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/ussd-pulls/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-ussd-pulls--id-"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-ussd-pulls--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-ussd-pulls--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-v1-ussd-pulls--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the ussd pull. Example: <code>1</code></p>
            </div>
                    </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The ID of the USSD pull.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>code</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The USSD code.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>response</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The response from the USSD pull.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The status of the USSD pull.</p>
Must be one of:
<ul style="list-style-type: square;"><li><code>Pending</code></li> <li><code>Queued</code></li> <li><code>Completed</code></li> <li><code>Failed</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>sim_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
<br>
<p>The id of the sim used to send the USSD pull.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>sent_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time when the USSD pull was sent.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>received_at</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
<br>
<p>The date and time when the response from the USSD pull was received.</p>
        </div>
                        <h2 id="ussd-pulls-DELETEapi-v1-ussd-pulls--id-">Delete a USSD pull</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Permanently deletes a USSD pull. It cannot be undone.</p>

<span id="example-requests-DELETEapi-v1-ussd-pulls--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "{{ config("app.url") }}/api/v1/ussd-pulls/1" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "{{ config("app.url") }}/api/v1/ussd-pulls/1"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = '{{ config("app.url") }}/api/v1/ussd-pulls/1';
$response = $client-&gt;delete(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {YOUR_AUTH_KEY}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = '{{ config("app.url") }}/api/v1/ussd-pulls/1'
headers = {
  'Authorization': 'Bearer {YOUR_AUTH_KEY}',
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('DELETE', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-DELETEapi-v1-ussd-pulls--id-">
            <blockquote>
            <p>Example response (204, No content):</p>
        </blockquote>
                <pre>
<code>Empty response</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-v1-ussd-pulls--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-v1-ussd-pulls--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-v1-ussd-pulls--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-v1-ussd-pulls--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-v1-ussd-pulls--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-v1-ussd-pulls--id-" data-method="DELETE"
      data-path="api/v1/ussd-pulls/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-v1-ussd-pulls--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-v1-ussd-pulls--id-"
                    onclick="tryItOut('DELETEapi-v1-ussd-pulls--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-v1-ussd-pulls--id-"
                    onclick="cancelTryOut('DELETEapi-v1-ussd-pulls--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-v1-ussd-pulls--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/v1/ussd-pulls/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="DELETEapi-v1-ussd-pulls--id-"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-v1-ussd-pulls--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-v1-ussd-pulls--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="DELETEapi-v1-ussd-pulls--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the ussd pull. Example: <code>1</code></p>
            </div>
                    </form>

            

        
    </div>
    <div class="dark-box">
                    <div class="lang-selector">
                                                        <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                                        <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                                                        <button type="button" class="lang-button" data-language-name="php">php</button>
                                                        <button type="button" class="lang-button" data-language-name="python">python</button>
                            </div>
            </div>
</div>
</body>
</html>
