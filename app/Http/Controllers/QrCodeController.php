<?php
/*
 * Copyright © 2018-2025 RBSoft (Ravi Patel). All rights reserved.
 *
 * Author: Ravi Patel
 * Website: https://rbsoft.org/downloads/sms-gateway
 *
 * This software is licensed, not sold. Buyers are granted a limited, non-transferable license
 * to use this software exclusively on a single domain, subdomain, or computer. Usage on
 * multiple domains, subdomains, or computers requires the purchase of additional licenses.
 *
 * Redistribution, resale, sublicensing, or sharing of the source code, in whole or in part,
 * is strictly prohibited. Modification (except for personal use by the licensee), reverse engineering,
 * or creating derivative works based on this software is strictly prohibited.
 *
 * Unauthorized use, reproduction, or distribution of this software may result in severe civil
 * and criminal penalties and will be prosecuted to the fullest extent of the law.
 *
 * For licensing inquiries or support, please visit https://support.rbsoft.org.
 */

namespace App\Http\Controllers;

use App\Facades\JwtAuth;
use App\Models\Setting;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class QrCodeController extends Controller
{
    public function create(): Response
    {
        $renderer = new ImageRenderer(new RendererStyle(400), new SvgImageBackEnd());
        $writer = new Writer($renderer);
        $ttl = Setting::retrieve('auth.qr_code.lifespan', Auth::id(), config('auth.qr_code.lifespan'));
        $content = json_encode(['token' => JwtAuth::createToken(Auth::user(), $ttl), 'server_url' => config('app.url')]);
        $qrCode = $writer->writeString(base64_encode($content));
        return new Response($qrCode, 200, ['Content-Type' => 'image/svg+xml']);
    }
}
