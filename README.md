# Welcome to Yetinder!

## How to start
Whole process is merged into a /run.sh script which can be run by
```bash
sh run.sh
```
## Manual Steps
1. **Install Composer dependencies:**
    ```bash
    composer install
    ```

2. **Start Docker:**
    ```bash
    docker compose up
    ```

3. **Build webpack:**
    ```bash
    npm run build
    ```

4. **Run the migrations:**
    ```bash
    docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
    ```

5. **Load fixtures:**
    ```bash
    docker compose exec php php bin/console doctrine:fixtures:load --no-interaction
    ```

6. **Open your browser and go to:**
    ```
    http://localhost:8080
    ```
