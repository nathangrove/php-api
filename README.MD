# A Simple PHP API Framework
This is a simple PHP API framework I wrote one weekend. I had 3 goals while building this.
1. Build it fast
2. Lay the groundwork for something practical and simple
3. Have fun

## Deployment Structure
I've included a docker-compose file that will deploy it for development purposes. You will have to determine who you would want to deploy it for production. Please do not use the included docker-compose file.

## File Structure
```
api/ 
  |- composer.json                
  |- tests/                       # Store the unit tests
  |- src/                         # Stores all API source code
  |   |- index.php                # All requests route here
  |   |- ControllerSuper.php      # The super class for all route controllers
  |   |- controllers/             # Stores route controllers
  |   |   |- TodoController.php   # A sample controller
sql/                              # Stores SQL to build the tables
  |- user.sql                     # The SQL to generate the user table
```
## Authentication
There is a rudementary JWT system implemented. It will generate tokens when a POST request is made to `/api/login`. If authentication is not required for a specific route you can add a variable to the controller class `var $skip_auth = true`