<!DOCTYPE html>
<html lang="en">
  <head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="Dorado Labs">
    <meta name="author" content="Dorado Labs">
    <meta name="keyword" content="">
    <title>{!! $title ?? 'Ingresar' !!} | {{ config('app.name', 'Laravel') }} G&Therefore;L&Therefore;S&Therefore;P&Therefore;</title>
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">
    <!-- Main styles for this application-->
    <link href="coreui/css/style.css" rel="stylesheet">
    <script>
      window.dataLayer = window.dataLayer || [];

      function gtag() {
        dataLayer.push(arguments);
      }
      gtag('js', new Date());
      // Shared ID
      gtag('config', 'UA-118965717-3');
      // Bootstrap ID
      gtag('config', 'UA-118965717-5');
    </script>
    <script>
      function miFuncion() {
        var response = grecaptcha.getResponse();

        if(response.length == 0){
          alert("Captcha no verificado")
        } else {
          alert("Captcha verificado");
        }
      }
    </script>
  </head>
  <body>
    <div class="bg-light min-vh-100 d-flex flex-row align-items-center" style="background-image: url(../bg-logia-2.jpg);background-size: 100%;" >
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-8">
            <div class="card-group d-block d-md-flex row">
              <div class="card col-md-7 p-4 mb-0 " style="opacity: 0.9">
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}" class="margin-bottom-0" target="_top">
                        @csrf
                  <h1>Ingreso</h1>
                  <p class="text-medium-emphasis">Datos de Usuario</p>
                  <div class="input-group mb-3"><span class="input-group-text">
                      <svg class="icon">
                        <use xlink:href="coreui/vendors/@coreui/icons/svg/free.svg#cil-user"></use>
                      </svg></span>

                    <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus>
                    @error('username')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                  </div>
                  <div class="input-group mb-4">
                    <span class="input-group-text">
                      <svg class="icon">
                        <use xlink:href="coreui/vendors/@coreui/icons/svg/free.svg#cil-lock-locked"></use>
                      </svg>
                    </span>
                      <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                      @error('password')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                  </div>
                  <div class="input-group mb-3">
                    {{-- <label for="capatcha">Codigo Captcha de Seguridad</label> --}}
                    <div class="captcha">
                      <span>{!! app('captcha')->display() !!}</span>
                      <!-- <button type="button" class="btn btn-success refresh-cpatcha"><i class="fa fa-refresh"></i></button> -->
                    </div>

                    @error('g-recaptcha-response')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="row">
                    <div class="col-6">
                      <button class="btn btn-primary px-4" type="submit">Ingresar</button>
                    </div>
                    <div class="col-6 text-end">
                      {{-- <button class="btn btn-link px-0" type="button">Pedio su clave?</button> --}}
                    </div>
                  </div>
                </div>
            </form>
              </div>
              <div class="card col-md-5 text-white bg-dark py-5" style="opacity: 0.9">
                <div class="card-body text-center">
                  <div>
                    <h2><img src="{{asset('glsp200.png')}}" width="100"><br><br>SIGEMA</h2>
                        <p></p>
                    <h1>G&Therefore;L&Therefore;S&Therefore;P&Therefore;</h1>
                    {{-- <button class="btn btn-lg btn-outline-light mt-3" type="button">Register Now!</button> --}}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- CoreUI and necessary plugins-->
    <script src="coreui/vendors/@coreui/coreui/js/coreui.bundle.min.js"></script>
    <script>
    </script>

  </body>
</html>
