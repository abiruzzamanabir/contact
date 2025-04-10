@extends('admin.layouts.app')

@section('main')
    <div class="container">
        <h3>Activity Logs for {{ $contact->name }}</h3>

        @if ($logs->isEmpty())
            <p>No logs available.</p>
        @else
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Performed By</th>
                        <th>Performed At</th>
                        <th>Changed Data</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td><strong>{{ $log->performed_by }}</strong></td>
                            <td>{{ $log->performed_at->diffForHumans() }}</td> <!-- Human-readable time -->
                            <td>
                                @php
                                    // Decode the JSON into an associative array
                                    $changedData = json_decode($log->changed_data, true);
                                @endphp

                                @if (is_array($changedData))
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Field</th>
                                                <th>Old Value</th>
                                                <th>New Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($changedData as $key => $value)
                                                <tr>
                                                    <td>{{ $key }}</td>
                                                    <td>
                                                        @if (is_array($value) && isset($value['old']))
                                                            {{ $value['old'] }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (is_array($value) && isset($value['new']))
                                                            {{ $value['new'] }}
                                                        @elseif (!is_array($value))
                                                            {{ $value }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <pre class="bg-light p-2 rounded">{{ json_encode($changedData, JSON_PRETTY_PRINT) }}</pre>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <a href="{{ route('contact.index') }}" class="btn btn-secondary mt-3">Back</a>
    </div>
@endsection
