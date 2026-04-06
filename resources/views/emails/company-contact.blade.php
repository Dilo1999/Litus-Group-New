<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Company enquiry</title>
</head>
<body style="font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; line-height: 1.5; color: #1f2937;">
  <p style="margin: 0 0 16px;">
    New enquiry submitted via the website{{ !empty($companyName) ? ' for '.$companyName : '' }}.
  </p>

  <table cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
    <tr>
      <td style="padding: 6px 12px 6px 0; font-weight: 600;">Name</td>
      <td style="padding: 6px 0;">{{ $senderName }}</td>
    </tr>
    <tr>
      <td style="padding: 6px 12px 6px 0; font-weight: 600;">Email</td>
      <td style="padding: 6px 0;"><a href="mailto:{{ $senderEmail }}">{{ $senderEmail }}</a></td>
    </tr>
    @if(!empty($senderPhone))
      <tr>
        <td style="padding: 6px 12px 6px 0; font-weight: 600;">Phone</td>
        <td style="padding: 6px 0;">{{ $senderPhone }}</td>
      </tr>
    @endif
  </table>

  <div style="margin-top: 16px; padding: 12px 14px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px;">
    {!! nl2br(e($messageBody)) !!}
  </div>
</body>
</html>

