<!-- Modal for Contact Details -->
<div class="modal fade" id="contactModal{{ $user->id }}" tabindex="-1" role="dialog"
    aria-labelledby="contactModalLabel{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contactModalLabel{{ $user->id }}">Contact Details - {{ $user->name }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> {{ $user->name }}</p>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Phone:</strong> {{ $user->phone }}</p>
                        <p><strong>Designation:</strong> {{ $user->designation }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Organization:</strong> {{ $user->organization }}</p>
                        <p><strong>Address:</strong> {{ $user->address }}</p>
                        <p><strong>Created At:</strong> {{ $user->created_at->format('Y-m-d H:i') }}</p>
                        <p><strong>Updated At:</strong> {{ $user->updated_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Created By:</strong> {{ $user->created_by }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Updated By:</strong> {{ $user->updated_by }}</p>
                    </div>
                </div>

                <div class="mt-4">
                    <h6><strong>Contact Types:</strong></h6>
                    <ul class="list-group">
                        @foreach ($user->contactTypes as $contactType)
                            <li class="list-group-item">{{ $contactType->name }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <!-- Print Button -->
                <button type="button" class="btn btn-info"
                    onclick="printContactDetails({{ $user->id }})">Print</button>
            </div>
        </div>
    </div>
</div>

<script>
    function printContactDetails(userId) {
        // Create a new window
        var printWindow = window.open('', '', 'height=800,width=1200');

        // Get the content of the modal by user ID and place it inside the new window
        var contactDetails = document.getElementById('contactModal' + userId).querySelector('.modal-body').innerHTML;

        // Build the HTML structure for printing
        printWindow.document.write('<html><head><title>Contact Details</title>');
        printWindow.document.write(
            '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">'
        );
        printWindow.document.write('</head><body>');
        printWindow.document.write('<div class="container">');
        printWindow.document.write('<h4>Contact Details - ' + document.getElementById('contactModalLabel' + userId)
            .innerText + '</h4>');
        printWindow.document.write(contactDetails);
        printWindow.document.write('</div>');
        printWindow.document.write('</body></html>');

        // Wait for the content to load and then trigger the print dialog
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
    }
</script>
