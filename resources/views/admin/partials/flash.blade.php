@if(! View::hasSection('hide_admin_success_flash') && session('success'))
    <div class="flash flash-success">{{ session('success') }}</div>
@endif

@if(! View::hasSection('hide_admin_error_flash') && session('error'))
    <div class="flash flash-error">{{ session('error') }}</div>
@endif

@if(! View::hasSection('hide_admin_error_flash') && $errors->any())
    <div class="flash flash-error">
        <strong>Periksa kembali input berikut:</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
