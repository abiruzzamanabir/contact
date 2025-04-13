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

    // vCard
    $vcard = "BEGIN:VCARD\nVERSION:3.0\n";
    $vcard .= "FN:{$contact->name}\n";
    $vcard .= "ORG:{$contact->organization}\n";
    $vcard .= "TITLE:{$contact->designation}\n";
    $vcard .= "TEL:{$contact->phone}\n";
    $vcard .= "EMAIL:{$contact->email}\n";
    $vcard .= "ADR:{$contact->address}\n";
    $vcard .= 'END:VCARD';
    $vcardBase64 = base64_encode($vcard);

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

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-Fv+F4m7Cey+vXc37gUg+dr7iBvXGzO+mK4cihZB7IhtJoWWZyZBi3r0YAEqv8a0XKcz27mT8uhw2dzx1IUz9xg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f7f9fc;
            color: #333;
            transition: background-color 0.3s, color 0.3s;
        }

        .container {
            max-width: 900px;
            margin: 20px auto;
            background: #fff;
            padding: 30px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            transition: background-color 0.3s, color 0.3s;
        }

        h1,
        th,
        td,
        p {
            color: #333;
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

        .btn-success {
            background-color: #28a745;
            border: none;
        }

        .btn-dark {
            background-color: #343a40;
            border: none;
        }

        .dropdown-item {
            padding: 10px;
            font-size: 1rem;
        }

        .qr-code-container img {
            max-width: 150px;
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

        /* Dark mode styles */
        body.dark-mode {
            background-color: #343a40;
            color: #fff;
        }

        body.dark-mode .container {
            background-color: #495057;
            color: #fff;
        }

        body.dark-mode h1,
        body.dark-mode th,
        body.dark-mode td,
        body.dark-mode p,
        body.dark-mode .dropdown-item {
            color: #fff;
        }

        body.dark-mode .table th {
            background-color: #6c757d;
        }

        body.dark-mode .table td {
            background-color: #495057;
        }

        body.dark-mode .btn {
            border-radius: 25px;
            font-size: 1rem;
        }

        body.dark-mode .btn-info {
            background-color: #17a2b8;
            border: none;
        }

        body.dark-mode .btn-primary {
            background-color: #007bff;
            border: none;
        }

        body.dark-mode .btn-dark {
            background-color: #343a40;
            border: none;
        }

        body.dark-mode .qr-code-container .text-muted {
            color: #fff !important;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Contact Details - {{ $contact->name }}</h1>

        <table class="table table-bordered">
            @foreach ($contactDetails as $label => $value)
                <tr>
                    <th>{{ $label }}</th>
                    <td>{{ $value }}</td>
                </tr>
            @endforeach
        </table>

        @if ($summary)
            <div class="alert alert-info mt-4">
                <strong>Smart Summary:</strong><br>
                {{ $summary }}
            </div>
        @endif

        <div class="qr-code-container mt-4 text-center">
            <iframe src="https://maps.google.com/maps?q={{ urlencode($contact->address) }}&output=embed" width="100%"
                height="400" frameborder="0"></iframe>

            <div class="d-flex flex-wrap justify-content-center mt-4">
                <div class="mx-2">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(request()->fullUrl()) }}"
                        alt="Page QR Code" />
                    <p class="text-muted small mt-2">This Page</p>
                </div>
                <div class="mx-2">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $encodedContactDetails }}"
                        alt="Contact QR Code" />
                    <p class="text-muted small mt-2">Contact Info</p>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end flex-wrap mt-4">
            <button class="btn btn-info mb-2 mr-2" onclick="window.print()">
                <i class="fas fa-print"></i> Print
            </button>

            <a href="data:text/vcard;base64,{{ $vcardBase64 }}" download="{{ $contact->name }}.vcf"
                class="btn btn-success mb-2 mr-2">
                <i class="fas fa-download"></i> Download vCard
            </a>

            <button class="btn btn-secondary mb-2 mr-2" onclick="copyMapUrl()">
                <i class="fas fa-map-marker-alt"></i> Copy Map URL
            </button>

            <button class="btn btn-dark mb-2 mr-2" onclick="toggleDarkMode()">
                <i class="fas fa-moon"></i> Toggle Dark Mode
            </button>

            <div class="btn-group mb-2">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                    <i class="fas fa-share-alt"></i> Share
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="#" onclick="shareWhatsApp()"><i
                            class="fab fa-whatsapp text-success"></i> WhatsApp</a>
                    <a class="dropdown-item" href="#" onclick="shareEmail()"><i
                            class="fas fa-envelope text-warning"></i> Email</a>
                    <a class="dropdown-item" href="#" onclick="shareSMS()"><i class="fas fa-sms text-primary"></i>
                        SMS</a>
                    <a class="dropdown-item" href="#" onclick="copyToClipboard()"><i class="fas fa-copy"></i> Copy
                        to Clipboard</a>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous">
    </script>

    <script>
        const contactDetails =
            `Name: {{ $contact->name }}\nEmail: {{ $contact->email }}\nPhone: {{ $contact->phone }}\nDesignation: {{ $contact->designation }}\nOrganization: {{ $contact->organization }}\nAddress: {{ $contact->address }}`;

        const mapUrl = `https://www.google.com/maps?q={{ urlencode($contact->address) }}`;

        const whatsappMessage = `Contact Details:\n${contactDetails}\n\nMap Location: ${mapUrl}`;

        function shareWhatsApp() {
            const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(whatsappMessage)}`;
            window.open(whatsappUrl, '_blank');
        }

        function shareEmail() {
            const subject = "Here's a Contact You Might Need";
            const body = `Hi,\n\nHere's a contact that might be useful:\n\n${contactDetails}\n\nMap: ${mapUrl}`;
            window.location.href = `mailto:?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
        }

        function copyToClipboard() {
            navigator.clipboard.writeText(contactDetails).then(() => {
                alert("Contact details copied to clipboard!");
            }).catch(err => {
                alert("Error copying to clipboard: " + err);
            });
        }

        function copyMapUrl() {
            navigator.clipboard.writeText(mapUrl).then(() => {
                alert("Map URL copied to clipboard!");
            }).catch(err => {
                alert("Failed to copy Map URL: " + err);
            });
        }

        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
        }

        function shareSMS() {
            const smsBody =
                `Contact Info:\n{{ $contact->name }}\n{{ $contact->designation }} at {{ $contact->organization }}\nPhone: {{ $contact->phone }}\nEmail: {{ $contact->email }}\nAddress: {{ $contact->address }}`;
            const smsUrl = `sms:?&body=${encodeURIComponent(smsBody)}`;
            window.location.href = smsUrl;
        }
    </script>
</body>

</html>
