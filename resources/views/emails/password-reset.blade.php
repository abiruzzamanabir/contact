<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Password Change Confirmation</title>
    <style type="text/css">
        body {
            background-image: linear-gradient(rgba(255, 255, 255, 0.9),
                    rgb(255, 255, 255, 0.9)),
                url('https://png.pngtree.com/thumb_back/fh260/background/20220629/pngtree-letter-envelope-illustration-image-mail-message-floated-sent-image_1416038.jpg');
            margin: 0;
        }

        table {
            border-spacing: 0;
            padding: 20px;
        }

        td {
            padding: 0;
        }

        .main-table {
            max-width: 600px;
            background-image: linear-gradient(rgba(255, 255, 255, 0.5),
                    rgb(255, 255, 255, 0.5)),
                url('https://png.pngtree.com/thumb_back/fh260/background/20220629/pngtree-letter-envelope-illustration-image-mail-message-floated-sent-image_1416038.jpg');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center center;
            padding: 10px;
            border-spacing: 0;
            margin: 0 auto;
        }

        .container {
            background-color: #ffffff;
            background-image: linear-gradient(rgba(255, 255, 255, 0.9),
                    rgb(255, 255, 255, 0.9)),
                url('https://png.pngtree.com/thumb_back/fh260/background/20220629/pngtree-letter-envelope-illustration-image-mail-message-floated-sent-image_1416038.jpg');
            background-repeat: no-repeat;
            background-size: contain;
            background-position: center center;
        }

        .logo {
            text-align: center;
            font-size: 0;
        }

        .logo .column {
            width: 100%;
            max-width: 300px;
            display: inline-block;
            vertical-align: middle;
        }

        .logo .column a {
            text-decoration: none;
            vertical-align: middle;
            color: tomato;
        }

        .logo .column strong {
            vertical-align: middle;
            color: tomato;
        }

        .button {
            display: block;
            padding: 7px 20px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            color: #000 !important;
            background-image: linear-gradient(rgba(255, 255, 255, 0.5),
                    rgb(255, 255, 255, 0.5)),
                url('https://png.pngtree.com/thumb_back/fh260/background/20220629/pngtree-letter-envelope-illustration-image-mail-message-floated-sent-image_1416038.jpg');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center center;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            margin: 20px auto;
            width: 30%;
            text-align: center;
        }

        .button:hover {
            background-image: linear-gradient(rgba(122, 122, 122, 0.7),
                    rgba(122, 122, 122, 0.7)),
                url('https://png.pngtree.com/thumb_back/fh260/background/20220629/pngtree-letter-envelope-illustration-image-mail-message-floated-sent-image_1416038.jpg');
            color: #fff !important;
        }

        .footer {
            margin: 100px auto 0px;
            color: #fff;
            padding: 10px;
            text-align: center;
            font-size: 14px;
            box-shadow: 0 2px 8px rgba(71, 71, 71, 0.5);
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                url('https://png.pngtree.com/thumb_back/fh260/background/20220629/pngtree-letter-envelope-illustration-image-mail-message-floated-sent-image_1416038.jpg');
            background-size: cover;
            background-position: center;
        }

        .footer a {
            color: #999999;
            text-decoration: none;
        }

        .footer p {
            text-align: center;
            margin: 20px 0px 5px;
        }

        .footer a:hover {
            color: #555555;
        }

        .fa {
            padding: 7px;
            font-size: 13px;
            width: 20px;
            text-align: center;
            text-decoration: none;
            margin: 10px 5px;
            color: white !important;
        }

        .fa:hover {
            opacity: 0.7;
        }

        .footer img {
            width: 36px !important;
            border: 0px !important;
            display: inline !important;
        }

        @media only screen and (max-width: 600px) {
            img {
                display: block;
                margin-left: auto;
                margin-right: auto;
                width: 50%;
            }

            .button {
                display: block;
                padding: 10px 15px;
                font-size: 14px;
                margin: 20px auto;
                width: 40%;
            }

            .footer a {
                color: #999999;
                text-decoration: underline;
                border: none;
            }

            .fa {
                padding: 0px;
                font-size: 12px;
                width: 20px;
                text-align: center;
                text-decoration: none;
                margin: 0px 5px;
                color: white !important;
            }
        }
    </style>
</head>

<body>
    <center class="main-table">
        <div class="container">
            <table width="100%">
                <tr>
                    <td style="padding: 20px 0">
                        <p>Hello <strong>{{ $name }}</strong>,</p>

                        <p>We received a request to reset your password. To continue, please use the link below:</p>

                        <p>
                            <a href="{{ url('/reset-password/' . $token . '/' . $email) }}">
                                Reset Your Password
                            </a>
                        </p>

                        <p>If you did not request this change, you can safely ignore this email. Your account will
                            remain secure.</p>

                        <p>Thank you,<br />
                            <strong>{{ Config::get('app.name') }} Support Team</strong>
                        </p>

                        <hr>

                        <p>
                            Having trouble with the button? You can also copy and paste the link below into your web
                            browser:<br />
                            <a href="{{ url('/reset-password/' . $token . '/' . $email) }}">
                                {{ url('/reset-password/' . $token . '/' . $email) }}
                            </a>
                        </p>


                    </td>
                </tr>
            </table>
        </div>
    </center>
</body>

</html>
