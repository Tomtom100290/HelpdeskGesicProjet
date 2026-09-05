graph TB
    subgraph Local [" Machine de Développement (Local) "]
        subgraph DockerEngine [" Moteur Docker "]
            NGINX_DEV["«artifact»<br/>Nginx (Web)<br/>:8000"]
            PHP_DEV["«artifact»<br/>PHP-FPM 8.3<br/>(Symfony App)"]
            DB_DEV[("«artifact»<br/>MariaDB 11<br/>:3306")]
            MERCURE_DEV["«artifact»<br/>Mercure Hub<br/>:8443"]
            MAILPIT_DEV["«artifact»<br/>Mailpit (SMTP)<br/>:1025"]

            NGINX_DEV --> PHP_DEV
            PHP_DEV --> DB_DEV
            PHP_DEV --> MERCURE_DEV
            PHP_DEV --> MAILPIT_DEV
        end
    end

    subgraph CICD [" GitHub (Remote) "]
        subgraph Runner [" Runner GitHub Actions "]
            PHP_CI["PHP 8.3 CLI"]
            SQLITE_CI[("SQLite (In-Memory)")]
            PHPUNIT["Suite PHPUnit"]
            PHPSTAN["Analyse PHPStan"]

            PHP_CI --> PHPSTAN
            PHP_CI --> PHPUNIT
            PHPUNIT --> SQLITE_CI
        end
    end

    subgraph Prod [" Serveur VPS Hostinger (Production) "]
        subgraph Server [" OS Linux (Ubuntu) "]
            NGINX_PROD["«artifact»<br/>Nginx (Reverse Proxy)<br/>:80 / :443"]
            PHP_PROD["«artifact»<br/>PHP-FPM 8.3<br/>(Symfony App)"]
            DB_PROD[("«artifact»<br/>MariaDB Service<br/>:3306")]
            MERCURE_PROD["«artifact»<br/>Mercure Service"]

            NGINX_PROD --> PHP_PROD
            PHP_PROD --> DB_PROD
            PHP_PROD --> MERCURE_PROD
        end
    end

    %% Relations entre environnements
    Local -- "1. Git Push (dev/main)" --> CICD
    CICD -- "2. Déploiement auto (SSH / Webhook)" --> Prod