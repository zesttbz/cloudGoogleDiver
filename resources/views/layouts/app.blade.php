<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    

    <link href="/favicon.ico" rel="icon" type="image/x-icon" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="/fontawesome/css/all.min.css">

    @yield('css')
    <style>
        img.avatar {
            width: 25px;
            height: 25px;
            border-radius: 100%;
            border: 1px solid #d7d7d7;
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-cloud-upload-alt"></i>
                <span>{{ env('APP_NAME_FULL') }}</span>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                    @if (Auth::check())
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard">
                            <i class="fas fa-tachometer-alt mr-1"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard/upload">
                            <i class="fas fa-upload mr-1"></i>
                            <span>Upload</span>
                        </a>
                    </li>
                    <li class="nav-item dropdown ml-2">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="avatar" src="{{ Auth::user()->image ?: '/images/avatar.png' }}">
                            <span>{{ Auth::user()->name }}</span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                          	<a class="dropdown-item" href="/dashboard/profile">Account</a>
                            <a class="dropdown-item" href="/logout">Log out</a>
                        </div>
                    </li>
                    @else
                    <li class="nav-item">
                        <span style="cursor: pointer" class="nav-link" data-toggle="modal"
                            data-target="#modal-login">Log in</span>
                    </li>
                    @endif

                    {{-- <li class="nav-item dropdown ml-2">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-globe-asia mr-1"></i>
                            <span>Tiếng Việt</span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="#">Tiếng Việt</a>
                            <a class="dropdown-item" href="#">English</a>
                        </div>
                    </li> --}}
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')

    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h2>Unlimited storage</h2>
                <p>Files are permanently stored</p>
                <p class="text-danger">Maximum upload size 10Gb per file</p>
            </div>
            <div class="col-md-4">
                <h2>Unlimited bandwidth</h2>
                <p>Unlimited download speed</p>
                <p>Upload - Download - Share File</p>
            </div>
            <div class="col-md-4">
                <h2>Contact</h2>
                <p>Phone: {{ env('APP_PHONE') }}</p>
                <p>Mail: <a href="mailto:{{ env('APP_MAIL') }}">{{ env('APP_MAIL') }}</a></p>
            </div>
        </div>
        <hr>
        <footer class="text-center">
            <div>© 2020 - <?php echo date("Y"); ?> {{ env('APP_NAME_FULL') }}</div>
            <p><a href="mailto:"></a></p>
        </footer>
    </div>

    <div class="modal fade" id="modal-login" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <h4 class="mb-3">Log in</h4>
                    <form action="/login" method="post">
                        {!! csrf_field() !!}
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" required name="email">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" required name="password">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary btn-block">
                                Log in
                            </button>
                        </div>
                        <div class="form-group">
                            <span>Do not have an account?</span>
                            <span style="color:#007bff; cursor: pointer" data-dismiss="modal" data-toggle="modal"
                                data-target="#modal-register">
                                Create an account here
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-register" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <h4 class="mb-3">Registration</h4>
                    <form action="/register" method="post">
                        {!! csrf_field() !!}
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" required name="name">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" required name="email">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" required name="password">
                        </div>
                        <div class="form-group">
                            <label>Confirm password</label>
                            <input type="password" class="form-control" required name="password_confirmation">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary btn-block">
                                Sign up
                            </button>
                        </div>
                        <div class="form-group">
                            <span>
                                Do you already have an account?
                            </span>
                            <span style="color:#007bff; cursor: pointer" data-dismiss="modal" data-toggle="modal"
                                data-target="#modal-login">
                                Log in
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

@yield('js')

</html>