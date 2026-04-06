<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Job application</title>
</head>
<body style="font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; line-height: 1.5; color: #1f2937;">
  <p style="margin: 0 0 16px;">New job application via the website careers form.</p>
  <table cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
    <tr>
      <td style="padding: 6px 12px 6px 0; font-weight: 600;">Position</td>
      <td style="padding: 6px 0;">{{ $position }}</td>
    </tr>
    <tr>
      <td style="padding: 6px 12px 6px 0; font-weight: 600;">Name</td>
      <td style="padding: 6px 0;">{{ $name }}</td>
    </tr>
    <tr>
      <td style="padding: 6px 12px 6px 0; font-weight: 600;">Email</td>
      <td style="padding: 6px 0;"><a href="mailto:{{ $email }}">{{ $email }}</a></td>
    </tr>
    @if(! empty($phone))
      <tr>
        <td style="padding: 6px 12px 6px 0; font-weight: 600;">Phone</td>
        <td style="padding: 6px 0;">{{ $phone }}</td>
      </tr>
    @endif
  </table>
  <p style="margin: 24px 0 0; font-size: 14px; color: #6b7280;">The CV is attached to this message.</p>
</body>
</html>
