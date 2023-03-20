#Setup Instructions

###System Requirements
- PHP 8
- Symfony 6.2.7
- MySQL 5.7
- Node.js
- NPM
- Composer
- Web server: Apache

###Installation Steps
- run php bin\console doctrine:database:drop --force
- run php bin\console doctrine:database:create
- run php bin\console doctrine:schema:update --force
- run php bin\console doctrine:fixtures:load

###Project's integrations
- Token Generator for rest api calls
    src/Service/TokenGenerator.php

- Custom authenticator integration:
    src/Security/ApiKeyAuthenticator.php

- Rest api integration (based on ApiPlatform):
     
    curl -X 'POST' 
    'YOUR_SITE_URL/api/api_tokens' 
    -H 'accept: application/ld+json' 
    -H 'Content-Type: application/ld+json' 
    -d '{
    "email": "string",
    "password": "string"
    }'

    code implementation: src/Controller/ApiTokenController.php

    curl -X 'GET' 
    'YOUR_SITE_URL/api/notifications' 
    -H 'accept: application/ld+json'
    -H 'X-AUTH-TOKEN: MY_TOKEN' 

    curl -X 'POST' 
    'YOUR_SITE_URL/api/notifications' 
    -H 'accept: application/ld+json' 
    -H 'Content-Type: application/ld+json'
    -H 'X-AUTH-TOKEN: MY_TOKEN'
    -d '{
    "content": "string",
    "type": "string",
    "userId": 0,
    "isRead": false
    }'

    curl -X 'PATCH'
    'YOUR_SITE_URL/api/notifications/YOUR_NOTIFICATION_ID'
    -H 'accept: application/ld+json'
    -H 'Content-Type: application/merge-patch+json'
    -H 'X-AUTH-TOKEN: MY_TOKEN'
    -d '{
    "content": "string",
    "type": "string",
    "userId": 0,
    "isRead": true
    }'