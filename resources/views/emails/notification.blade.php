<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 680px; margin: auto; padding: 20px; border: 1px solid #eee; }
        .header { text-align:center; margin-bottom:20px; }
        .content { font-size:16px; color:#222; }
        .footer { margin-top:30px; color:#666; font-size:13px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/logo.png') }}" alt="Mobile Mandu" style="max-width:160px; height:auto;">
        </div>
        <div class="content">
            <p>{{ $message }}</p>

            @if(!empty($product_slug))
                <p>View product: <a href="https://mobilemandu.com/products/{{ $product_slug }}">Open product</a></p>
            @endif

            @if(!empty($campaign_slug))
                <p>View campaign: <a href="https://mobilemandu.com/campaigns/{{ $campaign_slug }}">Open campaign</a></p>
            @endif
        </div>

        <div class="footer">
            <p>Thanks,<br>Mobile Mandu Team</p>
            <p style="font-size:12px; color:#999;">If you no longer want to receive these emails, update your notification preferences in your account.</p>
        </div>
    </div>
</body>
</html>
