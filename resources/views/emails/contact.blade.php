<!doctype html>
<html lang="en" style="margin:0;padding:0;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $subjectText }}</title>
    <style>
        body, table, td, a { margin:0; padding:0; border-collapse: collapse; }
        img { border:0; height:auto; line-height:100%; outline:none; text-decoration:none; }
        a { text-decoration:none; }
        body { height:100% !important; width:100% !important; font-family:Arial, Helvetica, sans-serif; background:#f8f8f8; }

        @media (max-width: 600px) {
            .container { width:100% !important; }
            .stack { display:block !important; width:100% !important; }
            .p-32 { padding:20px !important; }
            .text-center-sm { text-align:center !important; }
            .btn { display:block !important; width:100% !important; text-align:center !important; }
        }
    </style>
</head>
<body style="background-color:#f8f8f8; margin:0; padding:0;">

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f8f8f8;">
    <tr>
        <td align="center" style="padding:20px;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" class="container" style="max-width:600px;background-color:#ffffff;border-radius:12px;overflow:hidden;border:none;">
                <tr>
                    <td style="padding:32px 24px 16px 24px;" class="p-32" align="left">
                        <p style="margin:12px 0;font-size:15px;line-height:1.6;color:#444;">
                            <strong>Name:</strong> {{ $first_name }} {{ $last_name }}<br>
                            <strong>Email:</strong> {{ $email }}<br>
                            <strong>Phone:</strong> {{ $phone }}<br>
                            @if($product)
                            <strong>Product Requested:</strong> {{ $product->title }}<br>
                            @endif
                            <strong>Message:</strong><br>
                            {!! nl2br(e($contactMessage)) !!}
                        </p>
                        <p style="margin-top:20px; color:#444;">{!! $mailFooter !!}</p>
                    </td>
                </tr>

                <tr>
                    <td style="background-color:#f8f8f8;padding:20px;font-size:12px;color:#555;text-align:center;">
                        <p style="margin:0;">&copy; {{ date('Y') }} Mento Software. All rights reserved.</p>
                        <p style="margin:4px 0 0 0;"><a href="https://www.mentosoftware.co.uk/" style="color:#FF6D33;text-decoration:none;">Visit Website</a></p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>
</html>