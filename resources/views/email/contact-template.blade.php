
    <h2>Hello Admin,</h2>
    <p>You received an email from : <b>{{ $name }}</b></p>
    <p>Here are the details:</p>
    <b>Name:</b> {{ $name }} <br>
    <b>Email:</b> {{ $email }} <br>
    <b>Subject:</b> {{ $subject }} <br>
    <b>Message:</b> <br/> {!! nl2br($body) !!}
    <br/>

    -  Thank You, {{ env('APP_NAME') }}
