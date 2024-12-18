<!DOCTYPE html>
<html lang="sr">
<head>
    <title><?php echo APP_VERSION ?></title>
    <?php include 'includes/pageHead.php'; ?>
</head>
<body class="bg-gradient-primary-red">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-5 col-lg-6 col-md-7 col-sm-10">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">

                            <div class="col-lg-12">
                                <div class="p-4">
                                    <?php include 'includes/alerts.php'; ?>
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Prijava u sistem 2024!</h1>
                                        <p><?php echo APP_VERSION . '©' . date('Y')?></p>
                                    </div>

                                    <form action=".inc/login.php" method="post" class="user">
                                        <div class="form-group">
                                            <input name="username" type="text" class="form-control form-control-user" id="exampleInputUsername" aria-describedby="emailHelp" placeholder="Unesite korisničko ime ..." required autofocus oninvalid="this.setCustomValidity('Morate upisati korisničko ime!')" oninput="setCustomValidity('')">
                                        </div>
                                        <div class="form-group">
                                            <input name="password" type="password" class="form-control form-control-user" id="exampleInputPassword" placeholder="Password" required value="" oninvalid="this.setCustomValidity('Morate upisati password!')" oninput="setCustomValidity('')">
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block p-2">
                                            Prijavi se
                                        </button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- page body footer -->
    <?php include 'includes/pageBodyFooter.php'; ?>

</body>
</html>
