<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>GDPR Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body style="margin: 0; padding: 0;">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td style="padding: 10px 0 30px 0;">
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border: 1px solid #cccccc; border-collapse: collapse;">
                <tr>
                    <td align="center"  style="padding: 40px 0 30px 0; color: #153643; font-size: 28px; font-weight: bold; font-family: Arial, sans-serif;">
                        <img src="{{ asset('images/su_logo_en.gif') }}" alt="DSV Stockholms universitet" width="300" height="230" style="display: block;" />
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td style="color: #153643; font-family: Arial, sans-serif; font-size: 24px;">
                                    <b>Test message from the GDPR Portal</b>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 20px 0 30px 0; color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;">
                                    Your request <strong>ID#{{$details['case']}}</strong> has now been processed and is ready for downloading.
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                        <tr>
                                            <td width="260" valign="top">
                                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                    <tr>
                                                        <td>
                                                            <img src="{{ asset('images/su_logo_en.gif') }}" alt="GDPR Dashboard" width="100%" height="140" style="display: block;" />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 25px 0 0 0; color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;">
                                                            <a href="{{ $details['url'] }}">Dashboard</a>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td style="font-size: 0; line-height: 0;" width="20">
                                                &nbsp;
                                            </td>
                                            <td width="260" valign="top">
                                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                    <tr>
                                                        <td>
                                                            <img src="{{ asset('images/su_logo_en.gif') }}" alt="GDPR Statisticts" width="100%" height="140" style="display: block;" />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 25px 0 0 0; color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;">
                                                            Stats
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
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
                                <td align="right" width="25%">
                                    <table border="0" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="font-family: Arial, sans-serif; font-size: 12px; font-weight: bold;">
                                                <a href="http://su.se/" style="color: #ffffff;">
                                                    <img src="{{ asset('images/su_logo_responsive_en.png') }}" alt="Stockholms universitet" width="38" height="38" style="display: block;" border="0" />
                                                </a>
                                            </td>
                                            <td style="font-size: 0; line-height: 0;" width="20">&nbsp;</td>
                                            <td style="font-family: Arial, sans-serif; font-size: 12px; font-weight: bold;">
                                                <a href="http://dsv.su.se" style="color: #ffffff;">
                                                    <img src="{{ asset('images/dsv_logo_en.png') }}" alt="DSV Stockholms universitet" width="38" height="38" style="display: block;" border="0" />
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
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




