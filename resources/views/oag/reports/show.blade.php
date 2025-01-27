<!DOCTYPE html>
<html>
<head>
    <title>Report Details</title>
</head>
<body>
    <h1>Report Details</h1>
    
    @if($results->isEmpty())
        <p>No results found.</p>
    @else
        <table border="1">
            <thead>
                <tr>
                    @foreach(array_keys($results->first()) as $key)
                        <th>{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($results as $result)
                    <tr>
                        @foreach($result as $value)
                            <td>{{ $value }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
