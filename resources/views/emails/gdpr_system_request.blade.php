<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>GDPR Portalen</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body style="margin: 0; padding: 0;">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td style="padding: 10px 0 30px 0;">
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="800" style="border: 1px solid #cccccc; border-collapse: collapse;">
                <tr>
                    <td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td style="color: #153643; font-family: Arial, sans-serif; font-size: 24px;">
                                    <b>Begäran om ett GDPR utdrag id: {{ $case['case_id'] }}</b>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 20px 0 0px 0; color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;">
                                    Datum:
                                </td>
                                <td style="padding: 20px 0 0px 0; color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;">
                                    Handläggare(användarid):
                                </td>
                            </tr>
                            <tr>
                                <td style="color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;">
                                    <strong>{{$case['updated_at']}}</strong>
                                </td>
                                <td style="color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;">
                                    <strong>{{$case['gdpr_userid']}}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 20px 0 0px 0; color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;">
                                    Ärende:
                                </td>
                            </tr>
                            <tr>
                                <td style="color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;">

                                    Ett GDPR utdrag har begärts för <strong>{{$case['request_pnr']}} {{$case['request_email']}} {{$case['request_uid']}} </strong>

                                </td>
                            </tr>
                            <tr></tr>
                            <tr>
                                <td style="color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;">
                                    <strong>Använd nedanstående länk för att ladda upp utdraget:</strong>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 20px 0 0px 0; color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;">
                                    Länk:

                                    {{ $link }}
                                </td>
                            </tr>

                        </table>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#41D873" style="padding: 30px 30px 30px 30px;">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td style="color: #ffffff; font-family: Arial, sans-serif; font-size: 14px;" width="75%">
                                    DSV GDPR Portal
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
