<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Authentication') ?> | Awardee Performance Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --ugc-green: #006B3C;
            --ugc-green-dark: #004D2B;
            --ugc-red: #D71920;
            --ugc-black: #111111;
            --ugc-gray: #8A8F98;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--ugc-green-dark), var(--ugc-green));
        }

        .auth-card {
            border: 0;
            border-radius: 14px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.25);
        }

        .brand-strip {
            height: 6px;
            border-radius: 14px 14px 0 0;
            background: linear-gradient(90deg, var(--ugc-red) 0%, var(--ugc-red) 20%, var(--ugc-green) 100%);
        }

        .btn-ugc {
            background-color: var(--ugc-green);
            border-color: var(--ugc-green);
            color: #fff;
        }

        .btn-ugc:hover {
            background-color: var(--ugc-green-dark);
            border-color: var(--ugc-green-dark);
            color: #fff;
        }

        .ugc-link {
            color: var(--ugc-green);
            text-decoration: none;
        }

        .ugc-link:hover {
            color: var(--ugc-green-dark);
            text-decoration: underline;
        }

        .ugc-footer {
            color: #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-12 col-md-8 col-lg-5">
                <div class="card auth-card">
                    <div class="brand-strip"></div>
                    <div class="card-body p-4 p-md-5">
                        <?= $this->renderSection('content') ?>
                    </div>
                </div>
                <p class="text-center ugc-footer mt-3 mb-0 small">
                    © <?= date('Y') ?> Awardee Performance Management
                </p>
            </div>
        </div>
    </div>
</body>
</html>
