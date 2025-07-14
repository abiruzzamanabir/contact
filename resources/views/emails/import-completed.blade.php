<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Import Completed</title>
    <style type="text/css">
        body {
            background-image: linear-gradient(rgba(255, 255, 255, 0.9), rgb(255, 255, 255, 0.9)),
                url('https://png.pngtree.com/thumb_back/fh260/background/20220629/pngtree-letter-envelope-illustration-image-mail-message-floated-sent-image_1416038.jpg');
            margin: 0;
        }

        table {
            border-spacing: 0;
            padding: 20px;
        }

        .main-table {
            max-width: 600px;
            background-image: linear-gradient(rgba(255, 255, 255, 0.5), rgb(255, 255, 255, 0.5)),
                url('https://png.pngtree.com/thumb_back/fh260/background/20220629/pngtree-letter-envelope-illustration-image-mail-message-floated-sent-image_1416038.jpg');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center center;
            padding: 10px;
            margin: 0 auto;
        }

        .container {
            background-color: #ffffff;
            background-image: linear-gradient(rgba(255, 255, 255, 0.9), rgb(255, 255, 255, 0.9)),
                url('https://png.pngtree.com/thumb_back/fh260/background/20220629/pngtree-letter-envelope-illustration-image-mail-message-floated-sent-image_1416038.jpg');
            background-repeat: no-repeat;
            background-size: contain;
            background-position: center center;
        }

        .button {
            display: block;
            padding: 7px 20px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            color: #000 !important;
            background-color: #f0f0f0;
            border-radius: 5px;
            margin: 20px auto;
            width: 50%;
            text-align: center;
        }

        .button:hover {
            background-color: #e0e0e0;
        }

        .footer {
            margin: 100px auto 0px;
            color: #fff;
            padding: 10px;
            text-align: center;
            font-size: 14px;
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                url('https://png.pngtree.com/thumb_back/fh260/background/20220629/pngtree-letter-envelope-illustration-image-mail-message-floated-sent-image_1416038.jpg');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>

<body>
    <center class="main-table">
        <div class="container">
            <table width="100%">
                <tr>
                    <td style="padding: 20px 0">
                        <h2 style="color: #e84118;">Your Contact Import is Complete!</h2>

                        <p>Hello {{ $user->name ?? 'User' }},</p>

                        <p>We’ve successfully finished importing your contact data.</p>

                        <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($time)->format('F j, Y \a\t h:i A') }}
                            (UTC+6)</p>
                        <p><strong>IP Address:</strong> {{ $ip }}</p>

                        <p>
                            <a href="{{ url('/contacts') }}" class="button">
                                View Imported Contacts
                            </a>
                        </p>

                        <hr>

                        <p style="font-size: 12px; color: #888;">
                            This is an automated message. Do not reply.
                            <br>
                            If you didn’t trigger this import, contact support immediately.
                        </p>
                    </td>
                </tr>
            </table>
        </div>
    </center>
</body>

</html>
