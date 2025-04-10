@php
    $summary = "{$contact->name} is a {$contact->designation} at {$contact->organization}. ";
    $summary .= "You can reach them at {$contact->email} or call at {$contact->phone}. ";
    $summary .= "They are based in {$contact->address}.";

    $contactDetails = [
        'Name' => $contact->name,
        'Email' => $contact->email,
        'Phone' => $contact->phone,
        'Designation' => $contact->designation,
        'Organization' => $contact->organization,
        'Address' => $contact->address,
    ];

    // Convert contact details to a string
    $contactDetailsString = '';
    foreach ($contactDetails as $key => $value) {
        $contactDetailsString .= "{$key}: {$value}\n";
    }

    // URL encode the string for QR code
    $encodedContactDetails = urlencode($contactDetailsString);
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Details</title>

    <!-- Bootstrap 4.6.2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-Fv+F4m7Cey+vXc37gUg+dr7iBvXGzO+mK4cihZB7IhtJoWWZyZBi3r0YAEqv8a0XKcz27mT8uhw2dzx1IUz9xg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f7f9fc;
        }

        .container {
            max-width: 900px;
            margin: 20px auto;
            background: #fff;
            padding: 30px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            color: #333;
            font-weight: 600;
        }

        .table {
            margin-top: 20px;
            font-size: 1.1rem;
        }

        th {
            background-color: #f1f1f1;
            font-weight: 500;
            text-align: center;
        }

        td {
            vertical-align: middle;
        }

        .btn-share {
            margin-left: 10px;
        }

        .alert-summary {
            background-color: #f0f4f7;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .alert-info {
            background-color: #d1ecf1;
            padding: 10px;
            border-radius: 5px;
        }

        .btn {
            border-radius: 25px;
            font-size: 1rem;
        }

        .btn:hover {
            opacity: 0.8;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-info {
            background-color: #17a2b8;
            border: none;
        }

        .btn-group .dropdown-menu {
            min-width: 200px;
        }

        .dropdown-item {
            padding: 10px;
            font-size: 1rem;
        }

        .alert-summary {
            background-color: #f8f9fa;
            color: #333;
        }

        .share-buttons i {
            margin-right: 10px;
        }

        .qr-code-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .qr-code-container img {
            max-width: 150px;
            margin-right: 20px;
        }

        @media print {

            .btn,
            .dropdown {
                display: none !important;
            }

            .container {
                box-shadow: none;
                padding: 10px;
                max-width: 100%;
            }

            h1 {
                font-size: 22px;
            }

            @page {
                size: auto;
                margin: 10mm;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Contact Details - {{ $contact->name }}</h1>

        <table class="table table-bordered">
            <tr>
                <th>Name</th>
                <td>{{ $contact->name }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $contact->email }}</td>
            </tr>
            <tr>
                <th>Phone</th>
                <td>{{ $contact->phone }}</td>
            </tr>
            <tr>
                <th>Designation</th>
                <td>{{ $contact->designation }}</td>
            </tr>
            <tr>
                <th>Organization</th>
                <td>{{ $contact->organization }}</td>
            </tr>
            <tr>
                <th>Address</th>
                <td>{{ $contact->address }}</td>
            </tr>
        </table>

        @if ($summary)
            <div class="alert alert-info mt-4">
                <strong>Smart Summary:</strong><br>
                {{ $summary }}
            </div>
        @endif

        <div class="qr-code-container mt-4">
            <div class="w-100">
                <iframe src="https://maps.google.com/maps?q={{ urlencode($contact->address) }}&output=embed"
                    width="100%" height="500" frameborder="0"></iframe>
            </div>
            <div class="w-100 d-flex justify-content-center mt-4">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(request()->fullUrl()) }}"
                    alt="QR Code" />
            </div>
            <div class="w-100 d-flex justify-content-center mt-4">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $encodedContactDetails }}"
                    alt="QR Code" />
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <button class="btn btn-info" onclick="window.print()">
                <i class="fas fa-print"></i> Print
            </button>

            <div class="btn-group ml-3">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                    <i class="fas fa-share-alt"></i> Share
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="#" onclick="shareWhatsApp()"><i
                            class="fab fa-whatsapp text-success"></i> WhatsApp</a>
                    <a class="dropdown-item" href="#" onclick="shareTelegram()"><i
                            class="fab fa-telegram text-primary"></i> Telegram</a>
                    <a class="dropdown-item" href="#" onclick="shareFacebook()"><i
                            class="fab fa-facebook text-primary"></i> Facebook</a>
                    <a class="dropdown-item" href="#" onclick="shareTwitter()"><i
                            class="fab fa-twitter text-info"></i> Twitter</a>
                    <a class="dropdown-item" href="#" onclick="shareLinkedIn()"><i
                            class="fab fa-linkedin text-info"></i> LinkedIn</a>
                    <a class="dropdown-item" href="#" onclick="shareEmail()"><i
                            class="fas fa-envelope text-warning"></i> Email</a>
                    <a class="dropdown-item" href="#" onclick="copyToClipboard()"><i class="fas fa-copy"></i> Copy
                        to Clipboard</a>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery and Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous">
    </script>

    <!-- Share Script -->
    <script>
        const contactDetails =
            `Name: {{ $contact->name }}\nEmail: {{ $contact->email }}\nPhone: {{ $contact->phone }}\nDesignation: {{ $contact->designation }}\nOrganization: {{ $contact->organization }}\nAddress: {{ $contact->address }}`;

        const mapUrl = `https://www.google.com/maps?q={{ urlencode($contact->address) }}`;

        const whatsappMessage = `Contact Details:
${contactDetails}

Map Location: ${mapUrl}`;



        function shareWhatsApp() {
            const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(whatsappMessage)}`;

            window.open(whatsappUrl, '_blank');
        }

        function shareTelegram() {
            const url =
                `https://t.me/share/url?url=${encodeURIComponent(window.location.href)}&text=${encodeURIComponent(contactDetails)}`;
            window.open(url, '_blank');
        }

        function shareFacebook() {
            const url =
                `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}&quote=${encodeURIComponent(contactDetails)}`;
            window.open(url, '_blank');
        }

        function shareTwitter() {
            const url = `https://twitter.com/intent/tweet?text=${encodeURIComponent(contactDetails)}`;
            window.open(url, '_blank');
        }

        function shareLinkedIn() {
            const url = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(window.location.href)}`;
            window.open(url, '_blank');
        }

        function shareEmail() {
            const subject = "Contact Details";
            const body = contactDetails;
            window.location.href = `mailto:?subject=${subject}&body=${encodeURIComponent(body)}`;
        }

        function copyToClipboard() {
            navigator.clipboard.writeText(contactDetails).then(() => {
                alert("Contact details copied to clipboard!");
            }).catch(err => {
                alert("Error copying to clipboard: " + err);
            });
        }
    </script>

</body>

</html>
