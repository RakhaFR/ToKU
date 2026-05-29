<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="col-md-4">
            <div class="card shadow border-0 p-4">
                <div class="card-body">
                    <h3 class="text-center fw-bold mb-4">Login</h3>
                    
                    <form action="login_proses.php" method="POST">
                        <div class="mb-3">
                            <label for="user" class="form-label">User</label>
                            <input type="email" name="user" id="user" class="form-control" placeholder="Email@gmail.com" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="••••" required>
                        </div>
                        
                        <button type="submit" name="login" class="btn btn-primary w-100 py-2">Login</button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <a href="index.php" class="text-decoration-none small text-muted">&larr; Kembali ke Utama</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>