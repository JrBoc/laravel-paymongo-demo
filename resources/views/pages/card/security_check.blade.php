<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
        }

        iframe {
            display: block;
            background: #000;
            border: none;
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
    <iframe src="{{ $cardPayment->payment_attach_response['next_action']['redirect']['url'] }}" frameborder="0"></iframe>
    <form id="frm_update" action="{{ route('card-payment.update', $cardPayment) }}" method="POST" style="display: none">
        @csrf
        @method('put')
    </form>
    <script>
        window.addEventListener('message', function (e) {
            document.getElementById('frm_update').submit();
        });
    </script>
</body>
</html>
