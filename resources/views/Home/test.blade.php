@extends('layouts.test-master')
@section('content')

<form id="form">

    <!-- Email -->
    <input type="text" id="labelLogin" class="form-control" data-field="login">
    <label for="labelLogin">Email</label>
    <div class="valid-message"></div>

    <!-- Password -->
    <input type="password" id="labelPassword" class="form-control" data-field="password">
    <label for="labelSurname">Password</label>
    <div class="valid-message"></div>

    <!-- Submit Button -->
    <button type="submit">Submit</button>

</form>
<script>
    var form = $('#form').formValid({
        fields: {
            "login": {
                "required": true,
                "tests": [
                    {
                        "type": "null",
                        "message": "Not entered login"
                    },
                    {
                        "type": "email",
                        "message": "Your email is incorrect"
                    }
                ]
            },
            "password": {
                "required": true,
                "tests": [
                    {
                        "type": "null",
                        "message": "Not entered password"
                    }
                ]
            }
        }
    });

    form.keypress(300);

    $('button[type="submit"]').click(function() {
        form.test();
        if (form.errors() == 0) {
            alert('Ok');
        }
        return false;
    });
</script>
@endsection
