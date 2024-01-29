# Task_Managment


## Build and Run

The project is containerized using Docker and Docker Compose.
I explain how to build it and deploy it within the context of
compose. If you wish to build it from scratch I recommend you
look at the build recipe from `Dockerfile` to build it manually. 

### `.env` file:

Before you start you make sure that these 2 variables are provide a value in a `.env` file, also make sure that the file in same level as `docker-compose.yml`.

```ini

# db configurations
DB_USERNAME = your_db_name
MYSQL_ROOT_PASSWORD = your_db_password
```
